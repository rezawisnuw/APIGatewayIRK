<?php

namespace App\Http\Controllers\Live;

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Maatwebsite\Excel\Facades\Excel;

class IRKCurhatkuGateway extends Controller
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
                $raw_token = str_contains($data->header('Authorization'), 'Bearer') ? 'Authorization=Bearer'.substr($data->header('Authorization'),6) : 'Authorization=Bearer'.$data->header('Authorization');
            }else{
                $raw_token = str_contains($data->cookie('Authorization'), 'Bearer') ? 'Authorization=Bearer'.substr($data->cookie('Authorization'),6) : 'Authorization=Bearer'.$data->cookie('Authorization');
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
                        'Cookie' => 'Authorization=' . FacadesRequest::cookie('Authorization')
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
                        'Cookie' => 'Authorization=' . FacadesRequest::cookie('Authorization')
                    ]
                ]
            );
        }else{
            return new Client(
                [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-type' => 'application/json'
                    ]
                ]
            );
        }
    }
    
    public function get(Request $request){
        try {
            
            if($this->userValid($request)->getData()->result == 'Match'){
                $response = (new self)->client('toverify_gcp')->request('POST', 'live/curhatku/get', [
                    'json'=>[
                        'data' => $request->all()
                    ]
                ]);
    
                $result = json_decode($response->getBody()->getContents());
    
                if(!empty($result->data)){

                    $newdata = array();

                    foreach($result->data as $key=>$value){

                        if(!empty($result->data[0]->Gambar) && str_contains($result->data[0]->Gambar,'Dev/Ceritakita')){
                            $client = new Client();
                            $response = $client->request('GET',
                                    'https://cloud.hrindomaret.com/api/irk/download',
                                    [
                                        'query' => [
                                            'file_name' => $result->data[0]->Gambar
                                        ]
                                    ]
                                );
    
                            $body = $response->getBody();
                            
                            $temp = json_decode($body);

                            $value->Gambar_Cloud = $temp->data->encoded_file;
                            
                        }else{
                            
                            $value->Gambar_Cloud = 'File not found';

                        }
                            
                        $newdata[] = $value;
                    }
                    return $this->successRes($newdata, $result->message, $response->getStatusCode());
                } else{
                    return response()->json([
                        'result' => null,
                        'data' => $result,
                        'message' => 'Data is Empty',
                        'status' => 0,
                        'statuscode' => $response->getStatusCode()
                    ]);
                }
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
        try {
            
            if($this->userValid($request)->getData()->result == 'Match'){
                $response = (new self)->client('toverify_gcp')->request('POST', 'live/curhatku/post', [
                    'multipart'=>[
                        [
                            'name' => 'data',
                            'contents' => json_encode($request->all())
                        ],
                        [
                            'name'     => 'file',
                            'contents' => json_encode(base64_encode(file_get_contents($request->gambar)))
                        ]
                    ]
                ]);
    
                $result = json_decode($response->getBody()->getContents());
    
                if(!empty($result->data)){
                    $response = (new self)->client('')->request('POST', 'https://cloud.hrindomaret.com/api/irk/upload', [
                        'multipart' => [
                            [
                                'name' => 'file',
                                'contents' => file_get_contents($request->gambar),
                                'headers' => ['Content_type' => $request->gambar->getClientMimeType()],
                                'filename' => $request->gambar->getClientOriginalName()
                            ],
                            [
                                'name' => 'file_name',
                                'contents' => $result->data
                            ]
                        ]
                    ]);
        
                    $result = json_decode($response->getBody()->getContents());
                    return $this->successRes($result, $result->message, $response->getStatusCode());
                } else {
                    return $this->successRes($result->data, $result->message, $response->getStatusCode());
                }
                
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

    public function put(Request $request){
        
    }

    public function delete(Request $request){
        
    }
}