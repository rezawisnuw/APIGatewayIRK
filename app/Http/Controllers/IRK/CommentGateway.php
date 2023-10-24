<?php

namespace App\Http\Controllers\IRK;



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

class CommentGateway extends Controller
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

        $env = config('app.env');
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
                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->slug.'/comment/get', [
                    'json'=>[
                        'data' => $request->all()
                    ]
                ]);
    
                $result = json_decode($response->getBody()->getContents());
    
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
                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->slug.'/comment/post', [
                    'json'=>[
                        'data' => $request->all()
                    ]
                ]);
    
                $result = json_decode($response->getBody()->getContents());

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
        try {
            $decrypt_signature = Crypt::decryptString($this->signature);
            $decode_signature = json_decode($decrypt_signature);
           
            if($decode_signature->result == 'Match'){
                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->slug.'/comment/put', [
                    'json'=>[
                        'data' => $request->all()
                    ]
                ]);
    
                $result = json_decode($response->getBody()->getContents());

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

    public function delete(Request $request){
        
    }

}