<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use App\Http\Middleware\TokenVerify;
use Mockery;
use Tests\TestCase;

class UtilityGatewayTest extends TestCase
{
    public function testToken()
    {
        $token = 'valid_token_here';

        $request = Request::create('/api/live/login/get', 'POST');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $middleware = new TokenVerify();

        $response = $middleware->handle($request, function ($request) {
            return response('next');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->utilityGateway = Mockery::mock('UtilityGateway');
    }

    public function testLoginESS()
    {
        $request = new Request();
        $request->merge([
            'data' => [
                'nik' => '123456',
            ],
        ]);

        $this->utilityGateway->shouldReceive('LoginESS')
            ->with($request)
            ->andReturn($this->generateMockedResponse());

        $response = $this->utilityGateway->LoginESS($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    protected function generateMockedResponse()
    {
        $mockedResponse = new \Illuminate\Http\Response();
        $mockedResponse->setContent(json_encode([]));

        return $mockedResponse;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

}
