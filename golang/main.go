package main

import (
    "github.com/KaoriEl/liveinternet/pkg/operations"
)

/**
Запускает коннект к ребиту, тамж получаются сообщения.
Все работает отталкиваясь от сообщений рэббита
 */
func main() {
    operations.RabbitGetMsg()
}


