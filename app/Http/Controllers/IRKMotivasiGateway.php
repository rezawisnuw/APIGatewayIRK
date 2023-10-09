<?php

namespace App\Http\Controllers;



use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Crypt;

use Maatwebsite\Excel\Facades\Excel;

use App\Helper\IRKHelp;

class IRKMotivasiGateway extends Controller
{
    private $resultresp;
	private $dataresp;
	private $messageresp;
	private $statusresp;
	private $ttldataresp;
	private $statuscoderesp;

    public function __construct(Request $request)
    {
        // Call the parent constructor
        //parent::__construct();
        
        $slug = $request->route('slug');
		$this->slug = $slug;

        $env = env('APP_ENV');
        $this->env = $env;

        $helper = new IRKHelp($request);
		$this->helper = $helper;

		$segment = $helper->Segment($slug);
		$this->authorize = $segment['authorize'];
		$this->config = $segment['config'];
        $this->path = $segment['path'];

		$idkey = $helper->Environment($env);
		$this->tokenid = $idkey['tokenid'];

        $signature = $helper->Identifer($request);
		$this->signature = $signature;

    }


    public function get(Request $request){
        try {
            $decrypt_signature = Crypt::decryptString($this->signature);
            $decode_signature = json_decode($decrypt_signature);
           
            if($decode_signature->result == 'Match'){
                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->slug.'/motivasi/get', [
                    'json'=>[
                        'data' => $request->all()
                    ]
                ]);

                $result = json_decode($response->getBody()->getContents());
    
                if(!empty($result->data)){
                    $newdata = array();
                    $format = array("jpeg", "jpg", "png");
                    foreach($result->data as $key=>$value){

                        if(!empty($value->picture) && str_contains($value->picture,$this->path.'/Ceritakita/Motivasi/') && in_array(explode('.',$value->picture)[1], $format)){
                            $cloud = $this->helper->Client('other')->request('POST',
                                    'https://cloud.hrindomaret.com/api/irk/generateurl',
                                    [
                                        'json' => [
                                            'file_name' => $value->picture,
                                            'expired' => 30
                                        ]
                                    ]
                                );
    
                            $body = $cloud->getBody();
                            
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
                    $newresponse = $this->helper->Client('toverify_gcp')->request('POST', $this->slug.'/motivasi/get', [
                            'json' => [
                                'data' => [
                                'userid'=> $userid,
                                'code'=>'3'
                                ]
                            ]
                        ]
                    );
            
                    $newbody = $newresponse->getBody();
                    $newtemp = json_decode($newbody);

                    $this->resultresp = $result->message;
                    $this->dataresp = $newdata;
                    $this->messageresp = 'Success on Run';
                    $this->statusresp = 1;
                    $this->ttldataresp = $newtemp->data;

                    $running = $this->helper->RunningResp(
                        $this->resultresp,
                        $this->dataresp,
                        $this->messageresp,
                        $this->statusresp,
                        $this->ttldataresp
                    );
                    
                    return response()->json($running);
                    
                } else{
                    $this->resultresp = $result->message;
                    $this->dataresp = [];
                    $this->messageresp = 'Failed on Run';
                    $this->statusresp = 0;

                    $running = $this->helper->RunningResp(
                        $this->resultresp,
                        $this->dataresp,
                        $this->messageresp,
                        $this->statusresp,
                        $this->ttldataresp
                    );
                    
                    return response()->json($running);
                }   
            }else{
                return $decode_signature;
            }
            
        }catch (\Throwable $e) {
            $this->resultresp = $e->getMessage();
			$this->messageresp = 'Error in Catch';
			$this->statuscoderesp = $e->getCode();

			$error = $this->helper->ErrorResp(
				$this->resultresp, 
				$this->messageresp, 
				$this->statuscoderesp
			);

			return response()->json($error);
        }
    }

    public function post(Request $request){
        try {
            $decrypt_signature = Crypt::decryptString($this->signature);
            $decode_signature = json_decode($decrypt_signature);
           
            if($decode_signature->result == 'Match'){
                if(!empty($request->photo)){
                    $response = $this->helper->Client('toverify_gcp')->request('POST', $this->slug.'/motivasi/post', [
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
                        $cloud = $this->helper->Client('other')->request('POST','https://cloud.hrindomaret.com/api/irk/upload', [
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
            
                        $resultcloud = json_decode($requestcloud->getBody()->getContents());

                        $this->resultresp = $resultcloud->message;
                        $this->dataresp = $resultcloud->data;
                        $this->messageresp = 'Success on Run';
                        $this->statusresp = 1;

                        $running = $this->helper->RunningResp(
                            $this->resultresp,
                            $this->dataresp,
                            $this->messageresp,
                            $this->statusresp,
                            $this->ttldataresp
                        );
                        
                        return response()->json($running);

                    } else {
                        $this->resultresp = $result->message;
                        $this->dataresp = [];
                        $this->messageresp = 'Failed on Run';
                        $this->statusresp = 0;

                        $running = $this->helper->RunningResp(
                            $this->resultresp,
                            $this->dataresp,
                            $this->messageresp,
                            $this->statusresp,
                            $this->ttldataresp
                        );
                        
                        return response()->json($running);
                    }

                }else{
                    $response = $this->helper->Client('toverify_gcp')->request('POST', $this->slug.'/motivasi/post', [
                        'multipart'=>[
                            [
                                'name' => 'data',
                                'contents' => json_encode($request->all())
                            ]
                        ]
                    ]);

                    $result = json_decode($response->getBody()->getContents());

                    if(!empty($result->data)){

                        $this->resultresp = $result->message;
                        $this->dataresp = $result->data;
                        $this->messageresp = 'Success on Run';
                        $this->statusresp = 1;

                        $running = $this->helper->RunningResp(
                            $this->resultresp,
                            $this->dataresp,
                            $this->messageresp,
                            $this->statusresp,
                            $this->ttldataresp
                        );
                        
                        return response()->json($running);

                    } else {
                        $this->resultresp = $result->message;
                        $this->dataresp = [];
                        $this->messageresp = 'Failed on Run';
                        $this->statusresp = 0;

                        $running = $this->helper->RunningResp(
                            $this->resultresp,
                            $this->dataresp,
                            $this->messageresp,
                            $this->statusresp,
                            $this->ttldataresp
                        );
                        
                        return response()->json($running);
                    }
                }
    
            }else{
                return $decode_signature;
            }
            
        }catch (\Throwable $e) {
            $this->resultresp = $e->getMessage();
			$this->messageresp = 'Error in Catch';
			$this->statuscoderesp = $e->getCode();

			$error = $this->helper->ErrorResp(
				$this->resultresp, 
				$this->messageresp, 
				$this->statuscoderesp
			);

			return response()->json($error);
        }
    }

    public function put(Request $request){
        
    }

    public function delete(Request $request){
        
    }

}