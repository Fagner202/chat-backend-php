# Use a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Habilite os módulos necessários do Apache e PHP
RUN a2enmod rewrite

# Instale as dependências necessárias para o PHP e MySQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Defina o diretório de trabalho para o Apache
WORKDIR /var/www/html

# Copie o código da aplicação PHP para o container
COPY src/ /var/www/html/

# Exponha a porta 80 para acessar o servidor Apache
EXPOSE 80
