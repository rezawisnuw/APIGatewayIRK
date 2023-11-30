<?php

namespace Tests\Unit\Models;

use App\Models\Credentials;
use Illuminate\Http\Request;
use Tests\TestCase;

class CredentialsTest extends TestCase
{
    public function testIsTokenSignatureValid()
    {
        // Buat objek palsu (mock) untuk IRKHelp
        $irkHelpMock = $this->getMockBuilder('IRKHelp')
            ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
            ->getMock();

        // Buat instance dari Credentials dengan objek palsu (mock) IRKHelp
        $request = new Request();
        $slug = 'dev';
        $credentials = new Credentials($request, $slug);
        $credentials->irkHelp = $irkHelpMock;

        // Panggil metode IsTokenSignatureValid
        $token = 'example_token';
        $result = $credentials->IsTokenSignatureValid($token);

        // Lakukan pengujian terhadap hasil
        $this->assertNotNull($result);
    }

    public function testLogin()
    {
        // Buat objek palsu (mock) untuk IRKHelp
        $irkHelpMock = $this->getMockBuilder(IRKHelp::class)
            ->disableOriginalConstructor() // Nonaktifkan pemanggilan konstruktor
            ->getMock();

        // Buat instance dari Credentials dengan objek palsu (mock) IRKHelp
        $request = new Request();
        $slug = 'dev';
        $credentials = new Credentials($request, $slug);
        $credentials->irkHelp = $irkHelpMock;

        // Panggil metode Login
        $postbody = ['nik' => '123456'];
        $result = $credentials->Login($postbody);

        // Lakukan pengujian terhadap hasil
        $this->assertArrayHasKey('wcf', $result);
    }

    public function testLoginFailure()
    {
        $credentials = new Credentials(new Request(), 'live');

        // Panggil metode Login dengan data yang salah
        $postBody = ['nik' => '999999']; // Misalnya, nik yang tidak benar
        $result = $credentials->Login($postBody);

        $this->assertArrayHasKey('wcf', $result);
        $this->assertArrayNotHasKey('token', $result);
    }

    public function testLogout()
    {
        $irkHelpMock = $this->getMockBuilder(IRKHelp::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = new Request();
        $slug = 'dev';
        $credentials = new Credentials($request, $slug);
        $credentials->irkHelp = $irkHelpMock;

        $postbody = ['nik' => '123456'];
        $result = $credentials->Logout($postbody);

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('statuscode', $result);
    }

    public function testGetTokenAuth()
    {
        $irkHelpMock = $this->getMockBuilder(IRKHelp::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = new Request();
        $slug = 'dev';
        $credentials = new Credentials($request, $slug);
        $credentials->irkHelp = $irkHelpMock;
        
        $nik = '123456';
        $result = $credentials->GetTokenAuth($nik);

        $this->assertArrayHasKey('GetTokenForResult', $result);
    }

    public function testValidateTokenAuth()
    {
        $irkHelpMock = $this->getMockBuilder(IRKHelp::class)
        ->disableOriginalConstructor()
        ->getMock();

        $request = new Request();
        $slug = 'dev';
        $credentials = new Credentials($request, $slug);
        $credentials->irkHelp = $irkHelpMock;

        $token = 'example_token';
        $result = $credentials->ValidateTokenAuth($token);

        $this->assertNotNull($result);
    }

    public function testSPExecutor()
    {
        $credentials = new Credentials(new Request(), 'dev');

        // Panggil metode SPExecutor dengan data yang sesuai
        $postBody = ['request' => 'example_request_data'];
        $result = $credentials->SPExecutor($postBody);

        $this->assertNotNull($result);
        // Anda dapat menambahkan lebih banyak verifikasi sesuai dengan kebutuhan
    }

    public function testSPExecutorV2()
    {
        $credentials = new Credentials(new Request(), 'dev');

        // Panggil metode SPExecutorV2 dengan data yang sesuai
        $postBody = ['request' => 'example_request_data'];
        $result = $credentials->SPExecutorV2($postBody);

        $this->assertNotNull($result);
        // Anda dapat menambahkan lebih banyak verifikasi sesuai dengan kebutuhan
    }
}