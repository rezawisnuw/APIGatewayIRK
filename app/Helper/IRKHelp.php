<?php

namespace App\Helper;

use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Client;

class IRKHelp
{

    public function __construct(Request $request)
    {
        // Call the parent constructor
        //parent::__construct();

        $this->request = $request;
    }
    public function Segment($slug){

        $setting = [];

        if($slug === 'dev'){
            $setting['authorize'] = 'Authorization-dev';
            $setting['config'] = config('app.URL_DEV');
            $setting['path'] = 'Dev';
        }else if($slug === 'stag'){
            $setting['authorize'] = 'Authorization-stag';
            $setting['config'] = config('app.URL_STAG');
            $setting['path'] = 'Stag';
        }else if($slug === 'live'){
            $setting['authorize'] = 'Authorization';
            $setting['config'] = config('app.URL_LIVE');
            $setting['path'] = 'Live';
        }else{
            $response = $this->RunningResp('Something is wrong with the path of URI segment',null,'Failed on Run',0,'');
 
            $encode = json_encode($response);
            $encrypt = Crypt::encryptString($encode);

            return $encrypt;
        }
        
        return $setting;
    }

    public function Environment($env){
        
        $session = [];

        if($env === 'local'){
            $session['tokendraw'] = str_contains($this->request->header($this->Segment($this->request->route('slug'))['authorize']), 'Bearer') ? $this->Segment($this->request->route('slug'))['authorize'].'=Bearer'.substr($this->request->header($this->Segment($this->request->route('slug'))['authorize']),6) : $this->Segment($this->request->route('slug'))['authorize'].'=Bearer'.$this->request->header($this->Segment($this->request->route('slug'))['authorize']);
            $session['tokenid'] = str_contains($this->request->header($this->Segment($this->request->route('slug'))['authorize']), 'Bearer') ? substr($this->request->header($this->Segment($this->request->route('slug'))['authorize']),6) : $this->request->header($this->Segment($this->request->route('slug'))['authorize']);
        }else{
            $session['tokendraw'] = str_contains($this->request->cookie($this->Segment($this->request->route('slug'))['authorize']), 'Bearer') ? $this->Segment($this->request->route('slug'))['authorize'].'=Bearer'.substr($this->request->cookie($this->Segment($this->request->route('slug'))['authorize']),6) : $this->Segment($this->request->route('slug'))['authorize'].'=Bearer'.$this->request->cookie($this->Segment($this->request->route('slug'))['authorize']);
            $session['tokenid'] = str_contains($this->request->cookie($this->Segment($this->request->route('slug'))['authorize']), 'Bearer') ? substr($this->request->cookie($this->Segment($this->request->route('slug'))['authorize']),6) : $this->request->cookie($this->Segment($this->request->route('slug'))['authorize']);
        }

        return $session;
    }

    public function RunningResp($resultresp, $dataresp, $messageresp, $statusresp, $ttldataresp, $statuscoderesp = Response::HTTP_OK){
        $response = [
            'result' => $resultresp, // hasil respond asli langsung dari program
            'data' => $dataresp, // hasil data program, data kosong bisa null atau []
            'message' => $messageresp, // pesan translate lebih mudah, ex : 'Success on Run' atau 'Failed on Run'
            'status' => $statusresp, // 0 atau 1 (0 gagal, 1 berhasil)
            'statuscode' => $statuscoderesp, // defaultnya 200 atau bisa diganti manual
            'ttldata' => !empty($ttldataresp) ? $ttldataresp : 0,
            'ttlpage' => !empty($ttldataresp) ? fmod($ttldataresp,10) > 0 ? (($ttldataresp-fmod($ttldataresp,10))/10) + 1 : ($ttldataresp/10) + 0 : 0
        ];

        $encode = json_encode($response);
        $encrypt = Crypt::encryptString($encode);
        $decrypt =  Crypt::decryptString($encrypt);
        $decode = json_decode($decrypt);

        return $decode;
    }

