<?php

namespace App\Http\Controllers\Dev;

use Illuminate\Http\Request;
use Illuminate\database\QueryException;
use Telegram;
use GuzzleHttp;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Psr7;
use PDF;
use Storage;
use SoapClient;
use App\Models\Dev\Credential;

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
					if(env('APP_ENV') == 'local'){
						$request->headers->set('Authorization-dev','Bearer'.$result['token']);
					} else{
						$request->cookie('Authorization-dev', 'Bearer'.$result['token'], '120');
					}
					$param['param'] = ['code' => 1,'nik' => $request['data']['nik']];
					return response()->json($this->WorkerESS($request, $param));
				} else {
					return response()->json($result['wcf']);
				}
			
			} else {
				return response()->json(['result' => 'Request Data is Empty', 'data' => null, 'message' => 'Failed Login', 'status' => '0', 'statuscode' => 400]);
			}
			return response()->json($result);
        } catch (\Throwable $th) {
            return response()->json(['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch', 'status' => '0', 'statuscode' => $th->getCode()]);
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
				return response()->json(['result' => 'Request Data is Empty', 'data' => null, 'message' => 'Failed Logout', 'status' => '0', 'statuscode' => 400]);
			}
			return response()->json($result);
        } catch (\Throwable $th) {
            return response()->json(['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch', 'status' => '0', 'statuscode' => $th->getCode()]);
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
				'status' => '0',
				'statuscode' => 400
				]
			);
        }else{
            $filedata = array(
                'stream' => curl_file_create($filePath,$mime,$namaFile),
            );

			$client = new \GuzzleHttp\Client();
			$filefisik = ($request->has('filefisik') && $request->filefisik != '') ? $request->file('filefisik') : '';

			$response = $client->request('POST', "http://".config('app.URL_14_WCF')."/RESTSecurity.svc/UploadFileDariInfraKe93?filePath={$filePath}\\{$namaFile}.{$extension}",[

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
					'status' => '0',
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
				'status' => '0',
				'statuscode' => 400
				]
			);
        }else{

            $filedata = array(
                'stream' => curl_file_create($filePath,$mime,$namaFile),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://".config('app.URL_14_WCF')."/RESTSecurity.svc/UploadFileBLOBDariInfraKe93?filePath={$filePath}.{$namaFile}.{$extension}");
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
				'http://'.config('app.URL_12_WCF').'/RESTSecurity.svc/IDM/Public/DownloadFileInfra',
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
			return response()->json(['result' => 'Request Data is Empty', 'data' => null, 'message' => 'Gagal Mengambil data', 'status' => '0', 'statuscode' => 400]);
		}

    }

    public static function Firebase(Request $request) {
        try {
			if (count($request->json()->all())) {
				$postbody = $request->json(['data']);

				$result = '';

				$client = new Client();
				$response = $client->post(
					'http://'.config('app.URL_14_WCF').'/RESTSecurity.svc/IDM/Firebase',
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
				return response()->json(['result' => 'Request Data is Empty', 'data' => null, 'message' => 'Gagal Mengambil data', 'status' => '0', 'statuscode' => 400]);
			}

        } catch (\Throwable $th) {
            return response()->json(['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch', 'status' => '0', 'statuscode' => $th->getCode()]);
        }
    }

	public static function WorkerESS(Request $request, $param){
		if(env('APP_ENV') == 'local'){
			$raw_token = str_contains($request->header('Authorization-dev'), 'Bearer') ? 'Authorization-dev=Bearer'.substr($request->header('Authorization-dev'),6) : 'Authorization-dev=Bearer'.$request->header('Authorization-dev');
		} else{
			$raw_token = str_contains($request->cookie('Authorization-dev'), 'Bearer') ? 'Authorization-dev=Bearer'.substr($request->cookie('Authorization-dev'),6) : 'Authorization-dev=Bearer'.$request->cookie('Authorization-dev');
		}

		$split_token = explode('.', $raw_token);
		$decrypt_token = base64_decode($split_token[1]);
		$escapestring_token = json_decode($decrypt_token);

		if(!isset($request['data']['code']) && $param != null){
			
			if($escapestring_token == $param['param']['nik']){ 
				try {
					$client = new Client(); 
					$response = $client->post(
						'http://'.config('app.URL_14_WCF').'/RESTSecurity.svc/IDM/Worker',
						[
							RequestOptions::JSON => 
							['param' => $param['param']]
						],
						['Content-Type' => 'application/json']
					);
					$body = $response->getBody();
					$temp = json_decode($body);
					$result = json_decode($temp->WorkerResult);
					return $result[0];
				} catch (\Throwable $th) {
					return ['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch' , 'status' => 0, 'statuscode' => $th->getCode()];
				}

			} else {
				return ['result' => 'Your Data Is Not Authorized', 'data' => $escapestring_token, 'message' => 'Bad Request' , 'status' => 0, 'statuscode' => 400];
			}

		} 
		else {

			if($escapestring_token == $request['data']['find']){ 
				try {
					$client = new Client(); 
					$response = $client->post(
						'http://'.config('app.URL_14_WCF').'/RESTSecurity.svc/IDM/Worker',
						[
							RequestOptions::JSON => 
							['param' => $request['data']]
						],
						['Content-Type' => 'application/json']
					);
					$body = $response->getBody();
					$temp = json_decode($body);
					$result = json_decode($temp->WorkerResult);
					return $result[0];
				} catch (\Throwable $th) {
					return ['result' => $th->getMessage(), 'data' => null, 'message' => 'Error in Catch' , 'status' => 0, 'statuscode' => $th->getCode()];
				}
			} else {
				return ['result' => 'Your Data Is Not Authorized', 'data' => $escapestring_token, 'message' => 'Bad Request' , 'status' => 0, 'statuscode' => 400];
			}

		}  
    }
}


