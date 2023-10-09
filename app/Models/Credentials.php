<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use App\Helper\IRKHelp;

class Credentials extends Model
{
    
    public function __construct(Request $request, $slug)
    {
        // Call the parent constructor
        //parent::__construct();

        $helper = new IRKHelp($request);
        
        $segment = $helper->Segment($slug);
        $this->config = $segment['config'];
    }
	
    public function IsTokenSignatureValid($token)
    {
        $client = new Client();
        
		$response = $client->post(
            'http://'.$this->config.'/RESTSecurity/RESTSecurity.svc/Decode',
            [
                RequestOptions::JSON => 
                ['token'=>$token]
            ],
            ['Content-Type' => 'application/json']
        );

        $body = $response->getBody();
		$temp = json_decode($body);

        return $temp;
    }

	public function Login($postbody)
    {
        $client = new Client();

		$response = $client->post(
            'http://'.$this->config.'/RESTSecurity/RESTSecurity.svc/LoginESSV2',
            [
                RequestOptions::JSON => 
                ['user'=>$postbody]
            ],
            ['Content-Type' => 'application/json']
        );

        $body = $response->getBody();
		$temp = json_decode($body);
		$result = $temp->LoginESSV2Result;

		if($temp->LoginESSV2Result == 'Success' || $temp->LoginESSV2Result == 'Default' ){
            $token = $this->GetTokenAuth($postbody['nik']);
			return ['wcf' => ['result' => $postbody['nik'], 'data' => null, 'message' => 'Success Login', 'status' => '1', 'statuscode' => 200], 'token' => $token['GetTokenForResult']];
        }
        return ['wcf' => ['result' => $result, 'data' => [], 'message' => 'Failed Login', 'status' => '0', 'statuscode' => 400]];
    }

    public function Logout($postbody)
    {
        $token = $this->GetTokenAuth($postbody['nik']);

        if($token['GetTokenForResult'] == 'Login failed, No gain access for entry !!!')
            return ['result' => 'Unauthorized Request', 'data' => null, 'message' => 'Bad Request', 'status' => '0', 'statuscode' => 400];
        else 
            return ['result' => $postbody['nik'], 'data' => null, 'message' => 'Success Logout', 'status' => '1', 'statuscode' => 200];
    }

    public function GetTokenAuth($nik)
    {
		$client = new Client(); 

        $response = $client->post(
            'http://'.$this->config.'/RESTSecurity/RESTSecurity.svc/GetTokenFor',
            [
                RequestOptions::JSON => 
                ['nik' => $nik]
            ],
            ['Content-Type' => 'application/json']
        );

        $responseBody = json_decode($response->getBody(), true);
		
		return $responseBody;
	}

    public function ValidateTokenAuth($token)
    {
        $client = new Client();

		$response = $client->post(
            'http://'.$this->config.'/RESTSecurity/RESTSecurity.svc/Decode',
            [
                RequestOptions::JSON => 
                ['token'=>$token]
            ],
                
            ['Content-Type' => 'application/json']
        );

        $body = $response->getBody();
        $temp = json_decode($body);
        
        return $temp;
    }
}