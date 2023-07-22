<?php

namespace App\Http\Controllers\Stag;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\database\QueryException;
use Telegram;
use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Psr7;
use PDF;
use Storage;
use SoapClient;
use App\Models\Stag\Credential;

use DB;
use Validator;

class UtilityGateway extends Controller
{
    public function LoginESS(Request $request){
        $result = '';

        try {
			if (count($request->json()->all())) {
				$postbody = $request->json(['data']);

				$result = Credential::Login($postbody);

				if($result['wcf']['status'] == '1') {
					$param['param'] = ['code' => 1,'nik' => $request['data']['nik'], 'token' => $result['token']];
					if(env('APP_ENV') == 'local'){
						return response()
						//->json(['result' => 'Token has Stored in Header', 'data' => $this->WorkerESS($request, $param), 'message' => $result['wcf']['message'], 'status' => $result['wcf']['status'], 'statuscode' => 200])
						->json(['result' => 'Token has Stored in Header', 'data' => null, 'message' => $result['wcf']['message'], 'status' => $result['wcf']['status'], 'statuscode' => 200])
						->header('Authorization-stag','Bearer'.$result['token'])->send();
					} else{
						return response()
						//->json(['result' => 'Token has Stored in Cookie', 'data' => $this->WorkerESS($request, $param), 'message' => $result['wcf']['message'], 'status' => $result['wcf']['status'], 'statuscode' => 200])
						->json(['result' => 'Token has Stored in Cookie', 'data' => null, 'message' => $result['wcf']['message'], 'status' => $result['wcf']['status'], 'statuscode' => 200])
						->withCookie(cookie('Authorization-stag', 'Bearer'.$result['token'], '120'));
					}
				} else {
					return response()->json($result['wcf']);
				}
				
			} else {
				return response()->json(['result' => 'Request Data is Empty', 'data' => [], 'message' => 'Failed Login', 'status' => 0, 'statuscode' => 400]);
			}
			return response()->json($result);
        } catch (\Throwable $th) {
            return response()->json(['result' => $th->getMessage(), 'data' => [], 'message' => 'Error in Catch', 'status' => 0, 'statuscode' => $th->getCode()]);
        }
    }

	public function LogoutESS(Request $request){
        $result = '';

        try {
			if (count($request->json()->all())) {
				$postbody = $request->json(['data']);

				$result = Credential::Logout($postbody);
				if($result['status'] == '1') return response()->json($result);
				else return response()->json($result, 400);
			} else {
				return response()->json(['result' => 'Request Data is Empty', 'data' => null, 'message' => 'Failed Logout', 'status' => 0, 'statuscode' => 400]);
			}
			return response()->json($result);
        } catch (\Throwable $th) {
            return response()->json(['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch', 'status' => 0, 'statuscode' => $th->getCode()]);
        }
    }

    public function UploadFisik(Request $request) {
		$filePath = $request->input('filepath');
		$namaFile = $request->input('namafile');
		$file = $request->filefisik;
		$extension = $file->extension();
        $mime = $file->getClientMimeType();
        $result = "";
        $postbody='';
        $code =  0;

		$filetypearray = array('image' => $file);

		$rules = array(
			'image' => 'required|max:40000' // max 40MB
		);

		$validator = Validator::make($filetypearray, $rules);

        if ($validator->fails())
        {
			return response()->json([
				'result'  => 'File Rusak dari awal sebelum diuplaod, mohon cek ulang file tersebut !!',
				'data' => null,
				'message' => 'Gagal Upload !',
				'status' => 0,
				'statuscode' => 400
				]
			);
        }else{

            $filedata = array(
                'stream' => curl_file_create($filePath,$mime,$namaFile),
            );

			$client = new \GuzzleHttp\Client();
			$filefisik = ($request->has('filefisik') && $request->filefisik != '') ? $request->file('filefisik') : '';

			$response = $client->request('POST', "http://".config('app.URL_STAG')."/RESTSecurity/RESTSecurity.svc/UploadFileDariInfraKe93?filePath={$filePath}\\{$namaFile}.{$extension}",[

				'multipart' => [
					[
						'name' => 'stream',
						'contents' => file_get_contents($_FILES['filefisik']['tmp_name']),
						'headers'  => ['Content-Type' => $filefisik->getClientMimeType()],
						'filename' => $filefisik->getClientOriginalName()
					]
				],
			]);
			
			$result = json_decode($response->getBody());

			if(str_contains($result, 'Gagal')) {
				return response()->json([
					'result'  => $result,
					'data' => null,
					'message' => ''.$namaFile.' gagal diupload',
					'status' => 0,
					'statuscode' => 400
					]
				);
				
			} else {
				return response()->json([
					'result'  => $result,
					'data' => ''.$namaFile.' sukses diupload',
					'message' => 'Success on Run',
					'status' => '1',
					'statuscode' => 200
					]
				);
			}

        }
    }

