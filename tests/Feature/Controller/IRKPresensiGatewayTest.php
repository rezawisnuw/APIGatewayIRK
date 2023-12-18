<?php

namespace Tests\Feature\Controller;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
//use Illuminate\Support\Facades\Factory;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Client;

use App\Http\Controllers\IRK\PresensiGateway;
use App\Http\Middleware\TokenVerify;

class IRKPresensiGatewayTest extends TestCase
{

    use RefreshDatabase;

    public function testTokenAuthentication()
    {
        $token = 'HLfipy304fIvFVWdKiH7uGnwn4V4aHllgf6Xh60qe6566c1a';

        $request = Request::create('/api/dev/presensi/get', 'POST');
        $request->headers->set('Authorization-dev', 'Bearer ' . $token);

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

    public function testGetIntegration()
    {
        // Simulate authentication by acting as a user with a valid token
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;
        
        // Mock signature untuk simulasi 'Match'
        $signature = Crypt::encryptString(json_encode(['result' => 'Match']));
        
        $this->assertNotEmpty($token);

        // Mock data request untuk metode 'get'
        $data = [
            'userid' => 123,  // Sesuaikan dengan ID pengguna yang benar
            'tglAwal' => '2023-01-01',
            'tglAkhir' => '2023-01-31',
        ];

        // Mock hasil yang diharapkan dari permintaan GET
        $expectedResponse = [
            'result' => 'Data has been processed',
            'data' => [/* Data yang diharapkan di sini */],
            'message' => 'Success on Run',
            'status' => 1,
            'statuscode' => 200
        ];

        // Menggantikan permintaan HTTP untuk endpoint tertentu dengan respons palsu
        Http::fake([
            'http://hrindomaret.com:8000/api/v1/dev/presensi/get' => Http::response($expectedResponse, 200),
        ]);
        
        // Your integration code that triggers the GET request
        $response = $this->withHeaders([
            'Authorization-dev' => 'Bearer ' . $token,
            'Signature-dev' => $signature,
        ])->post('/api/v1/dev/presensi/get', $data);

        //dd($response->headers);
        dd($response->content());
        dd($response->headers);

        // Assertions
        $this->assertEquals(1, $response->json('status'));
        //$this->assertEquals(1, $responseStatus,
        //"Response status should be 1, but received $responseStatus. Response: " . $response->content());
    }

    public function testGetFunctional()
    {
        // Define the expected response structure
        $expectedResponse = [
            'result' => 'Data has been processed',
            'data' => [/* Data yang diharapkan di sini */],
            'message' => 'Success on Run',
            'status' => 1,
            'statuscode' => 200
        ];

        // Buat objek palsu (mock) untuk IRKPresensiGateway
        $gatewayMock = $this->getMockBuilder(IRKPresensiGatewayTest::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $request = new Request([
            'userid' => 123, // Sesuaikan dengan ID pengguna yang benar
            'tglAwal' => '2023-01-01',
            'tglAkhir' => '2023-01-31',
        ]);

        //$presensiGateway = new PresensiGateway($request);

        $mockRequest = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockIRKHelp = new \Tests\Mocks\MockIRKHelp($mockRequest);
        $gatewayMock->helper = $mockIRKHelp;

        $gatewayMock->expects($this->once())
            ->method('get')
            ->willReturn(json_encode($expectedResponse));

            $response = json_decode($gatewayMock->get($request), true);

        // Verifikasi struktur dan status respons
        $this->assertArrayHasKey('result', $response);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertEquals(1, $response['status']);
    }

    public function testGetEndToEnd()
    {
        // Buat objek Client Guzzle
        $client = new \GuzzleHttp\Client();

        // Mock hasil yang diharapkan dari permintaan GET
        $expectedResponse = [
            'result' => 'Data has been processed',
            'data' => [/* Data yang diharapkan di sini */],
            'message' => 'Success on Run',
            'status' => 1,
            'statuscode' => 200
        ];

        // Menggantikan permintaan HTTP untuk endpoint tertentu dengan respons palsu
        Http::fake([
            'http://hrindomaret.com:8000/api/v1/dev/presensi/get' => Http::response($expectedResponse, 200),
        ]);

        // Buat permintaan GET ke endpoint yang sesuai
        $response = $client->get('http://hrindomaret.com:8000/api/v1/dev/presensi/get', [
        ]);

        // Pastikan status respons adalah 200
        $this->assertEquals(200, $response->getStatusCode());

        // Dekode respons JSON
        $data = json_decode($response->getBody(), true);

        // Pastikan struktur respons sesuai dengan yang diharapkan
        $this->assertArrayHasKey('result', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('status', $data);
    }

    public function testPostIntegration()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->post('http://hrindomaret.com:8000/api/v1/dev/presensi/post', [
            'json' => [
                'data' => [
                    'nik' => '12345', // Adjust the data accordingly
                    'tglAbsen' => '2023-01-01',
                    'jamAbsen' => '08:00:00',
                ],
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        

        $this->assertArrayHasKey('result', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals(1, $data['status']);
    }

    public function testPostFunctional()
    {
        // Buat objek Request yang dapat digunakan
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Buat objek palsu (mock) untuk IRKPresensiGateway
        $gatewayMock = $this->getMockBuilder(IRKPresensiGatewayTest::class)
            ->onlyMethods(['post'])
            ->disableOriginalConstructor()
            ->getMock();

        // Mock hasil yang diharapkan dari metode post
        $expectedResponse = [
            'result' => 'Data has been processed',
            'data' => [],
            'message' => 'Success on Run',
            'status' => 1,
            'statuscode' => 200
        ];

        // Menggantikan metode post dengan perilaku yang diharapkan
        $gatewayMock->expects($this->once())
            ->method('post')
            ->willReturn(json_encode($expectedResponse));

        // Panggil metode post
        $response = $gatewayMock->post($request);

        // Verifikasi hasil yang diharapkan
        $this->assertEquals(json_encode($expectedResponse), $response);
        $this->assertJson($response);
    }

    public function testPostEndToEnd()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->post('http://hrindomaret.com:8000/api/v1/dev/presensi/post', [
            'json' => [
                'data' => [
                    'nik' => '12345', // Adjust the data accordingly
                    'tglAbsen' => '2023-01-01',
                    'jamAbsen' => '08:00:00',
                ],
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('result', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals(1, $data['status']);
    }
}
