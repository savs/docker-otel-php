# Use the official PHP image with built-in web server
FROM php:8.2-cli

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html
COPY . .
EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080"]
