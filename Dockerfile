FROM php:8.0-apache
RUN a2enmod rewrite
RUN service apache2 restart
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
EXPOSE 80