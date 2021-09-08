package operations

import (
    "bufio"
    "encoding/json"
    "fmt"
    "github.com/geziyor/geziyor"
    "github.com/geziyor/geziyor/client"
    "log"
    "math/rand"
    "os"
    "strconv"
    "sync"
    "time"
)

type Proxy struct {
    ProxyAddress string `json:"proxy_address"`
    ProxyPort string `json:"proxy_port"`
}

func inWrapp(TaskList Task) {
   var SiteUrl = TaskList.SiteUrl
   var needWrapping = TaskList.CountWrapping
   var proxyList = TaskList.Proxy
   log.Println(SiteUrl)
   log.Println(needWrapping)
   log.Println(proxyList)

   var ProxyList []Proxy

   er := json.Unmarshal([]byte(proxyList), &ProxyList)
   if er != nil {
       ResponseStatusError(TaskList)
       panic(er)
   } else {
       fmt.Println(ProxyList)
   }

   go boost(ProxyList, needWrapping, SiteUrl, TaskList)
}


func boost(ProxyList []Proxy, needWrapping string, SiteUrl string, TaskList Task) {
    if needWrapping, err := strconv.Atoi(needWrapping); err == nil {
        fmt.Println(needWrapping)
        var pump []string
        rawArray := userAgent(TaskList)
        rand.Seed(time.Now().UnixNano())
        for i := 0; i < needWrapping; i++ {
            pick := rawArray[rand.Intn(len(rawArray))]
            pump = append(pump, pick)
        }
        var wg sync.WaitGroup

        for i := 0; i < len(pump); i++ {
            userAgent := pump[i]
            ProxyAddress := ProxyList[i].ProxyAddress
            ProxyPort := ProxyList[i].ProxyPort
            fmt.Println(ProxyAddress)
            fmt.Println(ProxyPort)
            // Увеличиваем WaitGroup счетчик.
            wg.Add(1)
            go func() {
                defer wg.Done()
                geziyor.NewGeziyor(&geziyor.Options{
                    StartRequestsFunc: func(g *geziyor.Geziyor) {
                        g.GetRendered(SiteUrl, g.Opt.ParseFunc)
                    },

                    ParseHTMLDisabled: true,
                    ParseFunc: func(g *geziyor.Geziyor, r *client.Response) {
                        file, _ := os.Create("./asdasd.html")
                        file.Write(r.Body)
                        file.Close()
                    },
                    UserAgent: userAgent,
                    RobotsTxtDisabled: true,
                    ProxyAdress: ProxyAddress,
                    ProxyPort: ProxyPort,
                    RandomSleep: true,
                    //ProxyLogin: "Selsagem3455",
                    //ProxyPassword: "F9g3SnG",
                }).Start()

            }()
        }
        wg.Wait()
        fmt.Println("Мы не жалкие букашки, суперниндзя черепашки!")
    } else {
        fmt.Println(needWrapping, "не является целым числом.")
    }
    ResponseStatusStop(TaskList)
}

func userAgent(TaskList Task) []string{
    var agent []string
    file, err := os.Open("./useragents.txt")
    if err != nil {
        log.Fatal(err)
    }
    defer file.Close()

    scanner := bufio.NewScanner(file)
    for scanner.Scan() {
        userAgent := scanner.Text()
        agent = append(agent, userAgent)
    }
    if err := scanner.Err(); err != nil {
        ResponseStatusError(TaskList)
        log.Fatal(err)
    }
    return agent
}
