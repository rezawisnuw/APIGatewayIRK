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
            return response()->json($token);
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

            } else {
                $modelClass = "App\\Models\\IRK_v{$x}\\CredentialsModel";
                $model = new $modelClass($request, $slug);
                $verifyUser = $model->ValidateTokenAuth($token['tokenid']);

                if ($verifyUser->DecodeResult == 'Cocok' && ($token['platformid'] == 'mobile' || $token['platformid'] == 'website')) {
                   
                    return $next($request);

                } else {

                    $this->resultresp = 'Token & Signature Invalid';
                    $this->dataresp = $verifyUser;
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
