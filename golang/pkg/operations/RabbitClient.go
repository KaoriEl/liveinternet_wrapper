package operations

import (
    "encoding/json"
    "github.com/joho/godotenv"
    "github.com/streadway/amqp"
    "log"
    "os"
)

type Task struct {
    SiteUrl         string `json:"site_url"`
    CountWrapping   string `json:"count_wrapp"`
    Proxy           string `json:"proxyList"`

}

/**
Это подключает ENV
 */
func init() {
    err := godotenv.Load()
    if err != nil {
        log.Fatal("Error loading .env file")
    }
}

/**
Обработчик ерроров рэббита
*/
func failOnError(err error, msg string) {
    if err != nil {
        log.Fatalf("%s: %s", msg, err)
    }
}

// RabbitGetMsg
//Крч тут в новой рутине запускается сервер для отправки статусов
//Далее подключение и прослушивание канала очереди рэббита
//Запуск воркера
///**
func RabbitGetMsg() string{
    go server()
    conn, err := amqp.Dial("amqp://" + os.Getenv("USERNAME_RABBIT_MQ") + ":" + os.Getenv("PASSWORD_RABBIT_MQ") + "@rabbitmq:5672/")
    failOnError(err, "Failed to connect to RabbitMQ")
    defer conn.Close()

    ch, err := conn.Channel()
    failOnError(err, "Failed to open a channel")
    defer ch.Close()

    q, err := ch.QueueDeclare(
        "Queue_wrapper", // name
        false,   // durable
        false,   // delete when unused
        false,   // exclusive
        false,   // no-wait
        nil,     // arguments
    )

    failOnError(err, "Failed to declare a queue")


    msgs, err := ch.Consume(
        q.Name, // queue
        "",     // consumer
        true,   // auto-ack
        false,  // exclusive
        false,  // no-local
        false,  // no-wait
        nil,    // args
    )

    forever := make(chan bool)

        for d := range msgs {
            log.Println("----------------New-Msg---------------")
            log.Println("Received a message: %s", d.Body)
            log.Println("--------------------------------------")
            Worker(d.Body)

        }

    log.Println(" [*] Waiting for messages. [*]")
    <-forever

    return "ok"
}

// Worker
//Отсылает статус взята ли задача в работу
//Если взял в работу запускает обертку в накрутку
///**
func Worker(msg []byte){
    var TaskList Task
    er := json.Unmarshal(msg, &TaskList)
    if er != nil {
        ResponseStatusError(TaskList)
        panic(er)
    } else {
        log.Println(TaskList)
        ResponseStatusSuccessfully(TaskList)
        inWrapp(TaskList)
    }

}
