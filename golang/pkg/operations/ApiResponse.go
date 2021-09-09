package operations

import (
    "encoding/base64"
    "github.com/joho/godotenv"
    "log"
    "net/http"
    "net/url"
    "os"
    "strconv"
    "strings"
)

func init() {
    err := godotenv.Load()
    if err != nil {
        log.Fatal("Error loading .env file")
    }
}


func server() {
	log.Println("Server start!")
	err := http.ListenAndServe(":3000", nil)
	if err != nil {
		return
	}
}

// basicAuth
//Создает пару логин пароль для nginx auth
///**
func basicAuth(username, password string) string {
    auth := username + ":" + password
    return base64.StdEncoding.EncodeToString([]byte(auth))
}

// ResponseStatusSuccessfully
//Отправка статуса в пхп о том что задача взята в работу
///**
func ResponseStatusSuccessfully(TaskList Task) {
    var req *http.Request
    var username = os.Getenv("AUTH_NGINX_USERNAME")
    var passwd = os.Getenv("AUTH_NGINX_PASSWORD")
    log.Println(username)
    log.Println(passwd)
    formData := url.Values{
        "url": {TaskList.SiteUrl},
        "status":       {"START"},
    }
    log.Println(basicAuth(username, passwd))
    client := &http.Client{}
    req, _ = http.NewRequest("POST", "http://nginx:80/api/url_status", strings.NewReader(formData.Encode()))
    req.Header.Add("Content-Type", "application/x-www-form-urlencoded")
    req.Header.Add("Content-Length", strconv.Itoa(len(formData.Encode())))
    req.Header.Add("Authorization", "Basic "+basicAuth(username, passwd))
    resp, err := client.Do(req)
    log.Println(resp)
    log.Println(err)

}

// ResponseStatusStop
//Отправка статуса в пхп о том что задача окончена в работу
///**
func ResponseStatusStop(TaskList Task) {
    var req *http.Request
    var username = os.Getenv("AUTH_NGINX_USERNAME")
    var passwd = os.Getenv("AUTH_NGINX_PASSWORD")
    log.Println(username)
    log.Println(passwd)
    formData := url.Values{
        "url": {TaskList.SiteUrl},
        "status":       {"STOP"},
    }
    log.Println(basicAuth(username, passwd))
    client := &http.Client{}
    req, _ = http.NewRequest("POST", "http://nginx:80/api/url_status", strings.NewReader(formData.Encode()))
    req.Header.Add("Content-Type", "application/x-www-form-urlencoded")
    req.Header.Add("Content-Length", strconv.Itoa(len(formData.Encode())))
    req.Header.Add("Authorization", "Basic "+basicAuth(username, passwd))
    resp, err := client.Do(req)
    log.Println(resp)
    log.Println(err)

}

// ResponseStatusError
//Отправка статуса в пхп о том что задача завершилась с ошибкой
///**
func ResponseStatusError(TaskList Task) {
    var req *http.Request
    var username = os.Getenv("AUTH_NGINX_USERNAME")
    var passwd = os.Getenv("AUTH_NGINX_PASSWORD")
    log.Println(username)
    log.Println(passwd)
    formData := url.Values{
        "url": {TaskList.SiteUrl},
        "status":       {"ERR"},
    }
    log.Println(basicAuth(username, passwd))
    client := &http.Client{}
    req, _ = http.NewRequest("POST", "http://nginx:80/api/url_status", strings.NewReader(formData.Encode()))
    req.Header.Add("Content-Type", "application/x-www-form-urlencoded")
    req.Header.Add("Content-Length", strconv.Itoa(len(formData.Encode())))
    req.Header.Add("Authorization", "Basic "+basicAuth(username, passwd))
    resp, err := client.Do(req)
    log.Println(resp)
    log.Println(err)

}
