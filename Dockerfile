FROM php:8.2-apache

RUN a2enmod rewrite

COPY . /var/www/html/

RUN { \
    echo '<FilesMatch "\.html$">'; \
    echo '  SetHandler application/x-httpd-php'; \
    echo '</FilesMatch>'; \
} >> /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www/html/imgBD && \
    chmod -R 755 /var/www/html/imgBD

RUN docker-php-ext-install pdo pdo_mysql mysqli && \
    docker-php-ext-enable pdo pdo_mysql mysqli

EXPOSE 80

CMD ["apache2-foreground"]
