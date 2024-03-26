<?php

namespace App\Helper;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client;
use Validator;

class IRKHelp
{

    private $request;
    public function __construct(Request $request)
    {
        // Call the parent constructor
        //parent::__construct();

        $this->request = $request;
    }

    public function Segment($slug)
    {

        $setting = [];

        if ($slug == 'dev') {
            $setting['authorize'] = 'Authorization-dev';
            $setting['config'] = config('app.URL_DEV');
            $setting['path'] = 'Dev';
        } else if ($slug == 'stag') {
            $setting['authorize'] = 'Authorization-stag';
            $setting['config'] = config('app.URL_STAG');
            $setting['path'] = 'Stag';
        } else if ($slug == 'live') {
            $setting['authorize'] = 'Authorization';
            $setting['config'] = config('app.URL_LIVE');
            $setting['path'] = 'Live';
        } else {
            $response = $this->RunningResp('Something is wrong with the path of URI segment', [], 'Failed on Run', 0, '');

            $encode = json_encode($response);
            $encrypt = Crypt::encryptString($encode);

            return $encrypt;
        }

        return $setting;
    }

    public function Environment($env)
    {

        $session = [];
        $url = $_SERVER['HTTP_HOST'];
        $info = parse_url($url);

        if ($env === 'local') {

            $host = $info['host'];
            $port = $info['port'];
            $domain = $host;
            $subdomain = $port;

        } else {

            $path = explode(".", $info['path']);
            $domain = "." . $path[1] . "." . $path[2];
            $subdomain = $path[0];

        }

        config(['app.domain' => $domain]);
        config(['app.subdomain' => $subdomain]);

        $session['domain'] = config('app.domain');
        $session['subdomain'] = config('app.subdomain');

        $session['tokendraw'] = str_contains($this->request->cookie($this->Segment($this->request->route('slug'))['authorize']), 'Bearer') ? $this->Segment($this->request->route('slug'))['authorize'] . '=Bearer' . substr($this->request->cookie($this->Segment($this->request->route('slug'))['authorize']), 6) : $this->Segment($this->request->route('slug'))['authorize'] . '=Bearer' . $this->request->cookie($this->Segment($this->request->route('slug'))['authorize']);
        $session['tokenid'] = str_contains($this->request->cookie($this->Segment($this->request->route('slug'))['authorize']), 'Bearer') ? substr($this->request->cookie($this->Segment($this->request->route('slug'))['authorize']), 6) : $this->request->cookie($this->Segment($this->request->route('slug'))['authorize']);

        return $session;
    }

    public function RunningResp($resultresp, $dataresp, $messageresp, $statusresp, $ttldataresp, $statuscoderesp = Response::HTTP_OK)
    {
        if (!empty($ttldataresp)) {
            $response = [
                'result' => $resultresp, // hasil respond asli langsung dari program
                'data' => $dataresp, // hasil data program, data kosong bisa null atau []
                'message' => $messageresp, // pesan translate lebih mudah, ex : 'Success on Run' atau 'Failed on Run'
                'status' => $statusresp, // 0 atau 1 (0 gagal, 1 berhasil)
                'statuscode' => $statuscoderesp, // defaultnya 200 atau bisa diganti manual
                'ttldata' => !empty($ttldataresp) ? $ttldataresp : 0,
                'ttlpage' => !empty($ttldataresp) ? fmod($ttldataresp, 10) > 0 ? (($ttldataresp - fmod($ttldataresp, 10)) / 10) + 1 : ($ttldataresp / 10) + 0 : 0
            ];
        } else {
            $response = [
                'result' => $resultresp, // hasil respond asli langsung dari program
                'data' => $dataresp, // hasil data program, data kosong bisa null atau []
                'message' => $messageresp, // pesan translate lebih mudah, ex : 'Success on Run' atau 'Failed on Run'
                'status' => $statusresp, // 0 atau 1 (0 gagal, 1 berhasil)
                'statuscode' => $statuscoderesp, // defaultnya 200 atau bisa diganti manual
                // 'ttldata' => !empty($ttldataresp) ? $ttldataresp : 0,
                // 'ttlpage' => !empty($ttldataresp) ? fmod($ttldataresp,10) > 0 ? (($ttldataresp-fmod($ttldataresp,10))/10) + 1 : ($ttldataresp/10) + 0 : 0
            ];
        }


        $encode = json_encode($response);
        $encrypt = Crypt::encryptString($encode);
        $decrypt = Crypt::decryptString($encrypt);
        $decode = json_decode($decrypt);

        return $decode;
    }

