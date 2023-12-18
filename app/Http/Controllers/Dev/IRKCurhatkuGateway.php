<?php

namespace App\Http\Controllers\Dev;

use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;

class IRKCurhatkuGateway extends Controller
{
    public function successRes($data, $message, $ttldata, $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'result' => $message,
            'data' => $data,
            'message' => 'Success on Run',
            'status' => 1,
            'statuscode' => $statusCode,
            'ttldata' => !empty($ttldata) ? $ttldata : 0,
            'ttlpage' => !empty($ttldata) ? fmod($ttldata,10) > 0 ? (($ttldata-fmod($ttldata,10))/10) + 1 : ($ttldata/10) + 0 : 0
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
        if(isset($data->all()['nik'])){
            if($data->all()['userid'] ==  $data->all()['nik']){
                if(isset($data->all()['userid']) && isset($data->all()['nik'])){
                    if(env('APP_ENV') == 'local'){
                        $raw_token = str_contains($data->header('Authorization-dev'), 'Bearer') ? 'Authorization-dev=Bearer'.substr($data->header('Authorization-dev'),6) : 'Authorization-dev=Bearer'.$data->header('Authorization-dev');
                    }else{
                        $raw_token = str_contains($data->cookie('Authorization-dev'), 'Bearer') ? 'Authorization-dev=Bearer'.substr($data->cookie('Authorization-dev'),6) : 'Authorization-dev=Bearer'.$data->cookie('Authorization-dev');
                    }
                    
                    $split_token = explode('.', $raw_token);
                    $decrypt_token = base64_decode($split_token[1]);
                    $escapestring_token = json_decode($decrypt_token);
                   
                    if($escapestring_token == $data->userid && $escapestring_token == $data->nik){       
                        return $this->successRes(null, 'Match', '');
                    }else{
                        return $this->errorRes('Your data is not identified');
                    }
                }else{
                    return $this->errorRes('User not match');
                }
            }else{
                return $this->errorRes('User not relevant');
            }
        }else{
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
                    return $this->successRes(null, 'Match', '');
                }else{
                    return $this->errorRes('Your data is not identified');
                }
            }else{
                return $this->errorRes('User not match');
            }
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
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICE'),
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
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICE'),
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
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICE'),
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
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICE'),
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
                $response = (new self)->client('toverify_gcp')->request('POST', 'dev/curhatku/get', [
                    'json'=>[
                        'data' => $request->all()
                    ]
                ]);
    
                $result = json_decode($response->getBody()->getContents());
    
