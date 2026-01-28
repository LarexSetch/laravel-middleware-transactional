# Transactional decorator

Decorates method as transactional

## Usage

### Require dependency

```shell
composer require larexsetch/laravel-transactional
```

### Append middleware

```injectablephp
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\Larexsetch\LaravelTransactional\Http\Middleware\TransactionalMiddleware::class);
    })
    ->create();
```

### Add attribute in controller

```injectablephp
readonly class NoteController
{
    public function __construct(
        private SomeService $service
    ) {}

    #[Transactional]
    public function index($request): JsonResponse
    {
        $this->service->doOne();
        $this->service->doAnother();
        // ... more actions

        return response()->json(['ok' => true]);
    }
}
```

# Run tests

```shell
export DOCKER_TEST_TAG=phpunit:latest
docker build -t $DOCKER_TEST_TAG -f tests/Dockerfile .
docker run --rm -v ./:/opt/project $DOCKER_TEST_TAG /usr/local/bin/phpunit --configuration phpunit.xml tests/Http/Middleware
```
