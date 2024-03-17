<?php

namespace App\Http\Controllers\IRK_v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Helper\IRKHelp;

class IdeakuGateway extends Controller
{
    private $resultresp, $dataresp, $messageresp, $statusresp, $ttldataresp, $statuscoderesp, $base, $path, $helper, $signature;

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
                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/ideaku/get', [
                    'json' => [
                        'data' => $request->all()
                    ]
                ]);

                $result = json_decode($response->getBody()->getContents());

                if (!empty($result->data)) {

                    $userid = $request->userid;
                    $newresponse = $this->helper->Client('toverify_gcp')->request(
                        'POST',
                        $this->base . '/ideaku/get',
                        [
                            'json' => [
                                'data' => [
                                    'userid' => $userid,
                                    'code' => '3'
                                ]
                            ]
                        ]
                    );

                    $newbody = $newresponse->getBody();
                    $newtemp = json_decode($newbody);

                    if ($result->status == 'Processing') {
                        $newdata = array();
                        $format = array("jpeg", "jpg", "png");
                        foreach ($result->data as $value) {
                            $string_array = trim($value->picture, '{}');
                            $array_elements = explode(',', $string_array);
                            $images = array_map('trim', $array_elements);
                            foreach ($images as $key => $image) {
                                if (!empty($image) && str_contains($image, $this->path . '/Ceritakita/Ideaku/') && in_array(explode('.', $image)[1], $format)) {
                                    $cloud = $this->helper->Client('other')->request(
                                        'POST',
                                        'https://cloud.hrindomaret.com/api/irk/generateurl',
                                        [
                                            'json' => [
                                                'file_name' => $image,
                                                'expired' => 30
                                            ]
                                        ]
                                    );

                                    $body = $cloud->getBody();

                                    $temp = json_decode($body);

                                    $picture_cloud[$key] = $temp->status == 1 ? Crypt::encryptString($temp->url) : 'Corrupt';

                                } else {

                                    $picture_cloud = ['File not found'];

                                }

                            }

                            $value->employee = Crypt::encryptString($value->employee);
                            $value->picture_cloud = $picture_cloud;
                            $newdata[] = $value;
                        }

                        $this->resultresp = $result->message;
                        $this->dataresp = $newdata;
                        $this->messageresp = 'Success on Run';
                        $this->statusresp = 1;
                        $this->ttldataresp = $newtemp->data;

                    }

                    $this->resultresp = $result->message;
                    $this->dataresp = $result->data;
                    $this->messageresp = 'Success on Run';
                    $this->statusresp = 1;
                    $this->ttldataresp = $newtemp->data;

                } else {
                    if (count($result->data) < 1) {
                        $this->resultresp = $result->message;
                        $this->dataresp = [];
                        $this->messageresp = 'Success on Run';
                        $this->statusresp = 1;
                    } else {
                        $this->resultresp = $result->message;
                        $this->dataresp = null;
                        $this->messageresp = 'Failed on Run';
                        $this->statusresp = 0;
                    }
                }

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
                if (count($request->file()) > 0) {
                    foreach ($request->file('gambar') as $key => $value) {
                        $filegambar[] = ['filegambar' => base64_encode(file_get_contents($value))];
                    }

                    $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/ideaku/post', [
                        'multipart' => [
                            [
                                'name' => 'data',
                                'contents' => json_encode($request->all())
                            ],
                            [
                                'name' => 'file',
                                'contents' => json_encode($filegambar)
                            ]
                        ]
                    ]);

                    $result = json_decode($response->getBody()->getContents());

                    if (!empty($result->data)) {
                        foreach ($result->data as $key => $value) {
                            $cloud[] = json_decode($this->helper->Client('other')->request('POST', 'https://cloud.hrindomaret.com/api/irk/upload', [
                                'multipart' => [
                                    [
                                        'name' => 'file',
                                        'contents' => base64_encode(file_get_contents($request->gambar[$key])),
                                        'headers' => ['Content_type' => $request->gambar[$key]->getClientMimeType()],
                                        'filename' => $request->gambar[$key]->getClientOriginalName()
                                    ],
                                    [
                                        'name' => 'file_name',
                                        'contents' => $value
                                    ]
                                ]
                            ])->getBody()->getContents());

                            $statuscloud[] = $cloud[$key]->status;
                        }

                        if (in_array(0, $statuscloud)) {
                            $this->resultresp = 'File unsuccessfull uploaded';
                            $this->dataresp = null;
                            $this->messageresp = 'Failed on Run';
                            $this->statusresp = 0;
                        } else {
                            $this->resultresp = 'File successfully uploaded';
                            $this->dataresp = null;
                            $this->messageresp = 'Success on Run';
                            $this->statusresp = 1;
                        }

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

                } else {
                    $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/ideaku/post', [
                        'multipart' => [
                            [
                                'name' => 'data',
                                'contents' => json_encode($request->all())
                            ]
                        ]
                    ]);

                    $result = json_decode($response->getBody()->getContents());

                    if (!empty($result->data)) {

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