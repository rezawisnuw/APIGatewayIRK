<?php

namespace App\Http\Middleware;

use Closure;
use App\Helper\IRKHelp;

class IRKAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $resultresp, $dataresp, $messageresp, $statusresp, $ttldataresp, $statuscoderesp;

    public function handle($request, Closure $next)
    {

        try {

            // $slug = $request->route('slug');

            $x = $request->route('x');

            $helper = new IRKHelp($request);

            // $env = config('app.env');

            // $token = $helper->Environment($env);

            // if (empty($token['tokenid'])) {
            //     $this->resultresp = 'Token is Empty';
            //     $this->dataresp = [];
            //     $this->messageresp = 'Failed on Run';
            //     $this->statusresp = 0;

            //     $running = $helper->RunningResp(
            //         $this->resultresp,
            //         $this->dataresp,
            //         $this->messageresp,
            //         $this->statusresp,
            //         $this->ttldataresp
            //     );

            //     return response()->json($running);

            // } else {
            // $modelClass = "App\\Models\\IRK_v{$x}\\CredentialsModel";
            // $model = new $modelClass($request, $slug);
            // $verifyUser = $model->ValidateTokenAuth($token['tokenid']);

            if ($x == 2) {
                $utilClass = "App\\Http\\Controllers\\IRK_v{$x}\\UtilityGateway";
                $utility = new $utilClass($request);

                if (isset($request['userid'])) {
                    // $hardcode['param'] = ['code' => 1, 'nik' => $request['userid']];
                    $hardcode['param'] = ['code' => 3, 'userid' => $request['userid'], 'karyawan' => $request['userid']];
                    $verifyUser_IRK = $utility->WorkerESS($request, $hardcode)->data->isUserIRK;
                }

                if (isset($request['data'])) {
                    // $hardcode['param'] = ['code' => 1, 'nik' => $request['data']['nik']];
                    $hardcode['param'] = ['code' => 3, 'userid' => $request['data']['nik'], 'karyawan' => $request['data']['nik']];
                    $verifyUser_IRK = $utility->WorkerESS($request, $hardcode)->getData()->data->isUserIRK;
                }

                $uri_path = $_SERVER['REQUEST_URI'];
                $uri_parts = explode('/', $uri_path);
                $request_url = end($uri_parts);

                if ($request_url == 'get') {
                    return $next($request);
                } else {
                    if ($verifyUser_IRK == 'Active') {
                        return $next($request);
                    } else {
                        $dataresp = 'User ' . $verifyUser_IRK;
                    }
                }

                $this->resultresp = 'Token & Signature Invalid';
                $this->dataresp = $dataresp;
                $this->messageresp = 'Failed on Run';
                $this->statusresp = 0;

                $running = $helper->RunningResp(
                    $this->resultresp,
                    $this->dataresp,
                    $this->messageresp,
                    $this->statusresp,
                    $this->ttldataresp
                );

                return response()->json($running);
            } else {
                return $next($request);
            }

        } catch (\Throwable $th) {
            $this->resultresp = $th->getMessage();
            $this->messageresp = 'Error in Catch';
            $this->statuscoderesp = $th->getCode();

            $error = $helper->ErrorResp(
                $this->resultresp,
                $this->messageresp,
                $this->statuscoderesp
            );

            return response()->json($error);
        }

    }
}