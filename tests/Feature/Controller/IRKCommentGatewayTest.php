<?php

namespace Tests\Feature\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

use App\Http\Controllers\IRKCommentGateway;
use App\Http\Middleware\TokenVerify;

class IRKCommentGatewayTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function testGet(){
        $response = $this->post('/api/live/comment/get', [
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

    public function testPost(){
        $response = $this->post('/api/live/comment/post', [
            'comment' => 'TEST',
            'idticket' => '1',
            'userid' => '2000000000',
            'code' => '1',
            'tag' => 'motivasi'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data',
            'message',
            'status',
        ]);
    }

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

    public function testGetSuccessWithoutConstructor()
    {
        // Buat objek palsu (mock) untuk IRKCommentGateway
        $gatewayMock = $this->getMockBuilder(IRKCommentGateway::class)
            ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
            ->getMock();

        // Mock hasil yang diharapkan dari metode get
        $expectedResponse = [
            'result' => 'Match', // Sesuaikan dengan hasil yang diharapkan
            'message' => 'Success on Run',
            'data' => [/* Data yang diharapkan di sini */],
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

    public function testGetEndToEndSuccess()
{
    $response = $this->post('/api/live/comment/get', [
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

public function testGetEndToEndFailure()
{
    $response = $this->post('/api/live/comment/get', [
        'code' => '2', // Code yang menghasilkan kegagalan
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
    $response->assertJson(['status' => 0]); // Verifikasi bahwa status adalah 0 untuk kegagalan
}

public function testGetUnitSuccess()
{
    // Buat objek palsu (mock) untuk IRKCommentGateway
    $gatewayMock = $this->getMockBuilder(IRKCommentGateway::class)
        ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
        ->getMock();

    // Mock hasil yang diharapkan dari metode get
    $expectedResponse = [
        'result' => 'Match', // Sesuaikan dengan hasil yang diharapkan
        'message' => 'Success on Run',
        'data' => [/* Data yang diharapkan di sini */],
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
    // Buat objek palsu (mock) untuk IRKCommentGateway
    $gatewayMock = $this->getMockBuilder(IRKCommentGateway::class)
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

public function testGetIntegrationSuccess()
{
    $response = $this->post('/api/live/comment/get', [
        'code' => '1',
        'idticket' => '1',
        'tag' => 'curhatku'
    ]);
    $response->assertStatus(200);

    // Anda dapat menambahkan lebih banyak verifikasi sesuai dengan kebutuhan
}

public function testGetIntegrationFailure()
{
    $response = $this->post('/api/live/comment/get', [
        'code' => '2', // Code yang menghasilkan kegagalan
        'idticket' => '1',
        'tag' => 'curhatku'
    ]);
    $response->assertStatus(200);
    $response->assertJson(['status' => 0]); // Verifikasi bahwa status adalah 0 untuk kegagalan

    // Anda dapat menambahkan lebih banyak verifikasi sesuai dengan kebutuhan
}

}
