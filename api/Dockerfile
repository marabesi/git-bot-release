FROM php:7.4

WORKDIR /var/www

COPY . .

ENV CLIENT_ID '123'
ENV SECRET '123'
ENV REDIRECT_URL 'http://localhost:8181'
ENV STATE '123'
ENV GITLAB_URL 'https://my.gitlab.com'
ENV WEBHOOK_INCOME_URL 'http://localhost:8181/hook/income'
ENV WEBHOOK_TOKEN '123'
ENV WEBHOOK_PUSH 'true'
ENV WEBHOOK_ENABLE_SSL_VERIFICATION 'true'

RUN apt-get update && \
    apt-get install nodejs npm git libzip-dev -y && \
    npm install -g serverless && \
    docker-php-ext-install -j$(nproc) zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN echo CLIENT_ID=$CLIENT_ID >> .env && \
    echo SECRET=$SECRET >> .env && \
    echo REDIRECT_URL=$REDIRECT_URL >> .env && \
    echo STATE=$STATE >> .env && \
    echo GITLAB_URL=$GITLAB_URL >> .env && \
    echo WEBHOOK_INCOME_URL=$WEBHOOK_INCOME_URL >> .env && \
    echo WEBHOOK_TOKEN=$WEBHOOK_TOKEN >> .env && \
    echo WEBHOOK_PUSH=$WEBHOOK_PUSH >> .env && \
    echo WEBHOOK_ENABLE_SSL_VERIFICATION=$WEBHOOK_ENABLE_SSL_VERIFICATION >> .env

RUN composer install

EXPOSE 8181

CMD php -S 0.0.0.0:8181 public/index.php