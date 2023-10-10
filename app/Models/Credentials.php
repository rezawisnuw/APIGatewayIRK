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
        try {
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
        } catch (\Throwable $th) {
            throw new Exception('Bad Request'); 
        }
    }

	public function Login($postbody)
    {
        try {
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
        } catch (\Throwable $th) {
            throw new Exception('Bad Request'); 
        }
    }

    public function Logout($postbody)
    {
        try{
            $token = $this->GetTokenAuth($postbody['nik']);

            if($token['GetTokenForResult'] == 'Login failed, No gain access for entry !!!')
                return ['result' => 'Unauthorized Request', 'data' => null, 'message' => 'Bad Request', 'status' => '0', 'statuscode' => 400];
            else 
                return ['result' => $postbody['nik'], 'data' => null, 'message' => 'Success Logout', 'status' => '1', 'statuscode' => 200];
        } catch (\Throwable $th) {
            throw new Exception('Bad Request'); 
        }
    }

    public function SPExecutor($postBody){			 
		
		try {
            $result = '';
			
            $client = new Client(); 
            $response = $client->post(
                'http://'.$this->config.'/SPExecutor/SpExecutorRest.svc/execute'.isset($postBody['request']['list_sp']['query']) && $postBody['request']['list_sp']['query'] != null ? 'v3' : (isset($postBody['request']['list_sp']['sp_name']) && $postBody['request']['list_sp']['sp_name'] != null ? 'v2' : 'v?'), 
                [
                    'headers' => [
                        'Content-Type' => 'text/plain'
                    ],
                    'body' => json_encode(['req' => $postBody])
                ]
            );
			
			$body = $response->getBody();
			$temp = json_decode($body);

			return $temp;
        } catch (\Throwable $th) {
            throw new Exception('Bad Request'); 
        }
    }

    public function GetTokenAuth($nik)
    {
        try{
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
        } catch (\Throwable $th) {
            throw new Exception('Bad Request'); 
        }
	}

    public function ValidateTokenAuth($token)
    {
        try {
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
        } catch (\Throwable $th) {
            throw new Exception('Bad Request'); 
        }
    }
}