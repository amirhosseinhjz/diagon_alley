FROM php:8.1-cli-alpine

RUN echo -e "\e[32m > Install core dependencies\e[0m"
RUN apk add --no-cache --update bash curl freetype-dev libjpeg-turbo-dev libpng-dev

RUN echo -e "\e[32m > Install PHP extensions\e[0m"
RUN apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && docker-php-ext-install bcmath gd pdo_mysql pcntl \
    && pecl install redis \
	&& docker-php-ext-enable redis \
    && docker-php-source delete \
	&& apk del .phpize-deps

RUN echo -e "\e[32m > PHP modules\e[0m"
RUN php -m

RUN echo -e "\e[32m > Install Supervisor\e[0m"
RUN apk add --no-cache --update supervisor

RUN apk add nodejs npm

CMD ["mkdir", "/var/log/supervisor"]
