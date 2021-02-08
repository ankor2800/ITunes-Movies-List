# iTunes Movie

Тестовый проект с использованием **Slim Framework**, компонентов **Symfony** и шаблонизатора **Twig**.

Главная страница предствляет собой список последних 10 трейлеров, каждый элемент состоит из заголовка трейлера и его постера.

Заголовок является ссылкой на делальную страницу, которая имеет помимо заголовка и постера еще описание и ссылку на [iTunes Movie Trailers](https://trailers.apple.com).

Записи рагружаются из [RSS Feed](https://trailers.apple.com/trailers/home/rss/newtrailers.rss) сервиса с помощью консольной команды.

## Development

```
docker-compose up -d

docker exec app composer install --dev
```

Встроенный web server будет автоматически запущен при запуске контейнера.

**localhost**: http://0.0.0.0:8080

## Database

Инициализация БД:
```
php bin/console orm:schema-tool:update
```

Импорт в БД 10 последних записей из [iTunes Movie Trailers](https://trailers.apple.com) 

```
php bin/console fetch:trailers
```

---