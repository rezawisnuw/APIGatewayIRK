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
use App\Helper\IRKHelp;
use App\Models\Credentials;

class IzinGateway extends Controller
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

        $signature = $helper->Identifier($request);
		$this->signature = $signature;

    }
    
    public function get(Request $request){
        try {
            $decrypt_signature = Crypt::decryptString($this->signature);
            $decode_signature = json_decode($decrypt_signature);
           
            if($decode_signature->result == 'Match'){
                $data['list_query'] = array([
                	'conn'=>'ESS',
                	'query'=>'SELECT TOP 10 * FROM IDM_LEAVEREQUEST_ESS WITH(NOLOCK) WHERE employeeid = '.$request['userid'],
                	'process_name'=>'GetDataIzin'
                ]);
                
                $data['nik']=$request['userid'];
                $SPExecutor = IRKHelp::executeSP($data);

                $this->resultresp = $SPExecutor->result;
                $this->dataresp = $SPExecutor->data->GetDataIzin;
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
            //Log the exception details for better debugging
            \Log::error("Error in catch block: " . $e->getMessage());
            \Log::error("Exception Trace: " . $e->getTraceAsString());

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

    public function post(Request $request)
    {
}

    public function put(Request $request)
    {

    }

    public function delete(Request $request)
    {

    }
}
