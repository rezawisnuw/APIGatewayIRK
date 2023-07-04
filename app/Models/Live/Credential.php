<?php

namespace App\Models\Live;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Credential extends Model
{
    
	#LIVE
	
    public static function IsTokenSignatureValid(string $token)
    {
        $client = new Client();
        
		$response = $client->post(
            'http://'.config('app.URL_LIVE').'/RESTSecurity/RESTSecurity.svc/Decode',
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

	public static function Login($postbody)
    {
        $client = new Client();

		$response = $client->post(
            'http://'.config('app.URL_LIVE').'/RESTSecurity/RESTSecurity.svc/LoginESSV2',
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
            $token = Credential::GetTokenAuth($postbody['nik']);
			return ['wcf' => ['result' => $postbody['nik'], 'data' => null, 'message' => 'Success Login', 'status' => '1', 'statuscode' => 200], 'token' => $token['GetTokenForResult']];
        }
        return ['wcf' => ['result' => $result, 'data' => null, 'message' => 'Failed Login', 'status' => '0', 'statuscode' => 400]];
    }

    public static function Logout($postbody)
    {
        $token = Credential::GetTokenAuth($postbody['nik']);

        if($token['GetTokenForResult'] == 'Login failed, No gain access for entry !!!')
            return ['result' => 'Unauthorized Request', 'data' => null, 'message' => 'Bad Request', 'status' => '0', 'statuscode' => 400];
        else 
            return ['result' => $postbody['nik'], 'data' => null, 'message' => 'Success Logout', 'status' => '1', 'statuscode' => 200];
    }

    public static function GetTokenAuth(string $nik)
    {
		$client = new Client(); 

        $response = $client->post(
            'http://'.config('app.URL_LIVE').'/RESTSecurity/RESTSecurity.svc/GetTokenFor',
            [
                RequestOptions::JSON => 
                ['nik' => $nik]
            ],
            ['Content-Type' => 'application/json']
        );

        $responseBody = json_decode($response->getBody(), true);
		
		return $responseBody;
	}

    public static function ValidateTokenAuth(string $token)
    {
        $client = new Client();

		$response = $client->post(
            'http://'.config('app.URL_LIVE').'/RESTSecurity/RESTSecurity.svc/Decode',
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