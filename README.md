# laravel-middleware-transactional
Decorates method as transactional method


# Run tests

```shell
export DOCKER_TEST_TAG=phpunit:latest
docker build -t $DOCKER_TEST_TAG -f tests/Dockerfile .
docker run --rm -v ./:/opt/project $DOCKER_TEST_TAG /usr/local/bin/phpunit --configuration phpunit.xml tests/Http/Middleware
```