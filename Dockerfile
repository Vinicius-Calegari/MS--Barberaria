FROM dunglas/frankenphp:php8.4

RUN install-php-extensions pdo_mysql

WORKDIR /app
COPY . /app
COPY Caddyfile /etc/frankenphp/Caddyfile

ENV SERVER_NAME=:8080
EXPOSE 8080