                if(!empty($result->data)){
                    $newdata = array();
                    $format = array("jpeg", "jpg", "png");
                    foreach($result->data as $key=>$value){

                        if(!empty($value->picture) && str_contains($value->picture,'Dev/Ceritakita/Curhatku/') && in_array(explode('.',$value->picture)[1], $format)){
                            $client = (env('APP_ENV') == 'local') ? new Client(['verify' => false]) : new Client();
                            $response = $client->request('POST',
                                    'https://cloud.hrindomaret.com/api/irk/generateurl',
                                    [
                                        'json' => [
                                            'file_name' => $value->picture,
                                            'expired' => 30
                                        ]
                                    ]
                                );
    
                            $body = $response->getBody();
                            
                            $temp = json_decode($body);

                            $value->picture_cloud = $temp->status == 1 ? Crypt::encryptString($temp->url) : 'Corrupt';
                            
                        }else{
                            
                            $value->picture_cloud = 'File not found';

                        }
                        
                        $value->employee = Crypt::encryptString($value->employee);
                        $value->picture = Crypt::encryptString($value->picture);
                            
                        $newdata[] = $value;
                    }
                    $userid = $request->userid;
                    $newclient = new Client();
                    $newresponse = $newclient->post(
                        'http://'.config('app.URL_GCP_LARAVEL_SERVICE').'dev/curhatku/get',
                        [
                            RequestOptions::JSON => 
                            [
                                'data' => [
                                    'userid'=> $userid,
                                    'code'=>'3'
                                ]
                            ]
                        ],
                            
                        ['Content-Type' => 'application/json']
                    );
            
                    $newbody = $newresponse->getBody();
                    $newtemp = json_decode($newbody);
                    return $this->successRes($newdata, $result->message, $newtemp->data, $response->getStatusCode());
                } else{
                    return response()->json([
                        'result' => 'Data has been process',//$result->message,
                        'data' => [],//$result->data,
                        'message' => 'Success on Run',//$result->status,
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
                if(!empty($request->gambar)){
                    $response = (new self)->client('toverify_gcp')->request('POST', 'dev/curhatku/post', [
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
                        $requestcloud = (new self)->client('')->request('POST', 'https://cloud.hrindomaret.com/api/irk/upload', [
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

                        // $userid = explode("_",$result->data);
                        // $idticket = explode("_",$result->data);
                        // $newclient = new Client();
                        // $newresponse = $newclient->post(
                        //     'http://'.config('app.URL_GCP_LARAVEL_SERVICE').'dev/curhatku/get',
                        //     [
                        //         RequestOptions::JSON => 
                        //         [
                        //             'data' => [
                        //                 'userid'=>substr($userid[0],-10),
                        //                 'code'=>'2',
                        //                 'idticket'=>explode(".",$idticket[1])[0].'-C',
                        //                 'page'=>'0'
                        //             ]
                        //         ]
                        //     ],
                                
                        //     ['Content-Type' => 'application/json']
                        // );
                
                        // $body = $newresponse->getBody();
                        // $temp = json_decode($body);

                        // $newdata = array();
                        // foreach($temp->data as $key=>$value){

                        //     $clientcloud = (env('APP_ENV') == 'local') ? new Client(['verify' => false]) : new Client();
                        //     $responsecloud = $clientcloud->request('POST',
                        //             'https://cloud.hrindomaret.com/api/irk/generateurl',
                        //             [
                        //                 'json' => [
                        //                     'file_name' => $result->data,
                        //                     'expired' => 30
                        //                 ]
                        //             ]
                        //         );
    
                        //     $bodycloud = $responsecloud->getBody();
                            
                        //     $tempcloud = json_decode($bodycloud);

                        //     $value->picture_cloud = $tempcloud->status == 1 ? $tempcloud->url : 'Corrupt';
                                
                        //     $newjson = new \stdClass();

                        //     $newjson->idticket = $value->idticket;
                        //     $newjson->employee = Crypt::encryptString($value->employee);
                        //     $newjson->header = $value->header;
                        //     $newjson->text = $value->text;

                        //     // $substringPicture = substr($value->picture, strrpos($value->picture, '/') + 1);
                        //     // $substringPicture = substr($substringPicture, 0, strpos($substringPicture, '_'));
                        //     // $encodedStringPicture = base64_encode($substringPicture);
                        //     // $newPicture = str_replace($substringPicture, $encodedStringPicture, $value->picture);

                        //     //$newjson->picture = $newPicture;
                        //     $newjson->key = $value->key;
                        //     $newjson->alias = $value->alias;
                        //     $newjson->created = $value->created;
                        //     $newjson->ttlcomment = $value->ttlcomment;
                        //     $newjson->ttllike = $value->ttllike;
                        //     $newjson->likeby = $value->likeby;

                        //     // $substringPictureCloud = substr($value->picture_cloud, strrpos($value->picture_cloud, '/') + 1);
                        //     // $substringPictureCloud = substr($substringPictureCloud, 0, strpos($substringPictureCloud, '_'));
                        //     // $encodedStringPictureCloud = base64_encode($substringPictureCloud);
                        //     // $newPictureCloud = str_replace($substringPictureCloud, $encodedStringPictureCloud, $value->picture_cloud);

                        //     // $newjson->picture_cloud = $newPictureCloud;
                        //     $newjson->picture_cloud = Crypt::encryptString($value->picture_cloud);
                                
                        //     $newdata[] = $newjson;
                        // }
            
                        $resultcloud = json_decode($requestcloud->getBody()->getContents());
                        //return $this->successRes($newdata, $resultcloud->message, $requestcloud->getStatusCode());
                        return $this->successRes($resultcloud->data, $resultcloud->message, $requestcloud->getStatusCode());
                    } else {
                        return response()->json([
                            'result' => $result->message,
                            'data' => $result->data,
                            'message' => $result->status,
                            'status' => 0,
                            'statuscode' => $response->getStatusCode()
                        ]);
                    }

                }else{
                    $response = (new self)->client('toverify_gcp')->request('POST', 'dev/curhatku/post', [
                        'multipart'=>[
                            [
                                'name' => 'data',
                                'contents' => json_encode($request->all())
                            ]
                        ]
                    ]);

                    $result = json_decode($response->getBody()->getContents());

                    if(!empty($result->data)){
                        // $userid = explode("_",$result->data);
                        // $idticket = explode("_",$result->data);
                        // $newclient = new Client();
                        // $newresponse = $newclient->post(
                        //     'http://'.config('app.URL_GCP_LARAVEL_SERVICE').'dev/curhatku/get',
                        //     [
                        //         RequestOptions::JSON => 
                        //         [
                        //             'data' => [
                        //                 'userid'=>substr($userid[0],-10),
                        //                 'code'=>'2',
                        //                 'idticket'=>explode(".",$idticket[1])[0].'-C',
                        //                 'page'=>'0'
                        //             ]
                        //         ]
                        //     ],
                                
                        //     ['Content-Type' => 'application/json']
                        // );
                
                        // $body = $newresponse->getBody();
                        // $temp = json_decode($body);

                        // $newdata = array();
                        // foreach($temp->data as $key=>$value){
                        //     $value->picture_cloud = 'File not found';
                                
                        //     $newjson = new \stdClass();

                        //     $newjson->idticket = $value->idticket;
                        //     $newjson->employee = Crypt::encryptString($value->employee);
                        //     $newjson->header = $value->header;
                        //     $newjson->text = $value->text;

                        //     // $substringPicture = substr($value->picture, strrpos($value->picture, '/') + 1);
                        //     // $substringPicture = substr($substringPicture, 0, strpos($substringPicture, '_'));
                        //     // $encodedStringPicture = base64_encode($substringPicture);
                        //     // $newPicture = str_replace($substringPicture, $encodedStringPicture, $value->picture);

                        //     //$newjson->picture = $newPicture;
                        //     $newjson->key = $value->key;
                        //     $newjson->alias = $value->alias;
                        //     $newjson->created = $value->created;
                        //     $newjson->ttlcomment = $value->ttlcomment;
                        //     $newjson->ttllike = $value->ttllike;
                        //     $newjson->likeby = $value->likeby;
                        //     $newjson->picture_cloud = $value->picture_cloud;
                                
                        //     $newdata[] = $newjson;
                        // }

                        //return $this->successRes($newdata, $result->message, $response->getStatusCode());

                        return $this->successRes(null, $result->message, $response->getStatusCode());

                    } else {
                        return response()->json([
                            'result' => $result->message,
                            'data' => $result->data,
                            'message' => $result->status,
                            'status' => 0,
                            'statuscode' => $response->getStatusCode()
                        ]);
                    }
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