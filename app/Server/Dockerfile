FROM php:8.3

WORKDIR /usr/src

COPY ./app/Server/ ./app/Server/
COPY ./vendor/ ./vendor/

EXPOSE 8000

RUN docker-php-ext-install pdo pdo_mysql

CMD ["php", "-S", "0.0.0.0:8000", "./app/Server/LocalServer.php"]
