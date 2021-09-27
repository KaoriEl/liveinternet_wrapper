package operations

import (
    "bufio"
    _ "encoding/json"
    "fmt"
    "github.com/geziyor/geziyor"
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

// inWrapp
//Получение необходимых данных для накрутки и занесение их в структуру
//Вызов функции накрутки
///**
func inWrapp(TaskList Task) {
   var SiteUrl = TaskList.SiteUrl
   var needWrapping = TaskList.CountWrapping
   //var proxyList = TaskList.Proxy
   //var ProxyList []Proxy

   //er := json.Unmarshal([]byte(proxyList), &ProxyList)
   //if er != nil {
   //    ResponseStatusError(TaskList)
   //    panic(er)
   //} else {
   //    //fmt.Println(ProxyList)
   //}

   boost(needWrapping, SiteUrl, TaskList)
}


// boost
//Та самая накрутка, создает кучу браузеров в разных рутинах
///**
func boost(needWrapping string, SiteUrl string, TaskList Task) {
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
            wg.Add(1)
            go func() {
                defer wg.Done()
                geziyor.NewGeziyor(&geziyor.Options{
                   StartRequestsFunc: func(g *geziyor.Geziyor) {
                       g.GetRendered(SiteUrl, g.Opt.ParseFunc)
                   },

                   ParseHTMLDisabled: true,
                   //ParseFunc: func(g *geziyor.Geziyor, r *client.Response) {
                   //    file, _ := os.Create("./asdasd.html")
                   //    file.Write(r.Body)
                   //    file.Close()
                   //},
                   UserAgent: userAgent,
                   RobotsTxtDisabled: true,
                   RandomSleep: true,
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


// boost
//Парсю юзерагенты из txt файлика
///**
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
