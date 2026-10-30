FROM php:8.4-apache-bookworm

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers \
    && sed -ri 's/^Listen 80$/Listen 10000/' /etc/apache2/ports.conf \
    && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf \
    && echo 'ServerName localhost' >> /etc/apache2/apache2.conf

RUN printf '%s\n' \
    '<DirectoryMatch "^/var/www/html/(config|database|\.git|\.github)(/|$)">' \
    '  Require all denied' \
    '</DirectoryMatch>' \
    '<FilesMatch "^(\.env.*|Dockerfile|Caddyfile|README\.md|railway\.json|\.gitignore)$">' \
    '  Require all denied' \
    '</FilesMatch>' \
    > /etc/apache2/conf-available/ms-barbearia-security.conf \
    && a2enconf ms-barbearia-security

COPY . /var/www/html/
WORKDIR /var/www/html

EXPOSE 10000
CMD ["apache2-foreground"]
