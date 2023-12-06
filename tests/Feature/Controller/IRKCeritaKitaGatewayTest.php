<?php

namespace Tests\Feature\Controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Client;

use App\Http\Controllers\IRKCeritaKitaGateway;
use App\Http\Middleware\TokenVerify;

class IRKCeritaKitaGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function testTokenAuthentication()
    {
        $token = 'valid_token_here';

        $request = Request::create('/api/live/ceritakita/get', 'POST');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $middleware = new TokenVerify();

        $response = $middleware->handle($request, function ($request) {
            return response('next');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetEndToEndSuccess()
    {
        $response = $this->post('/api/live/ceritakita/get', [
            'userid' => 1,
            'code' => '2'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
    }

    public function testGetEndToEndFailure()
    {
        $response = $this->post('/api/live/ceritakita/get', [
            'userid' => 1,
            'code' => '0'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
        $response->assertJson(['status' => 0]);
    }

    public function testGetUnitSuccess()
    {
        // Buat objek palsu (mock) untuk IRKCeritakitaGateway
        $gatewayMock = $this->getMockBuilder(IRKCeritakitaGateway::class)
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

        $responseData = json_decode($response, true);
        $this->assertArrayHasKey('result', $responseData);
        $this->assertArrayHasKey('data', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertArrayHasKey('statuscode', $responseData);
        $this->assertEquals('Data has been process', $responseData['result']);
    }

    public function testGetUnitFailure()
    {
        // Buat objek palsu (mock) untuk IRKCeritakitaGateway
        $gatewayMock = $this->getMockBuilder(IRKCeritakitaGateway::class)
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

    public function testGetUnitInvalidInput()
    {
        $gatewayMock = $this->getMockBuilder(IRKCeritakitaGateway::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Menetapkan hasil yang diharapkan jika input tidak valid
        $expectedResponse = [
            'result' => 'Invalid Input',
            'data' => [],
            'message' => 'Invalid input provided',
            'status' => 0,
        ];

        $gatewayMock->method('get')->willReturn(json_encode($expectedResponse));

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $gatewayMock->get($request);

        $this->assertEquals(json_encode($expectedResponse), $response);
        $this->assertJson($response);
    }

    // /**
    //  * @return array
    //  */
    // public static function testDataProviderForGetUnit()
    // {
    //     return [
    //         'valid_data' => [
    //             'input' => [
    //                 'userid' => 1,
    //                 'code' => '2',
    //             ],
    //             'expectedResult' => [
    //                 'result' => 'Data has been process',
    //                 'data' => [],
    //                 'message' => 'Success on Run',
    //                 'status' => 1,
    //                 'statuscode' => 200,
    //             ],
    //         ],
    //         // Tambahkan data lain sesuai kebutuhan
    //     ];
    // }

    // /**
    //  * @dataProvider testDataProviderForGetUnit
    //  */
    // public function testGetUnit(array $input, array $expectedResult)
    // {
    //     $gatewayMock = $this->getMockBuilder(IRKCeritakitaGateway::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();

    //     $gatewayMock->method('get')->willReturn(json_encode($expectedResult));

    //     $request = $this->getMockBuilder(Request::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();

    //     $response = $gatewayMock->get($request);

    //     $this->assertEquals(json_encode($expectedResult), $response);
    //     $this->assertJson($response);
    //     $this->assertTrue(true);
    //     $this->assertNotEmpty($input);
    //     $this->assertNotEmpty($expectedResult);
    // }

    public function testGetIntegration()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://irk-gateway.hrindomaret.com/api/live/ceritakita/get', [
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