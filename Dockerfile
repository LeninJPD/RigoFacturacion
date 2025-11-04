FROM php:8.2-cli
WORKDIR /app
COPY . .

# Instalar extensión de PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pgsql pdo_pgsql
# Copiar el proyecto
COPY . /app
WORKDIR /app
# Exponer puerto y ejecutar PHP
EXPOSE 8080
CMD php -S 0.0.0.0:${PORT} -t .

EXPOSE 10000
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
