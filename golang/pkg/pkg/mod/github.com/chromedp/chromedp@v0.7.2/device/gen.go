// +build ignore

package main

import (
	"bytes"
	"encoding/json"
	"errors"
	"flag"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"os/exec"
	"regexp"
	"strings"
)

const deviceDescriptorsURL = "https://raw.githubusercontent.com/puppeteer/puppeteer/main/src/common/DeviceDescriptors.ts"

var flagOut = flag.String("out", "device.go", "out")

func main() {
	flag.Parse()
	if err := run(); err != nil {
		log.Fatal(err)
	}
}

type deviceDescriptor struct {
	Name      string `json:"name"`
	UserAgent string `json:"userAgent"`
	Viewport  struct {
		Width             int64   `json:"width"`
		Height            int64   `json:"height"`
		DeviceScaleFactor float64 `json:"deviceScaleFactor"`
		IsMobile          bool    `json:"isMobile"`
		HasTouch          bool    `json:"hasTouch"`
		IsLandscape       bool    `json:"isLandscape"`
	}
}

var cleanRE = regexp.MustCompile(`[^a-zA-Z0-9_]`)

// run runs the program.
func run() error {
	var descriptors []deviceDescriptor
	if err := get(&descriptors); err != nil {
		return err
	}

	// add reset device
	descriptors = append([]deviceDescriptor{{}}, descriptors...)

	buf := new(bytes.Buffer)
	fmt.Fprintf(buf, hdr, deviceDescriptorsURL)
	fmt.Fprintln(buf, "\n// Devices.")
	fmt.Fprintln(buf, "const (")
	for i, d := range descriptors {
		if i == 0 {
			fmt.Fprintln(buf, "// Reset is the reset device.")
			fmt.Fprintln(buf, "Reset infoType = iota\n")
		} else {
			name := cleanRE.ReplaceAllString(d.Name, "")
			name = strings.ToUpper(name[0:1]) + name[1:]
			fmt.Fprintf(buf, "// %s is the %q device.\n", name, d.Name)
			fmt.Fprintf(buf, "%s\n\n", name)
		}
	}
	fmt.Fprintln(buf, ")\n")

	fmt.Fprintln(buf, "// devices is the list of devices.")
	fmt.Fprintln(buf, "var devices = [...]Info{")
	for _, d := range descriptors {
		fmt.Fprintf(buf, "{%q, %q, %d, %d, %f, %t, %t, %t},\n",
			d.Name, d.UserAgent,
			d.Viewport.Width, d.Viewport.Height, d.Viewport.DeviceScaleFactor,
			d.Viewport.IsLandscape, d.Viewport.IsMobile, d.Viewport.HasTouch,
		)
	}
	fmt.Fprintln(buf, "}")

	// write and  format
	if err := ioutil.WriteFile(*flagOut, buf.Bytes(), 0644); err != nil {
		return err
	}
	return exec.Command("gofmt", "-w", "-s", *flagOut).Run()
}

var (
	startRE        = regexp.MustCompile(`(?m)^const\s+devices:\s*Device\[\]\s*=\s*\[`)
	endRE          = regexp.MustCompile(`(?m)^\];`)
	fixLandscapeRE = regexp.MustCompile(`isLandscape:\s*(true|false),`)
	fixKeysRE      = regexp.MustCompile(`(?m)^(\s+)([a-zA-Z]+):`)
	fixClosesRE    = regexp.MustCompile(`([\]\}]),\n(\s*[\]\}])`)
)

// get retrieves and decodes the device descriptors.
func get(v interface{}) error {
	req, err := http.NewRequest("GET", deviceDescriptorsURL, nil)
	if err != nil {
		return err
	}

	// retrieve
	cl := &http.Client{}
	res, err := cl.Do(req)
	if err != nil {
		return err
	}
	defer res.Body.Close()
	if res.StatusCode != 200 {
		return fmt.Errorf("got status code %d", res.StatusCode)
	}

	buf, err := ioutil.ReadAll(res.Body)
	if err != nil {
		return err
	}

	start := startRE.FindIndex(buf)
	if start == nil {
		return errors.New("could not find start")
	}
	buf = buf[start[1]-1:]

	end := endRE.FindIndex(buf)
	if end == nil {
		return errors.New("could not find end")
	}
	buf = buf[:end[1]-1]
	buf = bytes.Replace(buf, []byte("'"), []byte(`"`), -1)
	buf = fixLandscapeRE.ReplaceAll(buf, []byte(`"isLandscape": $1`))
	buf = fixKeysRE.ReplaceAll(buf, []byte(`$1"$2":`))
	buf = fixClosesRE.ReplaceAll(buf, []byte("$1\n$2"))
	buf = fixClosesRE.ReplaceAll(buf, []byte("$1\n$2"))

	return json.Unmarshal(buf, v)
}

const hdr = `package device

// See: %s

// Generated by gen.go. DO NOT EDIT.

`
