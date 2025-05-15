# Use the official PHP image with built-in web server
FROM php:8.2-cli
WORKDIR /var/www/html
COPY . .
EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]
