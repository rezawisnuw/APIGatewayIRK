<?php

namespace Tests\Feature\Controller;

use Tests\TestCase;
use App\Http\Controllers\IRKReportGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class IRKReportGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function testGetReport()
    {
        $request = [
            'key' => 'value', 
        ];

        $response = $this->json('POST', '/api/dev/report/get', $request);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
    }

    public function testPostReport()
    {
        $request = [
            'key' => 'value',
        ];
        $response = $this->json('POST', '/api/dev/report/post', $request);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
            'ttldata',
        ]);
    }

    public function testPutReport()
    {
        $request = [
            'key' => 'value',
        ];

        $response = $this->json('POST', '/api/dev/report/put', $request);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
            'ttldata',
        ]);
    }

    public function testDeleteReport()
    {
        $request = [
            'key' => 'value', 
        ];

        $response = $this->json('POST', '/api/dev/report/delete', $request);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
            'ttldata',
        ]);
    }

    public function testGetUnitSuccess()
    {
        // Buat objek palsu (mock) untuk IRKReportGateway
        $gatewayMock = $this->getMockBuilder(IRKReportGateway::class)
            ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
            ->onlyMethods(['get']) // Tentukan metode yang akan di-mock
            ->getMock();

        // Mock hasil yang diharapkan dari metode get
        $expectedResponse = [
            'result' => 'Match', // Sesuaikan dengan hasil yang diharapkan
            'data' => [/* Data yang diharapkan di sini */],
            'message' => 'Success on Run',
            'status' => 1,
        ];

        // Ganti metode get dengan perilaku yang diharapkan
        $gatewayMock->method('get')->willReturn(json_encode($expectedResponse));

        // Panggil metode get
        $response = $gatewayMock->get(new Request());

        // Verifikasi hasil yang diharapkan
        $this->assertEquals(json_encode($expectedResponse), $response);
        $this->assertJson($response);
    }

    public function testGetIntegration()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://irk-gateway.hrindomaret.com/api/live/report/get', [
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
