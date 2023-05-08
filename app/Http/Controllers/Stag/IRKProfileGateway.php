<?php

namespace App\Http\Controllers\Stag;

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Maatwebsite\Excel\Facades\Excel;

class IRKProfileGateway extends Controller
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
                $raw_token = str_contains($data->header('Authorization-stag'), 'Bearer') ? 'Authorization-stag=Bearer'.substr($data->header('Authorization-stag'),6) : 'Authorization-stag=Bearer'.$data->header('Authorization-stag');
            }else{
                $raw_token = str_contains($data->cookie('Authorization-stag'), 'Bearer') ? 'Authorization-stag=Bearer'.substr($data->cookie('Authorization-stag'),6) : 'Authorization-stag=Bearer'.$data->cookie('Authorization-stag');
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
        if(env('APP_ENV') == 'local'){
            if ($param == 'infra') {
                return new Client(
                    [
                        'base_uri' => config('app.URL_12_LARAVEL'),
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json'
                        ],
                        'verify' => false
                    ]
                );
            }else if ($param == 'gcp') {
                return new Client(
                    [
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICELB'),
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json'
                        ],
                        'verify' => false
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
                        ],
                        'verify' => false
                    ]
                );
            }else if ($param == 'toverify_gcp') {
                return new Client(
                    [
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICELB'),
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json',
                            'Cookie' => 'Authorization=' . FacadesRequest::cookie('Authorization')
                        ],
                        'verify' => false
                    ]
                );
            }else{
                return new Client(
                    [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json'
                        ],
                        'verify' => false
                    ]
                );
            }
        }
        else{
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
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICELB'),
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
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICELB'),
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
        
    }
    
    public function get(Request $request){
        try {
            
            if($this->userValid($request)->getData()->result == 'Match'){
                $response = (new self)->client('toverify_gcp')->request('POST', 'stag/profile/get', [
                    'json'=>[
                        'data' => $request->all()
                    ]
                ]);
    
                $result = json_decode($response->getBody()->getContents());
    
                if(!empty($result->data)){
                    $newdata = array();

                    foreach($result->data as $key=>$value){

                        if(!empty($value->Photo) && str_contains($value->Photo,'Stag/Ceritakita/Profile/')){
                            $client = (env('APP_ENV') == 'local') ? new Client(['verify' => false]) : new Client();
                            $response = $client->request('POST',
                                    'https://cloud.hrindomaret.com/api/irk/generateurl',
                                    [
                                        'json' => [
                                            'file_name' => $value->Photo,
                                            'expired' => 30
                                        ]
                                    ]
                                );
    
                            $body = $response->getBody();
                            
                            $temp = json_decode($body);

                            $value->Photo_Cloud = $temp->status == 1 ? $temp->url : 'Corrupt';
                            
                        }else{
                            
                            $value->Photo_Cloud = 'File not found';

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
                if(!empty($request->photo)){
                    $response = (new self)->client('toverify_gcp')->request('POST', 'stag/profile/post', [
                        'multipart'=>[
                            [
                                'name' => 'data',
                                'contents' => json_encode($request->all())
                            ],
                            [
                                'name'     => 'file',
                                'contents' => json_encode(base64_encode(file_get_contents($request->photo)))
                            ]
                        ]
                    ]);
        
                    $result = json_decode($response->getBody()->getContents());
    
                    if(!empty($result->data)){
                        $response = (new self)->client('')->request('POST', 'https://cloud.hrindomaret.com/api/irk/upload', [
                            'multipart' => [
                                [
                                    'name' => 'file',
                                    'contents' => file_get_contents($request->photo),
                                    'headers' => ['Content_type' => $request->photo->getClientMimeType()],
                                    'filename' => $request->photo->getClientOriginalName()
                                ],
                                [
                                    'name' => 'file_name',
                                    'contents' => $result->data
                                ]
                            ]
                        ]);
            
                        $resultcloud = json_decode($response->getBody()->getContents());

                        return $this->successRes($resultcloud, $resultcloud->message, $response->getStatusCode());
                    } else {
                        return response()->json([
                            'result' => null,
                            'data' => $result,
                            'message' => 'Data is Empty',
                            'status' => 0,
                            'statuscode' => $response->getStatusCode()
                        ]);
                    }
                }else{
                    $response = (new self)->client('toverify_gcp')->request('POST', 'stag/profile/post', [
                        'multipart'=>[
                            [
                                'name' => 'data',
                                'contents' => json_encode($request->all())
                            ]
                        ]
                    ]);

                    $result = json_decode($response->getBody()->getContents());

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
        try {
            
            if($this->userValid($request)->getData()->result == 'Match'){
                if(!empty($request->photo)){
                    $response = (new self)->client('toverify_gcp')->request('POST', 'stag/profile/put', [
                        'multipart'=>[
                            [
                                'name' => 'data',
                                'contents' => json_encode($request->all())
                            ],
                            [
                                'name'     => 'file',
                                'contents' => json_encode(base64_encode(file_get_contents($request->photo)))
                            ]
                        ]
                    ]);
        
                    $result = json_decode($response->getBody()->getContents());
    
                    if(!empty($result->data)){
                        $response = (new self)->client('')->request('POST', 'https://cloud.hrindomaret.com/api/irk/upload', [
                            'multipart' => [
                                [
                                    'name' => 'file',
                                    'contents' => file_get_contents($request->photo),
                                    'headers' => ['Content_type' => $request->photo->getClientMimeType()],
                                    'filename' => $request->photo->getClientOriginalName()
                                ],
                                [
                                    'name' => 'file_name',
                                    'contents' => $result->data
                                ]
                            ]
                        ]);
            
                        $resultcloud = json_decode($response->getBody()->getContents());

                        return $this->successRes($resultcloud, $resultcloud->message, $response->getStatusCode());
                    } else {
                        return response()->json([
                            'result' => null,
                            'data' => $result,
                            'message' => 'Data is Empty',
                            'status' => 0,
                            'statuscode' => $response->getStatusCode()
                        ]);
                    }
                }else{
                    $response = (new self)->client('toverify_gcp')->request('POST', 'stag/profile/put', [
                        'multipart'=>[
                            [
                                'name' => 'data',
                                'contents' => json_encode($request->all())
                            ]
                        ]
                    ]);

                    $result = json_decode($response->getBody()->getContents());

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

    public function delete(Request $request){
        
    }
}