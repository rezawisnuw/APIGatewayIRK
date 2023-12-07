<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use App\Http\Middleware\TokenAuth;
use App\Helper\IRKHelp;
use App\Models\Credentials;
use Illuminate\Http\Request;

class TokenAuthTest extends TestCase
{
    public function testValidToken()
    {
        $irkHelpMock = $this->createMock(IRKHelp::class);
        $credentialsMock = $this->createMock(Credentials::class);

        $irkHelpMock->expects($this->any())
            ->method('Segment')
            ->willReturn(['authorize' => 'Authorization', 'config' => 'your_config', 'path' => 'Live']);

        $credentialsMock->expects($this->any())
            ->method('ValidateTokenAuth')
            ->willReturn((object)['DecodeResult' => 'Cocok']);

        $tokenAuth = new TokenAuth($irkHelpMock);

        $request = new Request();
        $request->headers->set('Authorization', 'Bearer your_valid_token');

        $response = $tokenAuth->handle($request, function ($request) {
            return response('next'); 
        });

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(0, $responseData['status']);
    }

    public function testEmptyToken()
    {
        $irkHelpMock = $this->createMock(IRKHelp::class);

        $irkHelpMock->expects($this->any())
            ->method('Segment')
            ->willReturn(['authorize' => 'Authorization', 'config' => 'your_config', 'path' => 'Live']);

        $tokenAuth = new TokenAuth($irkHelpMock);

        $request = new Request();

        $response = $tokenAuth->handle($request, function ($request) {
            return response('next');
        });

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(0, $responseData['status']);
    }

    public function testInvalidToken()
    {
        $irkHelpMock = $this->createMock(IRKHelp::class);
        $credentialsMock = $this->createMock(Credentials::class);

        $irkHelpMock->expects($this->any())
            ->method('Segment')
            ->willReturn(['authorize' => 'Authorization', 'config' => 'your_config', 'path' => 'Live']);

        $credentialsMock->expects($this->any())
            ->method('ValidateTokenAuth')
            ->willReturn((object)['DecodeResult' => 'NotCocok']);

        $tokenAuth = new TokenAuth($irkHelpMock);

        $request = new Request();
        $request->headers->set('Authorization', 'Bearer your_invalid_token');

        $response = $tokenAuth->handle($request, function ($request) {
            return response('next');
        });

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals(0, $responseData['status']);
    }
}

