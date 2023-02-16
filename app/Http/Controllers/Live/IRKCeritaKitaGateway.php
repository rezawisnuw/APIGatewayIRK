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

class IRKCeritaKitaGateway extends Controller
{
    private function successRes($result, $message, $statusCode = Response::HTTP_OK)
    {
        return response()->json([
            'status' => 1,
            'result' => $result,
            'message' => $message
        ], $statusCode);
    }

    public function errorRes($message, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'status' => 0,
            'result' => null,
            'message' => $message,
        ], $statusCode);
    }

    public function client()
    {
        //LOGIN
        if (isset($param)) {
            return new Client(
                [
                    'base_uri' => config('app.URL_12_LARAVEL'),
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-type' => 'application/json'
                    ]
                ]
            );
        }
        //AFTER LOGIN
        else {
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
        }
    }
  
    public function login(Request $request)
    {
        try {
            $response = (new self)->client('login')->request('POST', 'loginverif', [
                'json' => $request->all()
            ]);

            $result = json_decode($response->getBody()->getContents());

            if (str_contains($result->status,'Success'))
                return $this->successRes('Token has stored in Cookie', $result->message, $response->getStatusCode())->withCookie(cookie('Authorization', 'Bearer'.$result->token, '120', null, 'api.hrindomaret.com', true, true, false, 'none'));
            else
                return $this->errorRes($result->message, $response->getStatusCode());
        } catch (ClientException | ServerException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode((string) $response->getBody());
            return $this->errorRes($responseBody->message, $response->getStatusCode());
        } catch (\Throwable $e) {
            return $this->errorRes($e->getMessage());
        }
    }

    public function auth(Request $request)
    {
        try {
            if(isset($request->nik)){
                $raw_token = str_contains($request->cookie('Authorization'), 'Bearer') ? 'Authorization=Bearer'.substr($request->cookie('Authorization'),6) : 'Authorization=Bearer'.$request->cookie('Authorization');
                $split_token = explode('.', $raw_token);
                $decrypt_token = base64_decode($split_token[1]);
                $escapestring_token = json_decode($decrypt_token);
    
                if($escapestring_token == $request->nik){
                    $response = (new self)->client()->request('POST', 'auth', [
                        'json' => $request->all()
                    ]);
        
                    $result = json_decode($response->getBody()->getContents());
        
                    return $this->successRes($result->data, $result->message, $response->getStatusCode());
                }else{
                    return $this->errorRes('Your data is not authorized');
                }
            }else{
                return $this->errorRes('Missing Validation Parameter');
            }
            
        } catch (ClientException | ServerException $e) {
            $response = $e->getResponse();
            $responseBody = json_decode((string) $response->getBody());

            return $this->errorRes($responseBody->message, $response->getStatusCode());
        } catch (\Throwable $e) {
            return $this->errorRes($e->getMessage());
        }
    }
}