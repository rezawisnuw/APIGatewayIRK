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

class IRKCeritaKitaGateway extends Controller
{
    public function successRes($data, $message, $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'result' => 1,
            'data' => $data,
            'message' => $message
        ], $statusCode);
    }

    public function errorRes($message, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'result' => 0,
            'data' => null,
            'message' => $message,
        ], $statusCode);
    }

    public function userValid($data)
    { //sudah konek db nih cuy
        //ssh udah di add juga
        //githubnya
        //ngewe
        if(isset($data->userid)){
            $raw_token = str_contains($data->cookie('Authorization-dev'), 'Bearer') ? 'Authorization-dev=Bearer'.substr($data->cookie('Authorization-dev'),6) : 'Authorization-dev=Bearer'.$data->cookie('Authorization-dev');
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
  
    public function signin(Request $request)
    {
        try {
           
            $response = (new self)->client('gcp')->request('POST', 'auth/dev', [
                'json'=>[
                    'data' => $request->all()
                ]
            ]);
             
            $result = json_decode($response->getBody()->getContents());

            return response()->json($result);

            if (str_contains($result->status,'Success'))
                return $this->successRes('Token has stored in Cookie', $result->message, $response->getStatusCode())->withCookie(cookie('Authorization-dev', 'Bearer'.$result->token, '120', null, 'api.hrindomaret.com', true, true, false, 'none'));
            else
                return $this->errorRes($result->message, $response->getStatusCode());
        } catch (ClientException | ServerException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode((string) $response->getBody());

            if($responseBody == '') return $this->errorRes($e->getMessage(), $response->getStatusCode());
            else return $this->errorRes($responseBody->message, $response->getStatusCode());
        } catch (\Throwable $e) {
            return $this->errorRes($e->getMessage());
        }
    }

    public function signout(Request $request)
    {
        try {
            
            if($this->userValid($request)->getData()->message == 'Match'){
                $response = (new self)->client('toverify_gcp')->request('POST', 'auth/dev', [
                    'json'=>[
                        'data' => $request->all()
                    ]
                ]);
    
                $result = json_decode($response->getBody()->getContents());
    
                return $this->successRes('Token has removed in Cookie', $result->message, $response->getStatusCode());
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

    public function auth(Request $request)
    {
        try {
            if($this->userValid($request)->getData()->message == 'Match'){
                $response = (new self)->client('toverify_gcp')->request('POST', 'auth/dev', [
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
}