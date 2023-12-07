<?php

namespace Tests\Feature\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Http\Middleware\TokenVerifyStag;
use Illuminate\Http\Request;
use App\Models\Stag\Credential;

class TokenVerifyStagTest extends TestCase
{
    use RefreshDatabase;

    public function testValidToken()
    {
        $token = 'valid_token_here';

        $request = Request::create('/api/some-endpoint', 'GET');
        $request->headers->set('Authorization-stag', 'Bearer ' . $token);

        $middleware = new TokenVerifyStag();

        $response = $middleware->handle($request, function ($request) {
            return response('next');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    // public function testEmptyToken()
    // {
    //     $request = Request::create('/api/some-endpoint', 'GET');
    //     $middleware = new TokenVerifyStag();

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
    //     $request->headers->set('Authorization-stag', 'Bearer ' . $token);

    //     $middleware = new TokenVerifyStag();

    //     $response = $middleware->handle($request, function ($request) {
    //         return response('next');
    //     });

    //     $this->assertEquals(400, $response->getStatusCode());
    // }
}
