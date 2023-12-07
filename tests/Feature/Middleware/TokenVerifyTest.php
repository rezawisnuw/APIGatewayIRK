<?php

namespace Tests\Feature\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Middleware\TokenVerify;
use Illuminate\Http\Request;

class TokenVerifyTest extends TestCase
{
    use RefreshDatabase;

    public function testValidToken()
    {
        $token = 'valid_token_here';

        $request = Request::create('/api/some-endpoint', 'GET');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $middleware = new TokenVerify();

        $response = $middleware->handle($request, function ($request) {
            return response('next');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    // public function testEmptyToken()
    // {
    //     $request = Request::create('/api/some-endpoint', 'GET');
    //     // Don't set the 'Authorization' header to simulate an empty token

    //     $middleware = new TokenVerify();

    //     $response = $middleware->handle($request, function ($request) {
    //         return response('next');
    //     });

    //     $this->assertEquals(400, $response->getStatusCode());
    // }

    // public function testInvalidToken()
    // {
    //     // Simulate an invalid token
    //     $token = 'invalid_token_here';

    //     $request = Request::create('/api/some-endpoint', 'GET');
    //     $request->headers->set('Authorization', 'Bearer ' . $token);

    //     $middleware = new TokenVerify();

    //     $response = $middleware->handle($request, function ($request) {
    //         return response('next');
    //     });

    //     $this->assertEquals(400, $response->getStatusCode());
    // }
}