    public function ErrorResp($resultresp, $messageresp, $statuscoderesp = Response::HTTP_BAD_REQUEST)
    {
        $response = [
            'result' => $resultresp, // hasil respond asli langsung dari program
            'data' => null,
            'message' => $messageresp, // pesan translate lebih mudah, ex : 'Error in Catch',
            'status' => 0,
            'statuscode' => ($statuscoderesp == 0) ? $statuscoderesp : 500 // defaultnya 400 atau bisa diganti manual
        ];

        $encode = json_encode($response);
        $encrypt = Crypt::encryptString($encode);
        $decrypt = Crypt::decryptString($encrypt);
        $decode = json_decode($decrypt);

        return $decode;
    }

    public function Identifier($datareq)
    {

        try {
            $raw_token = $this->Environment(env('APP_ENV'))['tokendraw'];
            $split_token = explode('.', $raw_token);
            $decrypt_token = base64_decode($split_token[1]);
            $escapestring_token = json_decode($decrypt_token);

            if (isset($datareq->all()['userid']) && isset($datareq->all()['nik'])) {
                if ($datareq->all()['userid'] == $datareq->all()['nik']) {
                    if ($escapestring_token == $datareq->userid && $escapestring_token == $datareq->nik) {
                        $response = $this->RunningResp('Match', null, 'Success on Run', 1, '');
                        $encode = json_encode($response);
                        $encrypt = Crypt::encryptString($encode);
                        return $encrypt;
                    } else {
                        $response = $this->RunningResp('Your nik is not identified', null, 'Failed on Run', 0, '');
                        $encode = json_encode($response);
                        $encrypt = Crypt::encryptString($encode);
                        return $encrypt;
                    }
                } else {
                    if (isset($datareq->all()['userid'])) {
                        if ($escapestring_token == $datareq->userid) {
                            $response = $this->RunningResp('Match', null, 'Success on Run', 1, '');
                            $encode = json_encode($response);
                            $encrypt = Crypt::encryptString($encode);
                            return $encrypt;
                        } else {
                            $response = $this->RunningResp('Your user is not identified', null, 'Failed on Run', 0, '');
                            $encode = json_encode($response);
                            $encrypt = Crypt::encryptString($encode);
                            return $encrypt;
                        }
                    } else {
                        $response = $this->RunningResp('Data is not relevant', null, 'Failed on Run', 0, '');
                        $encode = json_encode($response);
                        $encrypt = Crypt::encryptString($encode);
                        return $encrypt;
                    }

                }
            } else {
                if (isset($datareq->all()['userid'])) {
                    if ($escapestring_token == $datareq->userid) {
                        $response = $this->RunningResp('Match', null, 'Success on Run', 1, '');
                        $encode = json_encode($response);
                        $encrypt = Crypt::encryptString($encode);
                        return $encrypt;
                    } else {
                        $response = $this->RunningResp('Your data is not identified', null, 'Failed on Run', 0, '');
                        $encode = json_encode($response);
                        $encrypt = Crypt::encryptString($encode);
                        return $encrypt;
                    }
                } else {
                    $response = $this->RunningResp('Data is not relevant', null, 'Failed on Run', 0, '');
                    $encode = json_encode($response);
                    $encrypt = Crypt::encryptString($encode);
                    return $encrypt;
                }
            }

        } catch (\Throwable $e) {
            return $this->ErrorResp($e->getMessage(), 'Error in Catch', $e->getCode());
        }
    }