	public function UploadBlob(Request $request) {
		$filePath = $request->input('filepath');
		$namaFile = $request->input('namafile');
		$file = $request->filefisik;
		$extension = $file->extension();
        $mime = $file->getClientMimeType();
        $result = "";
        $postbody='';
        $code =  0;

		$filetypearray = array('image' => $file);

		$rules = array(
			'image' => 'mimes:jpeg,jpg,png|required|max:500' // max 1MB
		);

		$validator = Validator::make($filetypearray, $rules);

        if ($validator->fails())
        {
			return response()->json([
				'result'  => 'Validator failed',
				'data' => null,
				'message' => 'Gagal Upload !',
				'status' => 0,
				'statuscode' => 400
				]
			);
        }else{

            $filedata = array(
                'stream' => curl_file_create($filePath,$mime,$namaFile),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://".config('app.URL_STAG')."/RESTSecurity/RESTSecurity.svc/UploadFileBLOBDariInfraKe93?filePath={$filePath}.{$namaFile}.{$extension}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $filedata);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:'.$mime));

            $result = curl_exec($ch);

			return response()->json([
				'result'  => $result,
				'data' => ''.$namaFile.' sukses diupload',
				'message' => 'Success on Run',
				'status' => '1',
				'statuscode' => 200
				]
			);

        }
    }

    public function DownloadFile93(Request $request) {
		if (count($request->json()->all())) {
			$postbody = $request->json(['data']);

			$result = '';

			$client = new Client();
			$response = $client->post(
				'http://'.config('app.URL_STAG').'/RESTSecurity/RESTSecurity.svc/IDM/Public/DownloadFileInfra',
				[
					RequestOptions::JSON =>
					['filePath' => $postbody['filePath']]
				],
				['Content-Type' => 'application/json']
			);
			$body = $response->getBody();
			$temp = json_decode($body);
			$result = json_decode($temp->DownloadFileDariInfraKe93Result);
			return response()->json(['result' => json_decode($result), 'data' => 'Success on Run', 'message' => 'Berhasil Download data', 'status' => '1', 'statuscode' => 200]);
		} else {
			return response()->json(['result' => 'Request Data is Empty', 'data' => null, 'message' => 'Gagal Mengambil data', 'status' => 0, 'statuscode' => 400]);
		}

    }

    public static function Firebase(Request $request) {
        try {
			if (count($request->json()->all())) {
				$postbody = $request->json(['data']);

				$result = '';

				$client = new Client();
				$response = $client->post(
					'http://'.config('app.URL_STAG').'/RESTSecurity/RESTSecurity.svc/IDM/Firebase',
					[
						RequestOptions::JSON =>
						['param' => $postbody]
					],
					['Content-Type' => 'application/json']
				);
				$body = $response->getBody();
				$temp = json_decode($body);
				$result = json_decode($temp->FirebaseResult);
				return response()->json(['result' => $result, 'data' => 'Success on Run', 'message' => 'Berhasil Mengambil data', 'status' => '1', 'statuscode' => 200]);
			} else {
				return response()->json(['result' => 'Request Data is Empty', 'data' => null, 'message' => 'Gagal Mengambil data', 'status' => 0, 'statuscode' => 400]);
			}

        } catch (\Throwable $th) {
            return response()->json(['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch', 'status' => 0, 'statuscode' => $th->getCode()]);
        }
    }

	public static function WorkerESS(Request $request, $param=null){

		if(!isset($request['data']['code']) && $param != null){

			$raw_token = $param['param']['token'];
			$split_token = explode('.', $raw_token);
			$decrypt_token = base64_decode($split_token[1]);
			$escapestring_token = json_decode($decrypt_token);
			
			if($escapestring_token == $param['param']['nik']){ 
				try {
					$client = new Client(); 
					$response = $client->post(
						'http://'.config('app.URL_STAG').'/RESTSecurity/RESTSecurity.svc/IDM/Worker',
						[
							RequestOptions::JSON => 
							['param' => $param['param']]
						],
						['Content-Type' => 'application/json']
					);
					$body = $response->getBody();
					$temp = json_decode($body);
					$result = json_decode($temp->WorkerResult);
					//return $result[0];
					$newdata = array();
					foreach($result as $key=>$value){
			
						if(isset($value->NIK)){
							
							$object = json_decode(json_encode(array('nik' => $value->NIK, 'userid' => $value->NIK, 'code' => 1)));
							
							$client = new Client();
							$response = $client->post(
								'http://'.config('app.URL_GCP_LARAVEL_SERVICELB').'stag/profile/get',
								[
									RequestOptions::JSON =>[
										'data' => $object
									]
								]
							);
	
							$body = $response->getBody();
							
							$temp = json_decode($body);
								
							//$value->ALIAS = !empty($temp->data) ? empty($temp->data[0]->Alias) ? static::EncodeString(new Request(),'Sidomar'.$value->NIK) : $temp->data[0]->Alias : 'Data Corrupt';
							//$value->ALIAS = !empty($temp->data) ? empty($temp->data[0]->Alias) ? substr(base64_encode(microtime().$value->NIK),3,8) : $temp->data[0]->Alias : 'Data Corrupt';
							$value->ALIAS = substr(base64_encode(microtime().$value->NIK),3,8);
                            
                        }else{
                            
                            $value->ALIAS = null;

                        }

						$newjson = new \stdClass();

						$newjson->NIK = Crypt::encryptString($value->NIK);
							$newjson->NAMA = $value->NAMA;
							$newjson->EMAIL = Crypt::encryptString($value->EMAIL);
							$newjson->NOHP_ISAKU = Crypt::encryptString($value->NOHP_ISAKU);
							$newjson->JENIS_KELAMIN = $value->JENIS_KELAMIN;
							$newjson->ALIAS = $value->ALIAS;

						$newdata[] = $newjson;
					}

					return $newdata[0];
				} catch (\Throwable $th) {
					return ['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch' , 'status' => 0, 'statuscode' => $th->getCode()];
				}

			} else {
				return ['result' => 'Your Data Is Not Authorized', 'data' => $escapestring_token, 'message' => 'Bad Request' , 'status' => 0, 'statuscode' => 400];
			}

		} 
		else {
			if(env('APP_ENV') == 'local'){
				$raw_token = str_contains($request->header('Authorization-stag'), 'Bearer') ? 'Authorization-stag=Bearer'.substr($request->header('Authorization-stag'),6) : 'Authorization-stag=Bearer'.$request->header('Authorization-stag');
			} else{
				$raw_token = str_contains($request->cookie('Authorization-stag'), 'Bearer') ? 'Authorization-stag=Bearer'.substr($request->cookie('Authorization-stag'),6) : 'Authorization-stag=Bearer'.$request->cookie('Authorization-stag');
			}
	
			$split_token = explode('.', $raw_token);
			$decrypt_token = base64_decode($split_token[1]);
			$escapestring_token = json_decode($decrypt_token);
			
			if($request['data']['code'] == '1'){
				if($escapestring_token == $request['data']['nik']){ 
					try {
						$client = new Client(); 
						$response = $client->post(
							'http://'.config('app.URL_STAG').'/RESTSecurity/RESTSecurity.svc/IDM/Worker',
							[
								RequestOptions::JSON => 
								['param' => $request['data']]
							],
							['Content-Type' => 'application/json']
						);
						$body = $response->getBody();
						$temp = json_decode($body);
						$result = json_decode($temp->WorkerResult);
						
						$newdata = array();
                        foreach($result as $key=>$value){
							
							if(isset($value->NIK)){
							
								$object = json_decode(json_encode(array('nik' => $value->NIK, 'userid' => $value->NIK, 'code' => 1)));
								
								$client = new Client();
								$response = $client->post(
									'http://'.config('app.URL_GCP_LARAVEL_SERVICELB').'stag/profile/get',
									[
										RequestOptions::JSON =>[
											'data' => $object
										]
									]
								);
		
								$body = $response->getBody();
								
								$temp = json_decode($body);
									
								//$value->ALIAS = !empty($temp->data) ? empty($temp->data[0]->Alias) ? static::EncodeString(new Request(),'Sidomar'.$value->NIK) : $temp->data[0]->Alias : 'Data Corrupt';
								//$value->ALIAS = !empty($temp->data) ? empty($temp->data[0]->Alias) ? substr(base64_encode(microtime().$value->NIK),3,8) : $temp->data[0]->Alias : 'Data Corrupt';
								$value->ALIAS = substr(base64_encode(microtime().$value->NIK),3,8);
								
							}else{
								
								$value->ALIAS = null;
	
							}

							$newjson = new \stdClass();

							$newjson->NIK = Crypt::encryptString($value->NIK);
							$newjson->NAMA = $value->NAMA;
							$newjson->EMAIL = Crypt::encryptString($value->EMAIL);
							$newjson->NOHP_ISAKU = Crypt::encryptString($value->NOHP_ISAKU);
							$newjson->JENIS_KELAMIN = $value->JENIS_KELAMIN;
							$newjson->ALIAS = $value->ALIAS;

							$newdata[] = $newjson;
						}
						//return $result[0];
						return $newdata[0];
					} catch (\Throwable $th) {
						return ['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch' , 'status' => 0, 'statuscode' => $th->getCode()];
					}
				} else {
					return ['result' => 'Your Data Is Not Authorized', 'data' => $escapestring_token, 'message' => 'Bad Request' , 'status' => 0, 'statuscode' => 400];
				}
			}else{
				if($escapestring_token == $request['data']['find']){ 
					try {
						$client = new Client(); 
						$response = $client->post(
							'http://'.config('app.URL_STAG').'/RESTSecurity/RESTSecurity.svc/IDM/Worker',
							[
								RequestOptions::JSON => 
								['param' => $request['data']]
							],
							['Content-Type' => 'application/json']
						);
						$body = $response->getBody();
						$temp = json_decode($body);
						$result = json_decode($temp->WorkerResult);
						
						$newdata = array();
                        foreach($result as $key=>$value){
							
							if(isset($value->NIK)){
							
								$object = json_decode(json_encode(array('nik' => $value->NIK, 'userid' => $value->NIK, 'code' => 1)));
								
								$client = new Client();
								$response = $client->post(
									'http://'.config('app.URL_GCP_LARAVEL_SERVICELB').'stag/profile/get',
									[
										RequestOptions::JSON =>[
											'data' => $object
										]
									]
								);
		
								$body = $response->getBody();
								
								$temp = json_decode($body);
									
								//$value->ALIAS = !empty($temp->data) ? empty($temp->data[0]->Alias) ? static::EncodeString(new Request(),'Sidomar'.$value->NIK) : $temp->data[0]->Alias : 'Data Corrupt';
								//$value->ALIAS = !empty($temp->data) ? empty($temp->data[0]->Alias) ? substr(base64_encode(microtime().$value->NIK),3,8) : $temp->data[0]->Alias : 'Data Corrupt';
								$value->ALIAS = substr(base64_encode(microtime().$value->NIK),3,8);
								
							}else{
								
								$value->ALIAS = null;
	
							}

							$newjson = new \stdClass();

							$newjson->NIK = Crypt::encryptString($value->NIK);
							$newjson->NAMA = $value->NAMA;
							$newjson->EMAIL = Crypt::encryptString($value->EMAIL);
							$newjson->NOHP_ISAKU = Crypt::encryptString($value->NOHP_ISAKU);
							$newjson->JENIS_KELAMIN = $value->JENIS_KELAMIN;
							$newjson->ALIAS = $value->ALIAS;

							$newdata[] = $newjson;
						}
						//return $result[0];
						return $newdata[0];
						
					} catch (\Throwable $th) {
						return ['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch' , 'status' => 0, 'statuscode' => $th->getCode()];
					}
				} else {
					return ['result' => 'Your Data Is Not Authorized', 'data' => $escapestring_token, 'message' => 'Bad Request' , 'status' => 0, 'statuscode' => 400];
				}
			}

		}  
    }

	public static function EncodeString(Request $request, $str) { //this function cannot be recover to the original
		$encoded_text = '';

		if(empty($request->all()) && $str != null){
			for ($i = 0; $i < strlen($str); $i++) {
				$ascii_code = ord(substr($str, $i, 1));
				if (ctype_upper(substr($str, $i, 1))) {
					// uppercase letter
					$encoded_text .= chr(rand(65, 90));
				} elseif (ctype_lower(substr($str, $i, 1))) {
					// lowercase letter
					$encoded_text .= chr(rand(97, 122));
				} else {
					// non-letter character
					$encoded_text .= rand(0, 9);
				}
			}
		}else {
			if(env('APP_ENV') == 'local'){
				$raw_token = str_contains($request->header('Authorization-stag'), 'Bearer') ? 'Authorization-stag=Bearer'.substr($request->header('Authorization-stag'),6) : 'Authorization-stag=Bearer'.$request->header('Authorization-stag');
			} else{
				$raw_token = str_contains($request->cookie('Authorization-stag'), 'Bearer') ? 'Authorization-stag=Bearer'.substr($request->cookie('Authorization-stag'),6) : 'Authorization-stag=Bearer'.$request->cookie('Authorization-stag');
			}

			$split_token = explode('.', $raw_token);
			$decrypt_token = base64_decode($split_token[1]);
			$escapestring_token = json_decode($decrypt_token);

			if($escapestring_token == $request['nik']){ 
				for ($i = 0; $i < strlen($str); $i++) {
					$ascii_code = ord(substr($str, $i, 1));
					if (ctype_upper(substr($str, $i, 1))) {
						// uppercase letter
						$encoded_text .= chr(rand(65, 90));
					} elseif (ctype_lower(substr($str, $i, 1))) {
						// lowercase letter
						$encoded_text .= chr(rand(97, 122));
					} else {
						// non-letter character
						$encoded_text .= rand(0, 9);
					}
				}
			}else {
				return ['result' => 'Your Data Is Not Authorized', 'data' => $escapestring_token, 'message' => 'Bad Request' , 'status' => 0, 'statuscode' => 400];
			}
		
		}

		return substr(str_shuffle($encoded_text), 0, 8);
	}
}


