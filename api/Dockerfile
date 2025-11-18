FROM php:8.3-apache

# Instala dependências, configura PHP e habilita módulos Apache
RUN apt-get update && \
    \
    # Pacotes necessários ao PHP e utilitários
    apt-get install -y --no-install-recommends \
        zip unzip curl git nano vim \
        libzip-dev libonig-dev \
        libpng-dev libjpeg-dev libfreetype6-dev \
        libxml2-dev libssl-dev && \
    \
    # Configurar e instalar extensões do PHP
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo_mysql mbstring zip opcache && \
    \
    # Habilitar módulos do Apache
    a2enmod rewrite headers && \
    \
    # Limpeza para reduzir tamanho da imagem
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Define diretório de trabalho
WORKDIR /var/www/html

# Ajusta permissões para www-data
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html