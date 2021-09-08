# Накрутчик для Live internet

## Как развернуть проект?
1. Создать vps, скофигурировать и установить на неё докер.
2. Выполнить комманды ниже в той же последовательности:
```
docker-compose build
docker-compose up -d
docker exec -it laravel composer i
```
3. Подключиться в контейнеру laravel и выполнить команды ниже:
```
docker exec -it laravel sh //подключение
cd ..
chown www-data:www-data -R www
```
4. Выйти из контейнера и выполнить команды ниже:
```
docker exec -it mysql-db sh //подключение
cd /var/lib/mysql
chown -R mysql:mysql mysql
```
5. Выйти из контейнера и выполнить команды ниже:
```
docker exec -it laravel php key:generate 
docker exec -it laravel php artisan migrate 
docker exec -it laravel php artisan db:seed
docker exec -it laravel php artisan create:queue
```
6. Все должно работать.

## Команды которые могут понадобиться

* Если тебе нужно остановить все докер контейнеры, то можешь воспользоваться данной командой

```
docker stop $(docker ps -a -q)
```

* Если тебе нужно удалить все докер контейнеры, то можешь воспользоваться данной командой, но перед этим нужно их остановить.

```
docker rm $(docker ps -a -q)
```
