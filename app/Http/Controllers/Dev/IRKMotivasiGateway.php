<?php

namespace App\Http\Controllers\Dev;

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Maatwebsite\Excel\Facades\Excel;

class IRKMotivasiGateway extends Controller
{
    public function successRes($data, $message, $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'result' => $message,
            'data' => $data,
            'message' => 'Success on Run',
            'status' => 1,
            'statuscode' => $statusCode
        ]);
    }

    public function errorRes($message, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'result' => $message,
            'data' => null,
            'message' => 'Error in Catch',
            'status' => 0,
            'statuscode' => $statusCode
        ]);
    }

    public function userValid($data)
    { 
        if(isset($data->all()['userid'])){
            if(env('APP_ENV') == 'local'){
                $raw_token = str_contains($data->header('Authorization-dev'), 'Bearer') ? 'Authorization-dev=Bearer'.substr($data->header('Authorization-dev'),6) : 'Authorization-dev=Bearer'.$data->header('Authorization-dev');
            }else{
                $raw_token = str_contains($data->cookie('Authorization-dev'), 'Bearer') ? 'Authorization-dev=Bearer'.substr($data->cookie('Authorization-dev'),6) : 'Authorization-dev=Bearer'.$data->cookie('Authorization-dev');
            }
            
            $split_token = explode('.', $raw_token);
            $decrypt_token = base64_decode($split_token[1]);
            $escapestring_token = json_decode($decrypt_token);
           
            if($escapestring_token == $data->userid){    
                return $this->successRes(null, 'Match');
            }else{
                return $this->errorRes('Your data is not verified');
            }
        }else{
            return $this->errorRes('User not match');
        }
    }

    public function client($param)
    {
        
        if ($param == 'infra') {
            return new Client(
                [
                    'base_uri' => config('app.URL_12_LARAVEL'),
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-type' => 'application/json'
                    ]
                ]
            );
        }else if ($param == 'gcp') {
            return new Client(
                [
                    'base_uri' => config('app.URL_GCP_LARAVEL'),
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-type' => 'application/json'
                    ]
                ]
            );
        }else if ($param == 'toverify_infra') {
            return new Client(
                [
                    'base_uri' => config('app.URL_12_LARAVEL'),
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-type' => 'application/json',
                        'Cookie' => 'Authorization-dev=' . FacadesRequest::cookie('Authorization-dev')
                    ]
                ]
            );
        }else if ($param == 'toverify_gcp') {
            return new Client(
                [
                    'base_uri' => config('app.URL_GCP_LARAVEL'),
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-type' => 'application/json',
                        'Cookie' => 'Authorization-dev=' . FacadesRequest::cookie('Authorization-dev')
                    ]
                ]
            );
        }
    }
    
    public function get(Request $request){
        try {
            
            if($this->userValid($request)->getData()->result == 'Match'){
                $response = (new self)->client('toverify_gcp')->request('POST', 'irk/motivasi/get/dev', [
                    'json'=>[
                        'data' => $request->all()
                    ]
                ]);
    
                $result = json_decode($response->getBody()->getContents());
    
                return $this->successRes($result->data, $result->message, $response->getStatusCode());
            }else{
                return $this->userValid($request);
            }
            
        } catch (ClientException | ServerException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode((string) $response->getBody());

            if($responseBody == '') return $this->errorRes($e->getMessage(), $response->getStatusCode());
            else return $this->errorRes($responseBody->message, $response->getStatusCode());
        } catch (\Throwable $e) {
            return $this->errorRes($e->getMessage());
        }
    }

    public function post(Request $request){
        
    }

    public function put(Request $request){
        
    }

    public function delete(Request $request){
        
    }
}