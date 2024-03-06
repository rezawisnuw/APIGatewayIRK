<?php

namespace App\Http\Middleware;

use Closure;
use App\Helper\IRKHelp;

class TokenAuth
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

            $slug = $request->route('slug');

            $x = $request->route('x');

            $helper = new IRKHelp($request);

            $env = config('app.env');

            $token = $helper->Environment($env);

            if (empty($token['tokenid'])) {
                $this->resultresp = 'Token is Empty';
                $this->dataresp = [];
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
                $modelClass = "App\\Models\\IRK_v{$x}\\CredentialsModel";
                $model = new $modelClass($request, $slug);
                $verifyUser = $model->ValidateTokenAuth($token['tokenid']);

                $utilClass = "App\\Http\\Controllers\\IRK_v{$x}\\UtilityGateway";
                $utility = new $utilClass($request);
                if (isset($request['userid'])) {
                    $hardcode['param'] = ['code' => 1, 'nik' => $request['userid']];
                    $verifyUser_IRK = $utility->WorkerESS($request, $hardcode)->data[0]->isUserIRK;
                } else {
                    $hardcode['param'] = ['code' => 1, 'nik' => $request['data']['nik']];
                    $verifyUser_IRK = $utility->WorkerESS($request, $hardcode)->getData()->data[0]->isUserIRK;
                }

                if ($verifyUser->DecodeResult == 'Cocok') {
                    if ($verifyUser_IRK == 'Active') {
                        return $next($request);
                    } else {
                        $dataresp = 'User ' . $verifyUser_IRK;
                    }
                } else {
                    $dataresp = 'User ' . $verifyUser;
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
