<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Model\Credential\Stag\Credential;

class TokenVerifyStag
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		
		try{
			//$token = str_contains($request->header('Authorization'), 'Bearer') ? substr($request->header('Authorization'),6) : $request->header('Authorization');
            $token = str_contains($request->cookie('Authorization'), 'Bearer') ? substr($request->cookie('Authorization'),6) : $request->cookie('Authorization');

            if (empty($token)) {
                return response()->json([
					'Message' => 'Token Required !!!!',
					'Code' => 400
				]);
            }else{
                $verify = Credential::ValidateTokenAuth($token);
                if($verify->DecodeTokenStagResult != 'Cocok'){
                    return response()->json([
                        'Message' => 'Token & Signature Invalid !!!!',
                        'Code' => 400
                    ]);
                }
                // else{
                //     // return response()->json([
                //     //     'Message' => 'Token Signature Verified', 
                //     //     'Code' => 200]);
                //     return redirect()->route('author.dashboard');
                // }
            }
			
		} catch (\Throwable $th){
			return response()->json(['Message' => 'Process Not Found !!!!' , 'Code' => 400]);
		}

        return $next($request);
    }
}
