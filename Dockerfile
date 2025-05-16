# Use the official PHP image with built-in web server
FROM php:8.2-cli AS base

# Install dependencies: git, unzip, and PHP zip extension
RUN apt-get update \
    && apt-get install -y git unzip libzip-dev \
    && pecl install opentelemetry grpc \
    && docker-php-ext-enable opentelemetry \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM base AS app
WORKDIR /var/www/html
COPY . .

# Install OpenTelemetry SDK
RUN composer init \
    --no-interaction \
    --require slim/slim:"^4" \
    --require slim/psr7:"^1"
RUN composer update

FROM app AS composer
RUN composer config allow-plugins.php-http/discovery false
RUN composer require \
  open-telemetry/sdk \
  open-telemetry/opentelemetry-auto-slim \
  open-telemetry/exporter-otlp \
  php-http/guzzle7-adapter \
  monolog/monolog \
  open-telemetry/opentelemetry-logger-monolog
RUN composer update

FROM composer AS final
ENV OTEL_PHP_AUTOLOAD_ENABLED=true
ENV OTEL_TRACES_EXPORTER=otlp
ENV OTEL_METRICS_EXPORTER=otlp
ENV OTEL_LOGS_EXPORTER=otlp
ENV OTEL_EXPORTER_OTLP_INSECURE=true
ENV OTEL_RESOURCE_ATTRIBUTES=deployment.environment=development,service.name=phptest,service.namespace=demo,service.instance.id=98606,demo=andrew
ENV OTEL_EXPORTER_OTLP_ENDPOINT=http://alloy:4318
ENV OTEL_EXPORTER_OTLP_PROTOCOL=http/protobuf

EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080"]
