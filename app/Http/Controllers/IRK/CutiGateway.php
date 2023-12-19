<?php

namespace App\Http\Controllers\IRK;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Helper\IRKHelp;

class CutiGateway extends Controller
{
    private $resultresp, $dataresp, $messageresp, $statusresp, $ttldataresp, $statuscoderesp, $signature, $helper, $slug, $path, $model;

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

    public function get(Request $request)
    {
        try {
            $decrypt_signature = Crypt::decryptString($this->signature);
            $decode_signature = json_decode($decrypt_signature);

            if ($decode_signature->result == 'Match') {

                $param = $request->all(); // Use $request->all() to get all form data
                $param['nik'] = $request['userid'];
                $code = isset($param['code']) ? $param['code'] : 1; // Default to 1 if 'code' is not provided

                // Set the appropriate query based on the 'code'
                $queries = [
                    1 => [
                        'conn' => 'ESS',
                        'query' => 'SELECT TOP 10 * FROM LVLEAVEEMPLOYEETABLE WITH(NOLOCK) WHERE empleaveid = ' . $request['userid'],
                        'process_name' => 'GetDataCuti'
                    ],
                    2 => [
                        'conn' => 'ESS',
                        'query' => 'SELECT TOP 10 * FROM LVLEAVETYPETABLE WITH(NOLOCK)',
                        'process_name' => 'GetJenisCuti'
                    ],
                    3 => [
                        'conn' => 'ESS',
                        'query' => 'SELECT TOP 10 * FROM lvleavemaintenancetable WITH(NOLOCK) WHERE emplid = ' . $request['userid'],
                        'process_name' => 'GetSaldoCuti'
                    ],
                ];

                $param['list_query'] = [$queries[$code]]; // Use the selected query based on 'code'
                $response = $this->helper->SPExecutor($param);

                $newData = [];
                if ($param['code'] == 1) {
                    $newData['GetDataCuti'] = isset($response->result->GetDataCuti) ? $response->result->GetDataCuti : [];
                } elseif ($param['code'] == 2) {
                    $newData['GetJenisCuti'] = isset($response->result->GetJenisCuti) ? $response->result->GetJenisCuti : [];
                } elseif ($param['code'] == 3) {
                    $newData['GetSaldoCuti'] = isset($response->result->GetSaldoCuti) ? $response->result->GetSaldoCuti : [];
                } else {
                    // Jika code bukan 1, 2, atau 3, maka tampilkan code 1 sebagai default
                    $newData['GetDataCuti'] = isset($response->result->GetDataCuti) ? $response->result->GetDataCuti : [];
                }

                $this->resultresp = 'Data has been processed successfully';
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

            } else {
                return $decode_signature;
            }

        } catch (\Throwable $e) {
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
