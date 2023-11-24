# Проект SkillBox Symfony
### Как развернуть проект
1. Скачать Git репозиторий на ваш сервер (локальный сервер вашего ПК) или хостинг
2. Убедится, что на хостиге (локальном сервере) установлена версия PHP 8.1
3. Проверить, что на хостиге (локальном сервере) установлены следующие модули PHP: php-xml, php-curl, php8.1-gd, php-zip
4. Убедится, что на хостиге (локальном сервере) установлена Node версии 16-18
5. Установить зависимости composer
```composer install```
6. Установить NPM зависимости
```npm i```
7. Создать файл .env из env.example.txt
8. Сгенерировать и прописать параметр APP_SECRET
```php bin/console make:command regenerate-app-secret```
9. Прописать адрес хоста
```SITE_BASE_SCHEME=  SITE_BASE_HOST=```
10. В окружении задать параметры базы данных
```SDATABASE_URL=```
11. Если настроен DNS для отправки писем, задать его параметры, а параметр USER_REG_CHECK_EMAIL установить в 1, что включит функцию подтверждения email при регистрации
```MAILER_DSN=``` и ```USER_REG_CHECK_EMAIL=1```
12. Создать базу данных:
```php bin/console doctrine:database:create```
13. Создать миграции:
```php bin/console make:migration```
14. Создать структуру базы данных: 
```php bin/console doctrine:migrations:migrate```
15. Загрузить, по желанию, демо данные:
```php bin/console doctrine:fixtures:load```
16. Запустить сборку frontend скриптов
```npm run dev``` или ```npm run build```
17. Перевести проект в режим Production для этого в .env задать параметр APP_ENV=prod
18. Очистить cache проекта:
```php bin/console cache:clear```