    public function ErrorResp($resultresp, $messageresp, $statuscoderesp = Response::HTTP_BAD_REQUEST){
        $response = [
            'result' => $resultresp, // hasil respond asli langsung dari program
            'data' => null,
            'message' => $messageresp, // pesan translate lebih mudah, ex : 'Error in Catch',
            'status' => 0,
            'statuscode' => $statuscoderesp // defaultnya 400 atau bisa diganti manual
        ];

        $encode = json_encode($response);
        $encrypt = Crypt::encryptString($encode);
        $decrypt =  Crypt::decryptString($encrypt);
        $decode = json_decode($decrypt);

        return $decode;
    }

    public function Identifer($datareq)
    { 
        if(isset($datareq->all()['nik'])){
            if($datareq->all()['userid'] ==  $datareq->all()['nik']){
                if(isset($datareq->all()['userid']) && isset($datareq->all()['nik'])){
                    $raw_token = $this->Environment(env('APP_ENV'))['tokendraw'];
                    $split_token = explode('.', $raw_token);
                    $decrypt_token = base64_decode($split_token[1]);
                    $escapestring_token = json_decode($decrypt_token);
                   
                    if($escapestring_token == $datareq->userid && $escapestring_token == $datareq->nik){       
                        $response = $this->RunningResp('Match',null,'Success on Run',1,'');
                        $encode = json_encode($response);
                        $encrypt = Crypt::encryptString($encode);
                        return $encrypt;
                    }else{
                        $response = $this->RunningResp('Your data is not verified',null,'Failed on Run',0,'');
                        $encode = json_encode($response);
                        $encrypt = Crypt::encryptString($encode);
                        return $encrypt;
                    }
                }else{
                    $response = $this->RunningResp('User is not match',null,'Failed on Run',0,'');
                    $encode = json_encode($response);
                    $encrypt = Crypt::encryptString($encode);
                    return $encrypt;
                }
            }else{
                $response = $this->RunningResp('Data is not relevant',null,'Failed on Run',0,'');
                $encode = json_encode($response);
                $encrypt = Crypt::encryptString($encode);
                return $encrypt;
            }
        }else{
            if(isset($datareq->all()['userid'])){
                $raw_token = $this->Environment(env('APP_ENV'))['tokendraw'];
                $split_token = explode('.', $raw_token);
                $decrypt_token = base64_decode($split_token[1]);
                $escapestring_token = json_decode($decrypt_token);
               
                if($escapestring_token == $datareq->userid){    
                    $response = $this->RunningResp('Match',null,'Success on Run',1,'');
                    $encode = json_encode($response);
                    $encrypt = Crypt::encryptString($encode);
                    return $encrypt;
                }else{
                    $response = $this->RunningResp('Your data is not verified',null,'Failed on Run',0,'');
                    $encode = json_encode($response);
                    $encrypt = Crypt::encryptString($encode);
                    return $encrypt;
                }
            }else{
                $response = $this->RunningResp('User is not match',null,'Failed on Run',0,'');
                $encode = json_encode($response);
                $encrypt = Crypt::encryptString($encode);
                return $encrypt;
            }
        }
        
    }

    public function Client($param)
    {
        if(env('APP_ENV') === 'local'){
            if ($param == 'gcp') {
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
            }else if ($param == 'toverify_gcp') {
                return new Client(
                    [
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICELB'),
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json',
                            'Cookie' => $this->Segment($this->request->route('slug'))['authorize'].'=' . FacadesRequest::cookie($this->Segment($this->request->route('slug'))['authorize'])
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
            if ($param == 'gcp') {
                return new Client(
                    [
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICELB'),
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json'
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
                            'Cookie' => $this->Segment($this->request->route('slug'))['authorize'].'=' . FacadesRequest::cookie($this->Segment($this->request->route('slug'))['authorize'])
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

}