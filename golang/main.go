package main

import (
	"bufio"
	"fmt"
	"github.com/geziyor/geziyor"
	"github.com/geziyor/geziyor/client"
	"log"
	"os"
)

func main() {
	//userAgent()
	boost()
}

func boost() {
	geziyor.NewGeziyor(&geziyor.Options{
		StartRequestsFunc: func(g *geziyor.Geziyor) {
			g.GetRendered("https://ngs.ru/text/health/2021/06/25/69988499/comments/", g.Opt.ParseFunc)
		},
		ParseHTMLDisabled: false,
		ParseFunc: func(g *geziyor.Geziyor, r *client.Response) {
			//fmt.Println(string(r.Body))
			file, _ := os.Create("./asdasd.html")
			//file, _ := os.Open("./asdasd.html")
			file.Write(r.Body)
			file.Close()
		},
		//RequestDelayRandomize: true,
		//UserAgent: userAgent,
	}).Start()


}

func userAgent(){
	file, err := os.Open("./useragents.txt")
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()

	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		userAgent := scanner.Text()
		fmt.Println(userAgent)
		//go boost(userAgent)
	}

	if err := scanner.Err(); err != nil {
		log.Fatal(err)
	}
}
