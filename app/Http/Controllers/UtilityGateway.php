<?php

namespace App\Http\Controllers;

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
use App\Models\Credentials;
use App\Helper\IRKHelp;
use DB;
use Validator;

class UtilityGateway extends Controller
{

	private $resultresp;
	private $dataresp;
	private $messageresp;
	private $statusresp;
	private $ttldataresp;
	private $statuscoderesp;

	public function __construct(Request $request)
    {
        // Call the parent constructor
        //parent::__construct();
        
        $slug = $request->route('slug');
		$this->slug = $slug;

		$env = config('app.env');
        $this->env = $env;

		$model = new Credentials($request, $slug);
        $this->model = $model;

		$helper = new IRKHelp($request);
		$this->helper = $helper;

		$segment = $helper->Segment($slug);
		$this->authorize = $segment['authorize'];
		$this->config = $segment['config'];

		$idkey = $helper->Environment($env);
		$this->tokendraw = $idkey['tokendraw'];

    }

    public function LoginESS(Request $request){

        try {
			if (count($request->json()->all())) {
				$postbody = $request->json(['data']);
	
				$result = $this->model->Login($postbody);
				
				if($result['wcf']['status'] == '1') {

					// $param['param'] = ['code' => 1,'nik' => $request['data']['nik'], 'token' => $result['token']];

					if($this->env === 'local'){

						// return response()
						// ->json(['result' => 'Token has Stored in Header', 'data' => $this->WorkerESS($request, $param), 'message' => $result['wcf']['message'], 'status' => $result['wcf']['status'], 'statuscode' => 200])
						// ->header($this->authorize,'Bearer'.$result['token']);

						$this->resultresp = 'Token has Stored in Header';
						$this->dataresp = null;
						$this->messageresp = $result['wcf']['message'];
						$this->statusresp = $result['wcf']['status'];

						$running = $this->helper->RunningResp(
							$this->resultresp,
							$this->dataresp,
							$this->messageresp,
							$this->statusresp,
							$this->ttldataresp
						);

						return response()->json($running)
						->withHeaders([
							$this->authorize => 'Bearer'.$result['token'],
							'Cache-Control' => 'max-age=7200, public',
							'Expires' => now()->addHours(2)->format('D, d M Y H:i:s \G\M\T'),
						]);
						
					} else{

						// return response()
						// ->json(['result' => 'Token has Stored in Cookie', 'data' => $this->WorkerESS($request, $param), 'message' => $result['wcf']['message'], 'status' => $result['wcf']['status'], 'statuscode' => 200])
						// ->withCookie(cookie($this->authorize, 'Bearer'.$result['token'], '120'));

						$this->resultresp = 'Token has Stored in Cookie';
						$this->dataresp = null;
						$this->messageresp = $result['wcf']['message'];
						$this->statusresp = $result['wcf']['status'];

						$running = $this->helper->RunningResp(
							$this->resultresp,
							$this->dataresp,
							$this->messageresp,
							$this->statusresp,
							$this->ttldataresp
						);

						return response()->json($running)
						->withCookie(cookie($this->authorize, 'Bearer'.$result['token'], '120'))
						->withCookie(cookie('NameEncryption', 'ValueEncryption', '120', '/', env('SESSION_CONFIG_DOMAIN','.hrindomaret.com'), false, false));
						
					}
				} else {

					$this->resultresp = $result['wcf']['result'];
					$this->dataresp = null;
					$this->messageresp = $result['wcf']['message'];
					$this->statusresp = $result['wcf']['status'];

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

				$this->resultresp = 'Request Data is Empty';
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
        } catch (\Throwable $th) {

			$this->resultresp = $th->getMessage();
			$this->messageresp = 'Error in Catch';
			$this->statuscoderesp = $th->getCode();

			$error = $this->helper->ErrorResp(
				$this->resultresp, 
				$this->messageresp, 
				$this->statuscoderesp
			);

			return response()->json($error);

        }
    }

	public function LogoutESS(Request $request){

        try {
			if (count($request->json()->all())) {
				$postbody = $request->json(['data']);

				$result = $this->model->Logout($postbody);

				if($result['status'] == '1'){

					if($this->env === 'local'){

						$this->resultresp = 'Token has Revoked on Header';
						$this->dataresp = null;
						$this->messageresp = $result['message'];
						$this->statusresp = $result['status'];

						$running = $this->helper->RunningResp(
							$this->resultresp,
							$this->dataresp,
							$this->messageresp,
							$this->statusresp,
							$this->ttldataresp
						);

						return response()->json($running);
						
					} else{

						$this->resultresp = 'Token has Revoked on Cookie';
						$this->dataresp = null;
						$this->messageresp = $result['message'];
						$this->statusresp = $result['status'];

						$running = $this->helper->RunningResp(
							$this->resultresp,
							$this->dataresp,
							$this->messageresp,
							$this->statusresp,
							$this->ttldataresp
						);

						return response()->json($running);
						
					}

				}else{

					$this->resultresp = $result['result'];
					$this->dataresp = null;
					$this->messageresp = $result['message'];
					$this->statusresp = $result['status'];

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
				$this->resultresp = 'Request Data is Empty';
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
        } catch (\Throwable $th) {

			$this->resultresp = $th->getMessage();
			$this->messageresp = 'Error in Catch';
			$this->statuscoderesp = $th->getCode();

			$error = $this->helper->ErrorResp(
				$this->resultresp,
				$this->messageresp,
				$this->statuscoderesp
			);

			return response()->json($error);
	
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

			$response = $client->request('POST', "http://".$this->config."/RESTSecurity/RESTSecurity.svc/UploadFileDariInfraKe93?filePath={$filePath}\\{$namaFile}.{$extension}",[

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
            curl_setopt($ch, CURLOPT_URL, "http://".$this->config."/RESTSecurity/RESTSecurity.svc/UploadFileBLOBDariInfraKe93?filePath={$filePath}.{$namaFile}.{$extension}");
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
				'http://'.$this->config.'/RESTSecurity/RESTSecurity.svc/IDM/Public/DownloadFileInfra',
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
			return response()->json(['result' => 'Request Data is Empty', 'data' => [], 'message' => 'Gagal Mengambil data', 'status' => 0, 'statuscode' => 400]);
		}
    }

    public function Firebase(Request $request) {
        try {
			if (count($request->json()->all())) {
				$postbody = $request->json(['data']);

				$result = '';

				$client = new Client();
				$response = $client->post(
					'http://'.$this->config.'/RESTSecurity/RESTSecurity.svc/IDM/Firebase',
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
				return response()->json(['result' => 'Request Data is Empty', 'data' => [], 'message' => 'Gagal Mengambil data', 'status' => 0, 'statuscode' => 400]);
			}

        } catch (\Throwable $th) {
            return response()->json(['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch', 'status' => 0, 'statuscode' => $th->getCode()]);
        }
    }

	public function TransaksiGateway(Request $request) {
		
		$raw_token = $this->tokendraw;
		$split_token = explode('.', $raw_token);
		$decrypt_token = base64_decode($split_token[1]);
		$escapestring_token = json_decode($decrypt_token);
		
			
		if($escapestring_token == $request['nik']){
			try{		
				$result = Credentials::SPExecutor($request['data']);

				$this->resultresp = $result['status'] == 1 ? 'Data has been process' : 'Data cannot be process';
				$this->dataresp = $result['result'];
				$this->messageresp = $result['message'];
				$this->statusresp = $result['status'];

				$running = $this->helper->RunningResp(
					$this->resultresp,
					$this->dataresp,
					$this->messageresp,
					$this->statusresp,
					$this->ttldataresp
				);

				return response()->json($running);

			} catch (\Throwable $th){
				$this->resultresp = $th->getMessage();
				$this->messageresp = 'Error in Catch';
				$this->statuscoderesp = $th->getCode();
	
				$error = $this->helper->ErrorResp(
					$this->resultresp, 
					$this->messageresp, 
					$this->statuscoderesp
				);
	
				return response()->json($error);
			}
			
		} else {
			$this->resultresp = 'Your data is not authorized';
			$this->dataresp = $escapestring_token;
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

	public function WorkerESS(Request $request, $param=null){

		if(!isset($request['data']['code']) && $param != null){

			$raw_token = $param['param']['token'];
			$split_token = explode('.', $raw_token);
			$decrypt_token = base64_decode($split_token[1]);
			$escapestring_token = json_decode($decrypt_token);
			
			if($escapestring_token == $param['param']['nik']){ 
				try {
					$client = new Client(); 
					$response = $client->post(
						'http://'.$this->config.'/RESTSecurity/RESTSecurity.svc/IDM/Worker',
						[
							RequestOptions::JSON => 
							['param' => $param['param']]
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
								'http://'.config('app.URL_GCP_LARAVEL_SERVICELB').$this->slug.'/profile/get',
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
						$newjson->JENIS_KELAMIN = $value->NIK == '000001' ? 'PRIA' : ($value->NIK == '000002' ? 'WANITA' : $value->JENIS_KELAMIN);
						$newjson->ALIAS = $value->ALIAS;

						$newdata[] = $newjson;

						$this->resultresp = 'Data has been process';
						$this->dataresp = $newdata[0];
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
						
					}

				} catch (\Throwable $th) {
					$this->resultresp = $th->getMessage();
					$this->messageresp = 'Error in Catch';
					$this->statuscoderesp = $th->getCode();

					$error = $this->helper->ErrorResp(
						$this->resultresp,
						$this->messageresp,
						$this->statuscoderesp
					);

					return response()->json($error);
				}

			} else {
				$this->resultresp = 'Your data is not authorized';
				$this->dataresp = $escapestring_token;
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
		else {
			$raw_token = $this->tokendraw;
			$split_token = explode('.', $raw_token);
			$decrypt_token = base64_decode($split_token[1]);
			$escapestring_token = json_decode($decrypt_token);

			if($request['data']['code'] == '1'){
				if($escapestring_token == $request['data']['nik']){ 
					try {
						$client = new Client(); 
						$response = $client->post(
							'http://'.$this->config.'/RESTSecurity/RESTSecurity.svc/IDM/Worker',
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
									'http://'.config('app.URL_GCP_LARAVEL_SERVICELB').$this->slug.'/profile/get',
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
							$newjson->JENIS_KELAMIN = $value->NIK == '000001' ? 'PRIA' : ($value->NIK == '000002' ? 'WANITA' : $value->JENIS_KELAMIN);
							$newjson->ALIAS = $value->ALIAS;

							$newdata[] = $newjson;

							$this->resultresp = 'Data has been process';
							$this->dataresp = $newdata[0];
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

						}
					
					} catch (\Throwable $th) {
						$this->resultresp = $th->getMessage();
						$this->messageresp = 'Error in Catch';
						$this->statuscoderesp = $th->getCode();

						$error = $this->helper->ErrorResp(
							$this->resultresp,
							$this->messageresp,
							$this->statuscoderesp
						);

						return response()->json($error);
					}
				} else {
					$this->resultresp = 'Your data is not authorized';
					$this->dataresp = $escapestring_token;
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
			}else{
				if($escapestring_token == $request['data']['find']){ 
					try {
						$client = new Client(); 
						$response = $client->post(
							'http://'.$this->config.'/RESTSecurity/RESTSecurity.svc/IDM/Worker',
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
									'http://'.config('app.URL_GCP_LARAVEL_SERVICELB').$this->slug.'/profile/get',
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

							$newjson->NIK = Crypt::encryptString($value->NIK);
							$newjson->NAMA = $value->NAMA;
							$newjson->EMAIL = Crypt::encryptString($value->EMAIL);
							$newjson->NOHP_ISAKU = Crypt::encryptString($value->NOHP_ISAKU);
							$newjson->JENIS_KELAMIN = $value->NIK == '000001' ? 'PRIA' : ($value->NIK == '000002' ? 'WANITA' : $value->JENIS_KELAMIN);
							$newjson->ALIAS = $value->ALIAS;

							$newdata[] = $newjson;

							$this->resultresp = 'Data has been process';
							$this->dataresp = $newdata[0];
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
						}
						
					} catch (\Throwable $th) {
						$this->resultresp = $th->getMessage();
						$this->messageresp = 'Error in Catch';
						$this->statuscoderesp = $th->getCode();

						$error = $this->helper->ErrorResp(
							$this->resultresp,
							$this->messageresp,
							$this->statuscoderesp
						);

						return response()->json($error);

					}
				} else {
					$this->resultresp = 'Your data is not authorized';
					$this->dataresp = $escapestring_token;
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

		}  
    }

	public function EncodeString(Request $request, $str) { //this function is cannot be recover to the original
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
			$raw_token = $this->$this->tokendraw;
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


