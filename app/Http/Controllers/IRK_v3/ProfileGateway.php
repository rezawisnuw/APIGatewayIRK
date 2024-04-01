<?php

namespace App\Http\Controllers\IRK_v3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Helper\IRKHelp;

class ProfileGateway extends Controller
{
    private $resultresp, $dataresp, $messageresp, $statusresp, $ttldataresp, $statuscoderesp, $base, $path, $helper, $signature, $env, $idkey;

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
                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/get', [
                    'json' => [
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
                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/post', [
                    'json' => [
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
        try {
            $decrypt_signature = Crypt::decryptString($this->signature);
            $decode_signature = json_decode($decrypt_signature);

            if ($decode_signature->result == 'Match') {
                $response = $this->helper->Client('toverify_gcp')->request('POST', $this->base . '/profile/put', [
                    'json' => [
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

    public function delete(Request $request)
    {

    }

}