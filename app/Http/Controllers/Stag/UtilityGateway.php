<?php

namespace App\Http\Controllers\Stag;

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
				if($result['wcf']['status'] == '1') return response()->json($result['wcf'])->withCookie(cookie('Authorization-stag', 'Bearer'.$result['token'], '120'));
				else return response()->json($result['wcf']);
			} else {
				return response()->json(['result' => 'Process Not Found !!','message' => 'Gagal Login', 'status' => '0']);
			}
			return response()->json($result);
        } catch (\Throwable $th) {
            return response()->json(['result' => '','message' => 'Process Not Found !!!', 'status' => '0', 'Code' => 400]);
        }
    }

	public function LogoutESS(Request $request){
        $result = '';

        try {
			if (count($request->json()->all())) {
				$postbody = $request->json(['data']);

				$result = Credential::Logout($postbody);
				return response()->json($result);
			} else {
				return response()->json(['result' => 'Process Not Found !!','message' => 'Gagal Logout', 'status' => '0']);
			}
			return response()->json($result);
        } catch (\Throwable $th) {
            return response()->json(['result' => '','message' => 'Process Not Found !!!', 'status' => '0', 'Code' => 400]);
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
				'result'  => '',
				'message' => 'Gagal Upload !',
				'status' => '0',
				'Code' => 400]
			);
        }else{

            $filedata = array(
                'stream' => curl_file_create($filePath,$mime,$namaFile),
            );

			$client = new \GuzzleHttp\Client();
			$filefisik = ($request->has('filefisik') && $request->filefisik != '') ? $request->file('filefisik') : '';

			$response = $client->request('POST', "http://".config('app.URL_14_WCF')."/RESTSecurityDev/RESTSecurity.svc/UploadFileDariInfraKe93?filePath={$filePath}\\{$namaFile}.{$extension}",[

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
					'result'  => ''.$namaFile.' gagal diupload',
					'message' => $result,
					'status' => '0',
					'Code' => 400]
				);
				
			} else {
				return response()->json([
					'result'  => ''.$namaFile.' sukses diupload',
					'message' => $result,
					'status' => '1',
					'Code' => 200]
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
				'result'  => '',
				'message' => 'Gagal Upload !',
				'status' => '0',
				'Code' => 400]
			);
        }else{

            $filedata = array(
                'stream' => curl_file_create($filePath,$mime,$namaFile),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://".config('app.URL_14_WCF')."/RESTSecurityDev/RESTSecurity.svc/UploadFileBLOBDariInfraKe93?filePath={$filePath}.{$namaFile}.{$extension}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $filedata);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:'.$mime));

            $result = curl_exec($ch);

            return response()->json([
				'result'  => ''.$namaFile.' sukses diupload',
				'message' => 'Berhasil Upload !',
				'status' => '1',
				'Code' => 200]
			);

        }
    }

    public function DownloadFile93(Request $request) {
		if (count($request->json()->all())) {
				$postbody = $request->json(['data']);

				$result = '';

				$client = new Client();
				$response = $client->post(
					'http://'.config('app.URL_12_WCF').'/RESTSecurityDev/RESTSecurity.svc/IDM/Public/DownloadFileInfra',
					[
						RequestOptions::JSON =>
						['filePath' => $postbody['filePath']]
					],
					['Content-Type' => 'application/json']
				);
				$body = $response->getBody();
				$temp = json_decode($body);
				$result = json_decode($temp->DownloadFileDariInfraKe93Result);
				return response()->json(json_decode($result));
			} else {
				return response()->json(['result' => 'Process Not Found !!!!','message' => 'Gagal Mengambil data', 'status' => '0']);
			}

    }

    public static function Firebase(Request $request) {
        try {
			if (count($request->json()->all())) {
				$postbody = $request->json(['data']);

				$result = '';

				$client = new Client();
				$response = $client->post(
					'http://'.config('app.URL_14_WCF').'/RESTSecurityDev/RESTSecurity.svc/IDM/Firebase',
					[
						RequestOptions::JSON =>
						['param' => $postbody]
					],
					['Content-Type' => 'application/json']
				);
				$body = $response->getBody();
				$temp = json_decode($body);
				$result = json_decode($temp->FirebaseResult);
				return response()->json($result);
			} else {
				return response()->json(['result' => 'Process Not Found !!','message' => 'Gagal Mengambil data', 'status' => '0']);
			}

        } catch (\Throwable $th) {
            return response()->json(['result' => '','message' => 'Process Not Found !!!', 'status' => '0', 'Code' => 400]);
        }
    }
}


