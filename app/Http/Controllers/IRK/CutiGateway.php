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

class CutiGateway extends Controller
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

                $data = $request->all(); // Use $request->all() to get all form data
                $data['nik'] = $request['userid'];
                $code = isset($data['code']) ? $data['code'] : 1; // Default to 1 if 'code' is not provided

                // Set the appropriate query based on the 'code'
                $queries = [
                    1 => [
                        'conn' => 'ESS',
                        'query' => 'SELECT TOP 10 * FROM LVLEAVEEMPLOYEETABLE WITH(NOLOCK) WHERE empleaveid = '.$request['userid'],
                        'process_name' => 'GetDataCuti'
                    ],
                    2 => [
                        'conn' => 'ESS',
                        'query' => 'SELECT TOP 10 * FROM LVLEAVETYPETABLE WITH(NOLOCK)',
                        'process_name' => 'GetJenisCuti'
                    ],
                    3 => [
                        'conn' => 'ESS',
                        'query' => 'SELECT TOP 10 * FROM lvleavemaintenancetable WITH(NOLOCK) WHERE emplid = '.$request['userid'],
                        'process_name' => 'GetSaldoCuti'
                    ],
                ];

                $data['list_query'] = [$queries[$code]]; // Use the selected query based on 'code'
                $SPExecutor = IRKHelp::executeSP($data);
                
                $newData = [];
                if ($data['code'] == 1) {
                    $newData['GetDataCuti'] = isset($SPExecutor->data->GetDataCuti) ? $SPExecutor->data->GetDataCuti : [];
                } elseif ($data['code'] == 2) {
                    $newData['GetJenisCuti'] = isset($SPExecutor->data->GetJenisCuti) ? $SPExecutor->data->GetJenisCuti : [];
                } elseif ($data['code'] == 3) {
                    $newData['GetSaldoCuti'] = isset($SPExecutor->data->GetSaldoCuti) ? $SPExecutor->data->GetSaldoCuti : [];
                } else {
                    // Jika code bukan 1, 2, atau 3, maka tampilkan code 1 sebagai default
                    $newData['GetDataCuti'] = isset($SPExecutor->data->GetDataCuti) ? $SPExecutor->data->GetDataCuti : [];
                }
                
                $this->resultresp = $SPExecutor->result;
                $this->dataresp = reset($newData);
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
