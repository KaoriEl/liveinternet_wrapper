FROM ubuntu:16.04
FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    cron \
    vim \
    htop \
    zip \
    unzip \
    procps\
    && docker-php-ext-install zip

RUN \
  apt-get update && \
  apt-get install -y software-properties-common git curl supervisor unzip


COPY ./phpSupervisord/supervisord.conf /etc/supervisord.conf
# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath mysqli sockets

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www
ENV PHP_MEMORY_LIMIT=-1
RUN chown -R www-data:www-data /var/www/

RUN echo "*/5 * * * * root php artisan proxy:check >> /var/log/cron.log 2>&1" >> /etc/crontab

CMD ["/usr/bin/supervisord", "-n"]
#CMD ["/usr/sbin/cron", "-f"]
# USER $user
