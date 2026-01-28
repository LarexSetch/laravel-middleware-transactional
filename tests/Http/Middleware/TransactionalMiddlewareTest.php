<?php

declare(strict_types=1);

namespace Larexsetch\LaravelTransactional\Tests\Http\Middleware;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Larexsetch\LaravelTransactional\Attributes\Transactional;
use Larexsetch\LaravelTransactional\Http\Middleware\TransactionalMiddleware;
use Larexsetch\LaravelTransactional\Tests\TestCase;
use Mockery;

class TransactionalMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_skips_when_no_route_is_present(): void
    {
        $request = Request::create('/');
        $middleware = new TransactionalMiddleware();

        $response = $middleware->handle($request, fn($req) => 'next');

        $this->assertEquals('next', $response);
    }

    public function test_it_skips_when_not_a_controller_action(): void
    {
        $request = Request::create('/');
        $request->setRouteResolver(function () use ($request) {
            $route = Route::get('/', fn() => 'closure');
            $route->bind($request);

            return $route;
        });

        $middleware = new TransactionalMiddleware();
        $response = $middleware->handle($request, fn($req) => 'next');

        $this->assertEquals('next', $response);
    }

    public function test_it_skips_when_transactional_attribute_is_absent(): void
    {
        $request = Request::create('/');
        $request->setRouteResolver(function () use ($request) {
            $route = Route::get('/', [TestController::class, 'withoutAttribute']);
            $route->bind($request);

            return $route;
        });

        $middleware = new TransactionalMiddleware();
        $response = $middleware->handle($request, fn($req) => 'next');

        $this->assertEquals('next', $response);
    }

    public function test_it_starts_transaction_when_attribute_is_present(): void
    {
        $request = Request::create('/');
        $request->setRouteResolver(function () use ($request) {
            $route = Route::get('/', [TestController::class, 'withAttribute']);
            $route->bind($request);

            return $route;
        });

        DB::shouldReceive('connection')
            ->with(null)
            ->once()
            ->andReturnSelf();

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn($callback) => $callback());

        $middleware = new TransactionalMiddleware();
        $response = $middleware->handle($request, fn($req) => 'next');

        $this->assertEquals('next', $response);
    }

    public function test_it_uses_specified_connection(): void
    {
        $request = Request::create('/');
        $request->setRouteResolver(function () use ($request) {
            $route = Route::get('/', [TestController::class, 'withCustomConnection']);
            $route->bind($request);

            return $route;
        });

        DB::shouldReceive('connection')
            ->with('custom')
            ->once()
            ->andReturnSelf();

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn($callback) => $callback());

        $middleware = new TransactionalMiddleware();
        $response = $middleware->handle($request, fn($req) => 'next');

        $this->assertEquals('next', $response);
    }
}

class TestController
{
    public function withoutAttribute()
    {
    }

    #[Transactional]
    public function withAttribute()
    {
    }

    #[Transactional(connection: 'custom')]
    public function withCustomConnection()
    {
    }
}
