<?php

namespace App\Http\Controllers\IRK;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Helper\IRKHelp;

class PresensiGateway extends Controller
{
    private $resultresp, $dataresp, $messageresp, $statusresp, $ttldataresp, $statuscoderesp, $signature, $helper, $slug, $path;

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
                $tglAwal = $request->input('tglAwal');
                $tglAkhir = $request->input('tglAkhir');

                $param['list_query'] = array([
                    'conn' => 'DBPRESENSI_DUMMY',
                    'query' => "SELECT TOP 10 * FROM PresensiIntegrationOs2 WITH(NOLOCK) WHERE personnelnumber = {$request['userid']} AND DATE BETWEEN '{$tglAwal}' AND '{$tglAkhir}' ORDER BY DATE DESC",
                    'process_name' => 'GetDataPresensi'
                ]);

                $param['nik'] = $request['userid'];
                $response = $this->helper->SPExecutor($param);

                $this->resultresp = 'Data has been processed successfully';
                $this->dataresp = $response->result->GetDataPresensi;
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
        try {
            $decrypt_signature = Crypt::decryptString($this->signature);
            $decode_signature = json_decode($decrypt_signature);

            if ($decode_signature->result == 'Match') {
                $param = $request['data'];

                $param['list_sp'] = array([
                    'conn' => 'DBPRESENSI_DUMMY',
                    'payload' => [
                        'nik' => empty($request['data']['nik']) ? "" : $request['data']['nik'],
                        'tglAbsen' => empty($request['data']['tglAbsen']) ? "" : $request['data']['tglAbsen'],
                        'jamAbsen' => empty($request['data']['jamAbsen']) ? "" : $request['data']['jamAbsen']
                    ],
                    'sp_name' => 'InputPresensiWFH',
                    'process_name' => 'PostDataPresensi'
                ]);

                $response = $this->helper->SPExecutor($param);

                $this->resultresp = 'Data has been processed successfully';
                $this->dataresp = $response->result->PostDataPresensi;
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

    public function put(Request $request)
    {
    }

    public function delete(Request $request)
    {
    }
}
