FROM php:8.3-fpm-alpine

# Update, upgrade and install some things
RUN apk update && \
    apk upgrade && \
    apk add --no-cache \
        nano \
        ncdu \
        libzip-dev \
        zip


# Install some stuff
RUN docker-php-ext-install \
    zip

# Clean up
RUN rm -rf /var/cache/apk/*

#COPY --chown=www-data:www-data ./src/ /var/www/html/
#RUN chmod -R 755 /var/www/html

# WORKDIR /var/www/
