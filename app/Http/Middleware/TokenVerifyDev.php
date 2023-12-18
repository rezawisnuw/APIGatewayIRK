<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Dev\Credential;

class TokenVerifyDev
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
            if(env('APP_ENV') == 'local'){
                $token = str_contains($request->header('Authorization-dev'), 'Bearer') ? substr($request->header('Authorization-dev'),6) : $request->header('Authorization-dev');
            } else{
                $token = str_contains($request->cookie('Authorization-dev'), 'Bearer') ? substr($request->cookie('Authorization-dev'),6) : $request->cookie('Authorization-dev');
            }
            
            if (empty($token)) {
                return response()->json([
                    'result' => 'Token is Empty',
                    'data' => null,
					'message' => 'Process Stopped',
                    'status' => 0,
					'statuscode' => 400
				]);
            }else{
                $verify = Credential::ValidateTokenAuth($token);
                if($verify->DecodeResult != 'Cocok'){
                    return response()->json([
                        'result' => 'Token & Signature Invalid',
                        'data' => $verify,
                        'message' => 'Failed Authorized',
                        'status' => 0,
                        'statuscode' => 400
                    ]);
                }
                // else{
                //     // return response()->json([
                //     //     'result' => 'Token & Signature is Valid',
                //     //     'data' => $verify,
                //     //     'message' => 'Success Authorized', 
                //     //     'status' => 0,
                //     //     'statuscode' => 200]);
                //     return redirect()->route('author.dashboard');
                // }
            }
			
		} catch (\Throwable $th){
			return response()->json(['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch' , 'status' => 0, 'statuscode' => $th->getCode()]);
		}
        
        return $next($request);
    }
}
