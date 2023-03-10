<?php

namespace App\Models\Dev;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Credential extends Model
{
    
	#DEV
	
    public static function IsTokenSignatureValid(string $token)
    {
        $client = new Client();

		$response = $client->post(
                'http://'.config('app.URL_12_WCF').'/RESTSecurityDev/RESTSecurity.svc/Decode',
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
                'http://'.config('app.URL_12_WCF').'/RESTSecurityDev/RESTSecurity.svc/LoginESSV2',
                //'http://'.config('app.URL_INFRA_LB').':8043/RESTSecurity.svc/LoginESSV2',
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
			return ['wcf' => ['result' => 'Success' , 'message' => 'Berhasil Login', 'status' => '1', 'Code' => 200], 'token' => $token['GetTokenForResult'] ];
        }
        return ['wcf' => ['result' => $result, 'message' => 'Gagal Login', 'status' => '0', 'Code' => 200]];
    }

    public static function Logout($postbody)
    {
        $token = Credential::GetTokenAuth($postbody['nik']);
        
        if($token['GetTokenForResult'] == 'Login failed, No gain access for entry !!!')
            return ['result' => 'Unauthorized request !!!', 'message' => 'Failed', 'status' => '0', 'Code' => 400];
        else 
            return ['result' => $postbody['nik'].' has beer revoked', 'message' => 'Berhasil Logout', 'status' => '1', 'Code' => 200];
    }

    public static function GetTokenAuth(string $nik)
    {
		$client = new Client(); 

        $response = $client->post(
            'http://'.config('app.URL_12_WCF').'/RESTSecurityDev/RESTSecurity.svc/GetTokenFor',
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
            'http://'.config('app.URL_12_WCF').'/RESTSecurityDev/RESTSecurity.svc/Decode',
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