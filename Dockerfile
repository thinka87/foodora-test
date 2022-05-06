FROM tonyyb/laravel-php:7.4

WORKDIR /app
COPY . /app
RUN composer install

CMD php artisan serve --host=0.0.0.0 --port=8000
EXPOSE 8000