    public function Client($param)
    {
        if (env('APP_ENV') === 'local') {
            if ($param == 'gcp') {
                return new Client(
                    [
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICE'),
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json'
                        ],
                        'verify' => false
                    ]
                );
            } else if ($param == 'toverify_gcp') {
                return new Client(
                    [
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICE'),
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json',
                            'Cookie' => $this->Segment($this->request->route('slug'))['authorize'] . '=' . $this->request->cookie($this->Segment($this->request->route('slug'))['authorize'])
                        ],
                        'verify' => false
                    ]
                );
            } else {
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
        } else {
            if ($param == 'gcp') {
                return new Client(
                    [
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICE'),
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json'
                        ]
                    ]
                );

            } else if ($param == 'toverify_gcp') {
                return new Client(
                    [
                        'base_uri' => config('app.URL_GCP_LARAVEL_SERVICE'),
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-type' => 'application/json',
                            'Cookie' => $this->Segment($this->request->route('slug'))['authorize'] . '=' . $this->request->cookie($this->Segment($this->request->route('slug'))['authorize'])
                        ]
                    ]
                );
            } else {
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

    public function SPExecutor($param)
    {
        $client = new Client();
        $response = $client->post(
            isset($param['list_sp']) && $param['list_sp'] != null ?
            'http://' . $this->Segment($this->request->route('slug'))['config'] . '/SPExecutor/SpExecutorRest.svc/executev2' :
            'http://' . $this->Segment($this->request->route('slug'))['config'] . '/SPExecutor/SpExecutorRest.svc/executev3',
            [
                'headers' => [
                    'Content-Type' => 'text/plain'
                ],
                'body' => json_encode([
                    'request' => $param
                ])
            ]
        );

        $body = $response->getBody();
        $result = json_decode($body);

        return $result;
    }

    public function Firebase($param)
    {
        $postbody = $param->json(['data']);

        $response = $this->Client('other')->post(
            'http://' . $this->Segment($this->request->route('slug'))['config'] . '/RESTSecurity/RESTSecurity.svc/IDM/Firebase',
            [
                RequestOptions::JSON =>
                    ['param' => $postbody]
            ]
        );

        $body = $response->getBody();
        $temp = json_decode($body);
        $result = json_decode($temp->FirebaseResult);

        return $result;
    }

    public function UploadFisik($request)
    {
        $filePath = $request['filepath'];
        $namaFile = $request['namafile'];
        $file = $request['filefisik'];
        $extension = $file->extension();
        $mime = $file->getClientMimeType();
        $code = 0;

        $filetypearray = array('image' => $file);

        $rules = array(
            'image' => 'required|max:40000' // max 40MB
        );

        $validator = Validator::make($filetypearray, $rules);

        if ($validator->fails()) {
            return $validator;

        } else {

            $filedata = array(
                'stream' => curl_file_create($filePath, $mime, $namaFile),
            );

            $filefisik = ($request->has('filefisik') && $request['filefisik'] != '') ? $request->file('filefisik') : '';

            $response = $this->Client('other')->post(
                "http://" . $this->Segment($this->request->route('slug'))['config'] . "/RESTSecurity/RESTSecurity.svc/UploadFileDariInfraKe93?filePath={$filePath}\\{$namaFile}.{$extension}",
                [
                    'multipart' => [
                        [
                            'name' => 'stream',
                            'contents' => file_get_contents($_FILES['filefisik']['tmp_name']),
                            'headers' => ['Content-Type' => $filefisik->getClientMimeType()],
                            'filename' => $filefisik->getClientOriginalName()
                        ]
                    ],
                ]
            );

            $result = json_decode($response->getBody());

            return $result;

        }
    }

    public function UploadBlob($request)
    {
        $filePath = $request['filepath'];
        $namaFile = $request['namafile'];
        $file = $request['filefisik'];
        $extension = $file->extension();
        $mime = $file->getClientMimeType();
        $code = 0;

        $filetypearray = array('image' => $file);

        $rules = array(
            'image' => 'mimes:jpeg,jpg,png|required|max:500' // max 1MB
        );

        $validator = Validator::make($filetypearray, $rules);

        if ($validator->fails()) {
            return $validator;
        } else {

            $filedata = array(
                'stream' => curl_file_create($filePath, $mime, $namaFile),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://" . $this->Segment($this->request->route('slug'))['config'] . "/RESTSecurity/RESTSecurity.svc/UploadFileBLOBDariInfraKe93?filePath={$filePath}.{$namaFile}.{$extension}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $filedata);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:' . $mime));

            $result = curl_exec($ch);

            return $result;

        }
    }

    public function DownloadFile($request)
    {

        $postbody = $request->json(['data']);

        $response = $this->Client('other')->post(
            'http://' . $this->Segment($this->request->route('slug'))['config'] . '/RESTSecurity/RESTSecurity.svc/IDM/Public/DownloadFileInfra',
            [
                RequestOptions::JSON =>
                    ['filePath' => $postbody['filePath']]
            ]
        );
        $body = $response->getBody();
        $temp = json_decode($body);
        $result = json_decode($temp->DownloadFileDariInfraKe93Result);

        return $result;
    }

}