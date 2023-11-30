<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\IRK\Credentials;
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
    private $resultresp;
	private $dataresp;
	private $messageresp;
	private $statusresp;
	private $ttldataresp;
	private $statuscoderesp;

    public function handle($request, Closure $next)
    {
		
		try{
            
            $slug = $request->route('slug');

            $helper = new IRKHelp($request);

            $segment = $helper->Segment($slug);

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

            }else{
                $model = new Credentials($request, $slug);
                
                $verify = $model->ValidateTokenAuth($token['tokenid']);

                if($verify->DecodeResult != 'Cocok'){
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

            }
			
		} catch (\Throwable $th){
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

        return $next($request);
    }
}
