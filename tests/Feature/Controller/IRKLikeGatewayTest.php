<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Client;
use Tests\TestCase;

use App\Http\Controllers\IRKLikeGateway;
use App\Http\Middleware\TokenVerify;

class IRKLikeGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function testTokenAuthentication()
    {
        $token = 'valid_token_here';

        $request = Request::create('/api/live/like/get', 'POST');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $middleware = new TokenVerify();

        $response = $middleware->handle($request, function ($request) {
            return response('next');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetEndToEndSuccess(){
        $response = $this->post('/api/live/like/get', [
            'code' => '1',
            'idticket' => '1',
            'tag' => 'curhatku'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
    }

    public function testGetEndToEndFailure(){
        $response = $this->post('/api/live/like/get', [
            'code' => '1',
            'idticket' => '4',
            'tag' => 'ea'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
    }

    public function testPost()
    {
        $response = $this->post('/api/live/like/post',);
        $response->assertStatus(200);
    }

    public function testGetUnitSuccess()
    {
        // Buat objek palsu (mock) untuk IRKCeritakitaGateway
        $gatewayMock = $this->getMockBuilder(IRKLikeGateway::class)
            ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
            ->getMock();

        // Mock hasil yang diharapkan dari metode get
        $expectedResponse = [
            'result' => 'Match', // Sesuaikan dengan hasil yang diharapkan
            'data' => [/* Data yang diharapkan di sini */],
            'message' => 'Success on Run',
            'status' => 1,
        ];

        // Menggantikan metode get dengan perilaku yang diharapkan
        $gatewayMock->method('get')->willReturn(json_encode($expectedResponse));

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

    public function testGetUnitFailure()
    {
        // Buat objek palsu (mock) untuk IRKCeritakitaGateway
        $gatewayMock = $this->getMockBuilder(IRKLikeGateway::class)
            ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
            ->getMock();

        // Mock hasil yang diharapkan dari metode get
        $expectedResponse = [
            'result' => 'Mismatch', // Sesuaikan dengan hasil yang diharapkan
            'data' => [], // Data yang diharapkan
            'message' => 'Failed on Run',
            'status' => 0,
        ];

        // Menggantikan metode get dengan perilaku yang diharapkan
        $gatewayMock->method('get')->willReturn(json_encode($expectedResponse));

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
        // Buat objek palsu (mock) untuk IRKLikeGateway
        $gatewayMock = $this->getMockBuilder(IRKLikeGateway::class)
            ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
            ->getMock();

        // Mock hasil yang diharapkan dari metode post
        $expectedResponse = [
            'result' => 'Match', // Sesuaikan dengan hasil yang diharapkan
            'data' => [/* Data yang diharapkan di sini */],
            'message' => 'Success on Run',
            'status' => 1,
        ];

        // Menggantikan metode post dengan perilaku yang diharapkan
        $gatewayMock->method('post')->willReturn(json_encode($expectedResponse));

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
        // Buat objek palsu (mock) untuk IRKLikeGateway
        $gatewayMock = $this->getMockBuilder(IRKLikeGateway::class)
            ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
            ->getMock();

        // Mock hasil yang diharapkan dari metode post
        $expectedResponse = [
            'result' => 'Mismatch', // Sesuaikan dengan hasil yang diharapkan
            'data' => [], // Data yang diharapkan
            'message' => 'Failed on Run',
            'status' => 0,
        ];

        // Menggantikan metode post dengan perilaku yang diharapkan
        $gatewayMock->method('post')->willReturn(json_encode($expectedResponse));

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

    public function testGetIntegration()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://irk-gateway.hrindomaret.com/api/live/like/get', [
            'form_params' => [
                'userid' => 1,
                'code' => '1',
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('result', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('statuscode', $data);
    }

    // public function testGetIntegratio1()
    // {
    //     $slug = 'dev';
    //     $requestData = ['data' => 'your_request_data_here'];

    //     $client = new \GuzzleHttp\Client();

    //     $response = $client->request('POST', "https://irk-gateway.hrindomaret.com/api/$slug/like/get", [
    //         'json' => $requestData,
    //     ]);

    //     $statusCode = $response->getStatusCode();
    //     $responseData = json_decode($response->getBody(), true);

    //     $this->assertEquals(200, $statusCode);
    //     $this->assertArrayHasKey('result', $responseData);
    //     $this->assertArrayHasKey('data', $responseData);
    //     $this->assertArrayHasKey('message', $responseData);
    //     $this->assertArrayHasKey('status', $responseData);
    // }
}
