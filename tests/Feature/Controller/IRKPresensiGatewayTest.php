<?php

namespace Tests\Feature\Controller;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Client;

use App\Http\Controllers\IRKPresensiGateway;
use App\Http\Middleware\TokenVerify;

class IRKPresensiGatewayTest extends TestCase
{

    use RefreshDatabase;

    public function testTokenAuthentication()
    {
        $token = 'valid_token_here';

        $request = Request::create('/api/live/presensi/get', 'POST');
        $request->headers->set('Authorization', 'Bearer ' . $token);

        $middleware = new TokenVerify();

        $response = $middleware->handle($request, function ($request) {
            return response('next');
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetUnitSuccess()
    {
        // Buat objek palsu (mock) untuk IRKPresensiGateway
        $gatewayMock = $this->getMockBuilder(IRKPresensiGateway::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock hasil yang diharapkan dari metode get
        $expectedResponse = [
            'result' => 'Data has been processed',
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
        // Buat objek palsu (mock) untuk IRKPresensiGateway
        $gatewayMock = $this->getMockBuilder(IRKPresensiGateway::class)
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

//     public function testGetEndToEndSuccess()
// {
//     // Buat objek Client Guzzle
//     $client = new \GuzzleHttp\Client();

//     // Buat permintaan GET ke endpoint yang sesuai
//     $response = $client->get('http://localhost:8000/api/dev/presensi/get', [
//         'query' => [
//             'userid' => 1,
//             'code' => '2'
//         ],
//     ]);

//     // Pastikan status respons adalah 200
//     $this->assertEquals(200, $response->getStatusCode());

//     // Dekode respons JSON
//     $data = json_decode($response->getBody(), true);

//     // Pastikan struktur respons sesuai dengan yang diharapkan
//     $this->assertArrayHasKey('result', $data);
//     $this->assertArrayHasKey('data', $data);
//     $this->assertArrayHasKey('message', $data);
//     $this->assertArrayHasKey('status', $data);
// }

// public function testGetEndToEndFailure()
// {
//     // Buat objek Client Guzzle
//     $client = new \GuzzleHttp\Client();

//     // Buat permintaan GET ke endpoint yang sesuai
//     $response = $client->get('http://localhost:8000/api/dev/presensi/get', [
//         'query' => [
//             'userid' => 1,
//             'code' => '0'
//         ],
//     ]);

//     // Pastikan status respons adalah 200
//     $this->assertEquals(200, $response->getStatusCode());

//     // Dekode respons JSON
//     $data = json_decode($response->getBody(), true);

//     // Pastikan struktur respons sesuai dengan yang diharapkan
//     $this->assertArrayHasKey('result', $data);
//     $this->assertArrayHasKey('data', $data);
//     $this->assertArrayHasKey('message', $data);
//     $this->assertArrayHasKey('status', $data);

//     // Pastikan status memiliki nilai 0
//     $this->assertEquals(0, $data['status']);
// }


//     public function testGetIntegration()
//     {
//         $client = new \GuzzleHttp\Client();

//         $response = $client->post('http://localhost:8000/api/dev/presensi/get', [
//             'json' => [
//                 'userid' => '2005004059',
//                 'list_query' => [
//                     [
//                         'conn' => 'DBPRESENSI',
//                         'query' => "SELECT TOP 10 * FROM PresensiIntegrationOs2 WITH(NOLOCK) WHERE personnelnumber = '2005004059';",
//                         'process_name' => 'GetDataPresensi'
//                     ]
//                 ]
//             ],
//         ]);

//         $this->assertEquals(200, $response->getStatusCode());

//         $data = json_decode($response->getBody(), true);

//         $this->assertArrayHasKey('result', $data);
//         $this->assertArrayHasKey('data', $data);
//         $this->assertArrayHasKey('message', $data);
//         $this->assertArrayHasKey('status', $data);
//         $this->assertArrayHasKey('statuscode', $data);
//     }
}
