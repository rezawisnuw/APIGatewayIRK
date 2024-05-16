<?php

namespace App\Http\Controllers\IRK_v2;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;
use GuzzleHttp\RequestOptions;
use App\Helper\IRKHelp;

class UtilityGateway extends Controller
{
    private $resultresp, $dataresp, $messageresp, $statusresp, $ttldataresp, $statuscoderesp, $signature, $helper, $base, $path, $config, $tokendraw;

    public function __construct(Request $request)
    {
        // Call the parent constructor
        //parent::__construct();

        $slug = $request->route('slug');
        $x = $request->route('x');
        $this->base = 'v' . $x . '/' . $slug;

        $env = config('app.env');
        $this->env = $env;

        $helper = new IRKHelp($request);
        $this->helper = $helper;

        $segment = $helper->Segment($slug);
        $this->authorize = $segment['authorize'];
        $this->config = $segment['config'];

        $idkey = $helper->Environment($env);
        $this->tokendraw = $idkey['tokendraw'];

    }

    public function UnitCabang(Request $request, $hardcode = null)
    {

        $datareq['userid'] = empty($hardcode) ? $request['data']['nik'] : $hardcode['data']['nik'];
        $newRequest = new Request($datareq);
        $signature = $this->helper->Identifier($newRequest);
        $decrypt_signature = Crypt::decryptString($signature);
        $decode_signature = json_decode($decrypt_signature);

        try {
            if ($decode_signature->result == 'Match') {

                $response = $this->helper->Client('other')->post(
                    'http://' . $this->config . '/RESTSecurity/RESTSecurity.svc/IDM/Unit-Cabang',
                    [
                        RequestOptions::JSON =>
                            ['param' => empty($hardcode) ? $request['data'] : $hardcode['data']]
                    ]
                );

                $body = $response->getBody();
                $temp = json_decode($body);
                $result = json_decode($temp->UnitCabangResult);

                $this->resultresp = 'Data has been process';
                $this->dataresp = $result;
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
                $this->resultresp = 'Your data is not identified';
                $this->dataresp = $decode_signature->result;
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

        } catch (\Throwable $th) {
            $this->resultresp = $th->getMessage();
            $this->messageresp = 'Error in Catch';
            $this->statuscoderesp = $th->getCode();

            $error = $this->helper->ErrorResp(
                $this->resultresp,
                $this->messageresp,
                $this->statuscoderesp
            );

            return response()->json($error);

        }

    }

    public function StrukturKaryawan(Request $request, $hardcode = null)
    {
        $datareq['userid'] = $request['data']['userid'];
        $newRequest = new Request($datareq);
        $signature = $this->helper->Identifier($newRequest);
        $decrypt_signature = Crypt::decryptString($signature);
        $decode_signature = json_decode($decrypt_signature);

        try {
            if ($decode_signature->result == 'Match') {

                $param = $request['data'];

                $param['list_sp'] = array(
                    [
                        'conn' => 'HRD_OPR',
                        'payload' => ['karyawan' => $param['karyawan']],
                        'sp_name' => 'SP_GetStrukturKaryawan',
                        'process_name' => 'GetStrukturKaryawanResult'
                    ]
                );

                $response = $this->helper->SPExecutor($param);

                return response()->json($response);

            } else {
                $this->resultresp = 'Your data is not identified';
                $this->dataresp = $decode_signature->result;
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
        } catch (\Throwable $th) {
            $this->resultresp = $th->getMessage();
            $this->messageresp = 'Error in Catch';
            $this->statuscoderesp = $th->getCode();

            $error = $this->helper->ErrorResp(
                $this->resultresp,
                $this->messageresp,
                $this->statuscoderesp
            );

            return response()->json($error);

        }

    }

    public function Jabatan(Request $request, $hardcode = null)
    {
        $datareq['userid'] = $request['data']['nik'];
        $newRequest = new Request($datareq);
        $signature = $this->helper->Identifier($newRequest);
        $decrypt_signature = Crypt::decryptString($signature);
        $decode_signature = json_decode($decrypt_signature);

        try {
            if ($decode_signature->result == 'Match') {

                $param = $request['data'];

                $param['list_sp'] = array(
                    [
                        'conn' => 'HRD_OPR',
                        'payload' => ['idjabatan' => empty($param['idjabatan']) ? "" : $param['idjabatan']],
                        'sp_name' => 'SP_GetJabatan',
                        'process_name' => 'GetJabatanResult'
                    ]
                );

                $response = $this->helper->SPExecutor($param);

                return response()->json($response);

            } else {
                $this->resultresp = 'Your data is not identified';
                $this->dataresp = $decode_signature->result;
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
        } catch (\Throwable $th) {
            $this->resultresp = $th->getMessage();
            $this->messageresp = 'Error in Catch';
            $this->statuscoderesp = $th->getCode();

            $error = $this->helper->ErrorResp(
                $this->resultresp,
                $this->messageresp,
                $this->statuscoderesp
            );

            return response()->json($error);

        }

    }

    public function PresensiWFH(Request $request, $hardcode = null)
    {
        if (!isset($request['data']['tanggal']) && $hardcode != null) {
            try {
                $param = $hardcode['param'];

                $nik = $param['nik'];

                $tanggal = $param['tanggal'];

                $param['list_sp'] = array(
                    [
                        'conn' => 'PRESENSISHIFT_DMY',
                        'payload' => [
                            'nik' => $nik,
                            'tanggal' => $tanggal,
                        ],
                        'sp_name' => 'SP_GetShiftWFH',
                        'process_name' => 'GetShiftWFHResult'
                    ]
                );

                $response = $this->helper->SPExecutor($param);

                return response()->json($response);

            } catch (\Throwable $th) {
                $this->resultresp = $th->getMessage();
                $this->messageresp = 'Error in Catch';
                $this->statuscoderesp = $th->getCode();

                $error = $this->helper->ErrorResp(
                    $this->resultresp,
                    $this->messageresp,
                    $this->statuscoderesp
                );

                return response()->json($error);

            }
        } else {
            $datareq['userid'] = $request['data']['userid'];
            $newRequest = new Request($datareq);
            $signature = $this->helper->Identifier($newRequest);
            $decrypt_signature = Crypt::decryptString($signature);
            $decode_signature = json_decode($decrypt_signature);

            try {
                if ($decode_signature->result == 'Match') {
                    $param = $request['data'];

                    $nik = $param['userid'];

                    $tanggal = Carbon::now()->toDateString();

                    $param['list_sp'] = array(
                        [
                            'conn' => 'PRESENSISHIFT_DMY',
                            'payload' => [
                                'nik' => $nik,
                                'tanggal' => $tanggal,
                            ],
                            'sp_name' => 'SP_GetShiftWFH',
                            'process_name' => 'GetShiftWFHResult'
                        ]
                    );

                    $response = $this->helper->SPExecutor($param);

                    return response()->json($response);
                } else {
                    $this->resultresp = 'Your data is not identified';
                    $this->dataresp = $decode_signature->result;
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

            } catch (\Throwable $th) {
                $this->resultresp = $th->getMessage();
                $this->messageresp = 'Error in Catch';
                $this->statuscoderesp = $th->getCode();

                $error = $this->helper->ErrorResp(
                    $this->resultresp,
                    $this->messageresp,
                    $this->statuscoderesp
                );

                return response()->json($error);

            }
        }

    }

    public function WorkerESS(Request $request, $hardcode = null)
    {

        if (!isset($request['data']['code']) && $hardcode != null) {

            try {

                if ($hardcode['param']['code'] == 3) {

                    $param = $hardcode['param'];

                    $body['list_sp'] = array(
                        [
                            'conn' => 'HRD_OPR',
                            'payload' => ['karyawan' => $param['karyawan']],
                            'sp_name' => 'SP_GetStrukturKaryawan',
                            'process_name' => 'GetStrukturKaryawanResult'
                        ]
                    );
                    $temp = $this->helper->SPExecutor($body);
                    $result = $temp->result->GetStrukturKaryawanResult[0];
                } else {
                    $response = $this->helper->Client('other')->post(
                        'http://' . $this->config . '/RESTSecurity/RESTSecurity.svc/IDM/Worker',
                        [
                            RequestOptions::JSON =>
                                ['param' => $hardcode['param']]
                        ]
                    );
                    $body = $response->getBody();
                    $temp = json_decode($body);
                    $result = json_decode($temp->WorkerResult);
                }

                if (isset($request['userid'])) { //for middlewware irk authentication
                    $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/get', [
                        'json' => [
                            'data' => ['code' => 1, 'userid' => $request['userid']]
                        ]
                    ]);

                    $body = $response->getBody();

                    $temp = json_decode($body);

                    $result->isUserIRK = $temp->data[0]->is_active;

                    $this->resultresp = 'Data has been process';
                    $this->dataresp = $result;
                    $this->messageresp = 'Success on Run';
                    $this->statusresp = 1;

                    $running = $this->helper->RunningResp(
                        $this->resultresp,
                        $this->dataresp,
                        $this->messageresp,
                        $this->statusresp,
                        $this->ttldataresp
                    );

                    return $running;

                } else {
                    $hardcode_presensi['param'] = ['nik' => $hardcode['param']['code'] == 1 ? $result[0]->NIK : $result->nik, 'tanggal' => Carbon::now()->toDateString()];

                    $shift = $this->PresensiWFH($request, $hardcode_presensi)->getData()->result->GetShiftWFHResult[0];

                    $newdata = array();
                    $newdata['code'] = 1;
                    $newdata['nik'] = $hardcode['param']['code'] == 1 ? $result[0]->NIK : $result->nik;
                    $newdata['nama'] = $hardcode['param']['code'] == 1 ? $result[0]->NAMA : $result->nama;
                    $newdata['nohp'] = $hardcode['param']['code'] == 1 ? $result[0]->NOHP_ISAKU : $result->nomor_telepon;
                    $newdata['alias'] = '';
                    $newdata['email'] = $hardcode['param']['code'] == 1 ? $result[0]->EMAIL : $result->email;
                    $newdata['kelamin'] = $hardcode['param']['code'] == 1 ? $result[0]->JENIS_KELAMIN : $result->jenis_kelamin;
                    $newdata['status'] = '';
                    $newdata['idjabatan'] = $hardcode['param']['code'] == 1 ? $result[0]->KODE_JABATAN : $result->kode_jabatan;
                    $newdata['jabatan'] = $hardcode['param']['code'] == 1 ? $result[0]->JABATAN : $result->jabatan;
                    $newdata['idunit'] = $hardcode['param']['code'] == 1 ? $result[0]->ID_PT : $result->id_pt;
                    $newdata['unit'] = $hardcode['param']['code'] == 1 ? $result[0]->NAMA_PT : $result->nama_pt;
                    $newdata['idcabang'] = $hardcode['param']['code'] == 1 ? $result[0]->ID_CABANG : $result->id_cabang;
                    $newdata['cabang'] = $hardcode['param']['code'] == 1 ? $result[0]->NAMA_CABANG : $result->nama_cabang;
                    $newdata['iddepartemen'] = $hardcode['param']['code'] == 1 ? $result[0]->ID_BAGIAN : $result->id_bagian;
                    $newdata['departemen'] = $hardcode['param']['code'] == 1 ? $result[0]->BAGIAN : $result->bagian;
                    $newdata['iddirektorat'] = $hardcode['param']['code'] == 1 ? '' : $result->grandparent_organization_number;
                    $newdata['direktorat'] = $hardcode['param']['code'] == 1 ? '' : $result->grandparent_organization;
                    $newdata['iddivisi'] = $hardcode['param']['code'] == 1 ? '' : $result->parent_organization_number;
                    $newdata['divisi'] = $hardcode['param']['code'] == 1 ? '' : $result->parent_organization;
                    $newdata['platform'] = 'Mobile';

                    $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/post', [
                        'json' => [
                            'data' => $newdata
                        ]
                    ]);

                    $body = $response->getBody();

                    $temp = json_decode($body);

                    if ($temp->status == 'Processing') {

                        $newdata['alias'] = str_contains($temp->data, 'Admin') ? $temp->data : substr($temp->data, 3, 8);

                        $newdata['userid'] = $newdata['nik'];

                        $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/get', [
                            'json' => [
                                'data' => $newdata
                            ]
                        ]);

                        $body = $response->getBody();

                        $temp = json_decode($body);

                        $newdata['status'] = $temp->data[0]->is_active;

                    } else {

                        return $temp->message . ' ' . $temp->data;
                    }

                    $newjson = new \stdClass();

                    $newjson->nik = Crypt::encryptString($newdata['nik']);
                    $newjson->nama = $newdata['nama'];
                    $newjson->email = Crypt::encryptString($newdata['email']);
                    $newjson->nohp_isaku = Crypt::encryptString($newdata['nohp']);
                    $newjson->jenis_kelamin = $newdata['nik'] == '000001' ? 'PRIA' : ($newdata['nik'] == '000002' ? 'WANITA' : $newdata['kelamin']);
                    $newjson->alias = $newdata['alias'];
                    $newjson->user_irk = $newdata['status'] == 'Active' ? true : false;
                    $newjson->isPresensiAvailable = empty($shift) ? false : ($shift->jenisshift == 'WH' ? true : false);

                    return $newjson;
                }

            } catch (\Throwable $th) {
                $this->resultresp = $th->getMessage();
                $this->messageresp = 'Error in Catch';
                $this->statuscoderesp = $th->getCode();

                $error = $this->helper->ErrorResp(
                    $this->resultresp,
                    $this->messageresp,
                    $this->statuscoderesp
                );

                return $error;
            }
        } else {
            if ($request['data']['code'] == '1') {

                $datareq['userid'] = $request['data']['nik'];
                $newRequest = new Request($datareq);
                $signature = $this->helper->Identifier($newRequest);
                $decrypt_signature = Crypt::decryptString($signature);
                $decode_signature = json_decode($decrypt_signature);

                if ($decode_signature->result == 'Match') {

                    try {
                        $response = $this->helper->Client('other')->post(
                            'http://' . $this->config . '/RESTSecurity/RESTSecurity.svc/IDM/Worker',
                            [
                                RequestOptions::JSON =>
                                    ['param' => $request['data']]
                            ]
                        );
                        $body = $response->getBody();
                        $temp = json_decode($body);
                        $result = json_decode($temp->WorkerResult);

                        if (!empty($hardcode)) { //for middlewware irk authentication

                            $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/get', [
                                'json' => [
                                    'data' => ['code' => 1, 'userid' => $hardcode['param']['userid']]
                                ]
                            ]);

                            $body = $response->getBody();

                            $temp = json_decode($body);

                            $result[0]->isUserIRK = $temp->data[0]->is_active;

                            $this->resultresp = 'Data has been process';
                            $this->dataresp = $result;
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
                            $hardcode_presensi['param'] = ['nik' => $result[0]->NIK, 'tanggal' => Carbon::now()->toDateString()];
                            $shift = $this->PresensiWFH($request, $hardcode_presensi)->getData()->result->GetShiftWFHResult[0];

                            $newdata = array();
                            $newdata['code'] = 1;
                            $newdata['nik'] = $result[0]->NIK;
                            $newdata['nama'] = $result[0]->NAMA;
                            $newdata['nohp'] = $result[0]->NOHP_ISAKU;
                            $newdata['alias'] = '';
                            $newdata['email'] = $result[0]->EMAIL;
                            $newdata['kelamin'] = $result[0]->JENIS_KELAMIN;
                            $newdata['status'] = '';
                            $newdata['idjabatan'] = $result[0]->KODE_JABATAN;
                            $newdata['jabatan'] = $result[0]->JABATAN;
                            $newdata['idunit'] = $result[0]->ID_PT;
                            $newdata['unit'] = $result[0]->NAMA_PT;
                            $newdata['idcabang'] = $result[0]->ID_CABANG;
                            $newdata['cabang'] = $result[0]->NAMA_CABANG;
                            $newdata['iddepartemen'] = $result[0]->ID_BAGIAN;
                            $newdata['departemen'] = $result[0]->BAGIAN;
                            $newdata['iddirektorat'] = '';
                            $newdata['direktorat'] = '';
                            $newdata['iddivisi'] = '';
                            $newdata['divisi'] = '';
                            $newdata['platform'] = 'Website';

                            $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/post', [
                                'json' => [
                                    'data' => $newdata
                                ]
                            ]);

                            $body = $response->getBody();

                            $temp = json_decode($body);

                            if ($temp->status == 'Processing') {

                                $newdata['alias'] = str_contains($temp->data, 'Admin') ? $temp->data : substr($temp->data, 3, 8);

                                $newdata['userid'] = $newdata['nik'];

                                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/get', [
                                    'json' => [
                                        'data' => $newdata
                                    ]
                                ]);

                                $body = $response->getBody();

                                $temp = json_decode($body);

                                $newdata['status'] = $temp->data[0]->is_active;

                            } else {

                                $this->resultresp = $temp->message;
                                $this->dataresp = $temp->data;
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

                            $newjson = new \stdClass();

                            $newjson->nik = Crypt::encryptString($newdata['nik']);
                            $newjson->nama = $newdata['nama'];
                            $newjson->email = Crypt::encryptString($newdata['email']);
                            $newjson->nohp_isaku = Crypt::encryptString($newdata['nohp']);
                            $newjson->jenis_kelamin = $newdata['nik'] == '000001' ? 'PRIA' : ($newdata['nik'] == '000002' ? 'WANITA' : $newdata['kelamin']);
                            $newjson->alias = $newdata['alias'];
                            $newjson->user_irk = $newdata['status'] == 'Active' ? true : false;
                            $newjson->isPresensiAvailable = empty($shift) ? false : ($shift->jenisshift == 'WH' ? true : false);

                            $this->resultresp = 'Data has been process';
                            $this->dataresp = $newjson;
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

                        }

                    } catch (\Throwable $th) {
                        $this->resultresp = $th->getMessage();
                        $this->messageresp = 'Error in Catch';
                        $this->statuscoderesp = $th->getCode();

                        $error = $this->helper->ErrorResp(
                            $this->resultresp,
                            $this->messageresp,
                            $this->statuscoderesp
                        );

                        return response()->json($error);
                    }
                } else {
                    return $decode_signature;
                }
            } else if ($request['data']['code'] == '2') {
                if (isset($request['data']['find'])) {
                    try {
                        $response = $this->helper->Client('other')->post(
                            'http://' . $this->config . '/RESTSecurity/RESTSecurity.svc/IDM/Worker',
                            [
                                RequestOptions::JSON =>
                                    ['param' => $request['data']]
                            ]
                        );
                        $body = $response->getBody();
                        $temp = json_decode($body);
                        $result = json_decode($temp->WorkerResult);

                        $this->resultresp = 'Data has been process';
                        $this->dataresp = $result;
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

                    } catch (\Throwable $th) {
                        $this->resultresp = $th->getMessage();
                        $this->messageresp = 'Error in Catch';
                        $this->statuscoderesp = $th->getCode();

                        $error = $this->helper->ErrorResp(
                            $this->resultresp,
                            $this->messageresp,
                            $this->statuscoderesp
                        );

                        return response()->json($error);

                    }
                } else {
                    $this->resultresp = 'Your data is not verified';
                    $this->dataresp = $request['data'];
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
            } else if ($request['data']['code'] == '3') {
                if (isset($request['data']['karyawan'])) {
                    try {

                        if ($request['data']['userid'] == $request['data']['karyawan']) {
                            $result = $this->StrukturKaryawan($request, '')->getData()->result->GetStrukturKaryawanResult[0];

                            if (!empty($hardcode)) { //for middlewware irk authentication

                                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/get', [
                                    'json' => [
                                        'data' => ['code' => 1, 'userid' => $hardcode['param']['userid']]
                                    ]
                                ]);

                                $body = $response->getBody();

                                $temp = json_decode($body);

                                $result->isUserIRK = $temp->data[0]->is_active;

                                $this->resultresp = 'Data has been process';
                                $this->dataresp = $result;
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
                                $hardcode_presensi['param'] = ['nik' => $result->nik, 'tanggal' => Carbon::now()->toDateString()];
                                $shift = $this->PresensiWFH($request, $hardcode_presensi)->getData()->result->GetShiftWFHResult[0];

                                $newdata = array();
                                $newdata['code'] = 1;
                                $newdata['nik'] = $result->nik;
                                $newdata['nama'] = $result->nama;
                                $newdata['nohp'] = $result->nomor_telepon;
                                $newdata['alias'] = '';
                                $newdata['email'] = $result->email;
                                $newdata['kelamin'] = $result->jenis_kelamin;
                                $newdata['status'] = '';
                                $newdata['idjabatan'] = $result->kode_jabatan;
                                $newdata['jabatan'] = $result->jabatan;
                                $newdata['idunit'] = $result->id_pt;
                                $newdata['unit'] = $result->nama_pt;
                                $newdata['idcabang'] = $result->id_cabang;
                                $newdata['cabang'] = $result->nama_cabang;
                                $newdata['iddepartemen'] = $result->id_bagian;
                                $newdata['departemen'] = $result->bagian;
                                $newdata['iddirektorat'] = $result->grandparent_organization_number;
                                $newdata['direktorat'] = $result->grandparent_organization;
                                $newdata['iddivisi'] = $result->parent_organization_number;
                                $newdata['divisi'] = $result->parent_organization;
                                $newdata['platform'] = 'Website';

                                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/post', [
                                    'json' => [
                                        'data' => $newdata
                                    ]
                                ]);

                                $body = $response->getBody();

                                $temp = json_decode($body);

                                if ($temp->status == 'Processing') {

                                    $newdata['alias'] = str_contains($temp->data, 'Admin') ? $temp->data : substr($temp->data, 3, 8);

                                    $newdata['userid'] = $newdata['nik'];

                                    $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/get', [
                                        'json' => [
                                            'data' => $newdata
                                        ]
                                    ]);

                                    $body = $response->getBody();

                                    $temp = json_decode($body);

                                    $newdata['status'] = $temp->data[0]->is_active;

                                } else {

                                    $this->resultresp = $temp->message;
                                    $this->dataresp = $temp->data;
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

                                $newjson = new \stdClass();

                                $newjson->nik = Crypt::encryptString($newdata['nik']);
                                $newjson->nama = $newdata['nama'];
                                $newjson->email = Crypt::encryptString($newdata['email']);
                                $newjson->nohp_isaku = Crypt::encryptString($newdata['nohp']);
                                $newjson->jenis_kelamin = $newdata['nik'] == '000001' ? 'PRIA' : ($newdata['nik'] == '000002' ? 'WANITA' : $newdata['kelamin']);
                                $newjson->alias = $newdata['alias'];
                                $newjson->user_irk = $newdata['status'] == 'Active' ? true : false;
                                $newjson->isPresensiAvailable = empty($shift) ? false : ($shift->jenisshift == 'WH' ? true : false);

                                $this->resultresp = 'Data has been process';
                                $this->dataresp = $newjson;
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

                            }
                        } else {
                            $result = $this->StrukturKaryawan($request, '')->getData()->result->GetStrukturKaryawanResult;

                            if (count($result) > 0) {
                                $this->resultresp = 'Data has been process';
                                $this->dataresp = $result;
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
                                $this->resultresp = 'Data is cannot be process';
                                $this->dataresp = $result;
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

                    } catch (\Throwable $th) {
                        $this->resultresp = $th->getMessage();
                        $this->messageresp = 'Error in Catch';
                        $this->statuscoderesp = $th->getCode();

                        $error = $this->helper->ErrorResp(
                            $this->resultresp,
                            $this->messageresp,
                            $this->statuscoderesp
                        );

                        return response()->json($error);

                    }
                } else {
                    $this->resultresp = 'Your data is not verified';
                    $this->dataresp = $request['data'];
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
            } else {
                $this->resultresp = 'Process is not found';
                $this->dataresp = $request['data'];
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
    }

    public function FileManager(Request $request)
    {
        $datareq['userid'] = isset($request['userid']) ? $request['userid'] : $request['nik'];
        $newRequest = new Request($datareq);
        $signature = $this->helper->Identifier($newRequest);
        $decrypt_signature = Crypt::decryptString($signature);
        $decode_signature = json_decode($decrypt_signature);

        try {
            if ($decode_signature->result == 'Match') {
                $uri_path = $_SERVER['REQUEST_URI'];
                $uri_parts = explode('/', $uri_path);
                $request_url = end($uri_parts);

                if ($request_url == 'export') {
                    $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/filemanager/get', [
                        'json' => [
                            'data' => $request->all()
                        ]
                    ]);

                    $body = $response->getBody();
                    $temp = json_decode($body);
                    $result = $temp->data;
                } else if ($request_url == 'import') {
                    $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/filemanager/post', [
                        'json' => [
                            'data' => $request->all()
                        ]
                    ]);

                    $body = $response->getBody();
                    $temp = json_decode($body);
                    $result = $temp->data;
                }

                $this->resultresp = 'Data has been process';
                $this->dataresp = $result;
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
                $this->resultresp = 'Your data is not identified';
                $this->dataresp = $decode_signature->result;
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

        } catch (\Throwable $th) {
            $this->resultresp = $th->getMessage();
            $this->messageresp = 'Error in Catch';
            $this->statuscoderesp = $th->getCode();

            $error = $this->helper->ErrorResp(
                $this->resultresp,
                $this->messageresp,
                $this->statuscoderesp
            );

            return response()->json($error);

        }

    }

}