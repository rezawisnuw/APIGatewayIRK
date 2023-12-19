<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\IRK\CredentialsModel;
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
                $model = new CredentialsModel($request, $slug);

                $verify = $model->ValidateTokenAuth($token['tokenid']);

                if ($verify->DecodeResult != 'Cocok') {
                    $this->resultresp = 'Token & Signature Invalid';
                    $this->dataresp = $verify;
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
