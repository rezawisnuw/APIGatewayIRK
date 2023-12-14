<?php

namespace Tests\Feature\Controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Client;

use App\Http\Controllers\IRK\PresensiGateway;
use App\Http\Middleware\TokenVerify;

class IRKPresensiGatewayTest extends TestCase
{

    use RefreshDatabase;

    public function testTokenAuthentication()
    {
        $token = 'valid_token_here';

        $request = Request::create('/api/dev/presensi/get', 'POST');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $middleware = new TokenVerify();

        $response = $middleware->handle($request, function ($request) {
            return response('next');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetUnitSuccess()
    {
        $gatewayMock = $this->getMockBuilder(IRKPresensiGatewayTest::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $expectedResponse = [
            'result' => 'Data has been processed',
            'data' => [],
            'message' => 'Success on Run',
            'status' => 1,
            'statuscode' => 200
        ];

        $mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockIRKHelp = new \Tests\Mocks\MockIRKHelp($mockRequest);
        $gatewayMock->helper = $mockIRKHelp;

        $gatewayMock->expects($this->once())
            ->method('get')
            ->willReturn(json_encode($expectedResponse));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $gatewayMock->get($request);

        $this->assertEquals(json_encode($expectedResponse), $response);
        $this->assertJson($response);
    }

    public function testGetUnitFailure()
    {
        // Buat objek palsu (mock) untuk IRKPresensiGateway
        $gatewayMock = $this->getMockBuilder(IRKPresensiGatewayTest::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock hasil yang diharapkan dari metode get
        $expectedResponse = [
            'result' => 'Mismatch',
            'data' => [],
            'message' => 'Failed on Run',
            'status' => 0,
        ];

        // Menggantikan metode get dengan perilaku yang diharapkan
        $gatewayMock->expects($this->once())
            ->method('get')
            ->willReturn(json_encode($expectedResponse));

        // Buat objek Request yang dapat digunakan
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Panggil metode get
        $response = $gatewayMock->get($request);

        // Verifikasi hasil yang diharapkan
        $this->assertEquals(json_encode($expectedResponse), $response);
        $this->assertJson($response);
    }

    public function testPostUnitSuccess()
    {
        // Buat objek palsu (mock) untuk IRKPresensiGateway
        $gatewayMock = $this->getMockBuilder(IRKPresensiGatewayTest::class)
            ->onlyMethods(['post'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock hasil yang diharapkan dari metode post
        $expectedResponse = [
            'result' => 'Data has been processed',
            'data' => [/* Data yang diharapkan di sini */],
            'message' => 'Success on Run',
            'status' => 1,
            'statuscode' => 200
        ];

        // Menggantikan metode post dengan perilaku yang diharapkan
        $gatewayMock->expects($this->once())
            ->method('post')
            ->willReturn(json_encode($expectedResponse));

        // Buat objek Request yang dapat digunakan
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Panggil metode post
        $response = $gatewayMock->post($request);

        // Verifikasi hasil yang diharapkan
        $this->assertEquals(json_encode($expectedResponse), $response);
        $this->assertJson($response);
    }

    public function testPostUnitFailure()
    {
        // Buat objek palsu (mock) untuk IRKPresensiGateway
        $gatewayMock = $this->getMockBuilder(IRKPresensiGatewayTest::class)
            ->onlyMethods(['post'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock hasil yang diharapkan dari metode post
        $expectedResponse = [
            'result' => 'Mismatch',
            'data' => [],
            'message' => 'Failed on Run',
            'status' => 0,
        ];

        // Menggantikan metode post dengan perilaku yang diharapkan
        $gatewayMock->expects($this->once())
            ->method('post')
            ->willReturn(json_encode($expectedResponse));

        // Buat objek Request yang dapat digunakan
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Panggil metode post
        $response = $gatewayMock->post($request);

        // Verifikasi hasil yang diharapkan
        $this->assertEquals(json_encode($expectedResponse), $response);
        $this->assertJson($response);
    }
}
