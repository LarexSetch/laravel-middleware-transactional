FROM php:8.4-cli

WORKDIR /opt/project

RUN curl -o phpunit.phar https://phar.phpunit.de/phpunit-11.5.50.phar && \
    chmod +x phpunit.phar && \
    mv phpunit.phar /usr/local/bin/phpunit

CMD ["php"]
