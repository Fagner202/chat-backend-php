# Use a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Habilite os módulos necessários do Apache
RUN a2enmod rewrite

# Instale as dependências necessárias para o PHP e MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    curl \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Instala PHPUnit
RUN composer global require phpunit/phpunit --prefer-dist

# Adiciona o caminho dos binários globais do Composer ao PATH
ENV PATH="/root/.composer/vendor/bin:${PATH}"

# Defina o diretório de trabalho para o Apache
WORKDIR /var/www/html

# Copie o arquivo composer.json e composer.lock para o diretório de trabalho
COPY composer.json composer.lock /var/www/html/

# Instala as dependências do Composer
RUN composer install --no-interaction --prefer-dist

# Copie o código da aplicação PHP para o container
COPY src/ /var/www/html/

# Ajusta permissões para evitar problemas com volumes (opcional)
RUN chown -R www-data:www-data /var/www/html

# Exponha a porta 80 para acessar o servidor Apache
EXPOSE 80