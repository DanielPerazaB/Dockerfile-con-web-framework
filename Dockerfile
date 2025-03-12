#imagen oficial de PHP con Apache
FROM php:8.2-apache

#extensiones necesarias Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

#composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache

#configuracion de Apache para que use el directorio public/ de Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

#expone el puerto 80
EXPOSE 80

#Inicia Apache
CMD ["apache2-foreground"]
