<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\UploadedFile;
use GuzzleHttp\Client;
use Tests\TestCase;

use App\Http\Controllers\IRKMotivasiGateway;
use App\Http\Middleware\TokenVerify;

class IRKMotivasiGatewayTest extends TestCase
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

    public function testGetMotivasi()
    {
        $request = [
            'key' => 'value',
        ];

        $response = $this->json('POST', '/api/live/motivasi/get', $request);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
    }

    public function testPostMotivasi()
    {
        $request = [
            'key' => 'value', 
            'photo' => UploadedFile::fake()->image('motivasi.jpg'),
        ];

        $response = $this->json('POST', '/api/live/motivasi/post', $request);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
    }

    public function testGetUnitSuccess()
    {
        // Buat objek palsu (mock) untuk IRKCeritakitaGateway
        $gatewayMock = $this->getMockBuilder(IRKMotivasiGateway::class)
            ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
            ->getMock();

        // Mock hasil yang diharapkan dari metode get
        $expectedResponse = [
            'result' => 'Data has been process', // Sesuaikan dengan hasil yang diharapkan
            'data' => [/* Data yang diharapkan di sini */],
            'message' => 'Success on Run',
            'status' => 1,
            'statuscode' => 200
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
        $gatewayMock = $this->getMockBuilder(IRKMotivasiGateway::class)
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

    public function testGetIntegration()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://irk-gateway.hrindomaret.com/api/live/motivasi/get', [
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
}
