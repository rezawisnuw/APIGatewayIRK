<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use App\Http\Middleware\Cors;

class CorsTest extends TestCase
{
    public function testCorsHeaders()
    {
        $middleware = new Cors();

        $request = $this->createRequest('GET');
        $response = $middleware->handle($request, function ($req) {
            return response('Test Response');
        });

        $this->assertTrue($response->headers->has('Access-Control-Allow-Origin'));
        $this->assertEquals('*', $response->headers->get('Access-Control-Allow-Origin'));

        $this->assertTrue($response->headers->has('Access-Control-Allow-Methods'));
        $this->assertEquals('GET, POST, PUT, DELETE, OPTIONS', $response->headers->get('Access-Control-Allow-Methods'));

        $this->assertTrue($response->headers->has('Access-Control-Allow-Headers'));
        $this->assertEquals('Origin, X-Requested-With, Content-Type, X-Token-Auth, Authorization, Cookie, X-Frame-Options', $response->headers->get('Access-Control-Allow-Headers'));

        $this->assertTrue($response->headers->has('Access-Control-Allow-Credentials'));
        $this->assertEquals('true', $response->headers->get('Access-Control-Allow-Credentials'));

        $this->assertTrue($response->headers->has('Access-Control-Max-Age'));
        $this->assertEquals('3600', $response->headers->get('Access-Control-Max-Age'));
    }

    protected function createRequest($method, $uri = '/')
    {
        return \Illuminate\Http\Request::create($uri, $method);
    }
}
