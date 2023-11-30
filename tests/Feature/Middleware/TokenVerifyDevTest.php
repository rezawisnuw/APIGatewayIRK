<?php

namespace Tests\Feature\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Middleware\TokenVerifyDev;
use Illuminate\Http\Request;

class TokenVerifyDevTest extends TestCase
{
    use RefreshDatabase;

    public function testValidToken()
    {
        $token = 'valid_token_here';

        $request = Request::create('/api/some-endpoint', 'GET');
        $request->headers->set('Authorization-dev', 'Bearer ' . $token);

        $middleware = new TokenVerifyDev();

        $response = $middleware->handle($request, function ($request) {
            return response('next');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    // public function testEmptyToken()
    // {
    //     $request = Request::create('/api/some-endpoint', 'GET');

    //     $middleware = new TokenVerifyDev();

    //     $response = $middleware->handle($request, function ($request) {
    //         // Simulate an empty token
    //         return response('next');
    //     });

    //     $this->assertEquals(400, $response->getStatusCode());
    // }

    // public function testInvalidToken()
    // {
    //     $request = Request::create('/api/some-endpoint', 'GET');
    //     $request->headers->set('Authorization-dev', 'Bearer invalid_token_here');

    //     $middleware = new TokenVerifyDev();

    //     $response = $middleware->handle($request, function ($request) {
    //         // Simulate an invalid token
    //         return response('next');
    //     });

    //     $this->assertEquals(400, $response->getStatusCode());
    // }
}
