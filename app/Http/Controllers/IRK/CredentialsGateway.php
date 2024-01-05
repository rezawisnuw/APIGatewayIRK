<?php

namespace App\Http\Controllers\IRK;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\IRK\CredentialsModel;
use App\Helper\IRKHelp;

class CredentialsGateway extends Controller
{

	private $resultresp, $dataresp, $messageresp, $statusresp, $ttldataresp, $statuscoderesp, $slug, $path, $helper, $signature, $authorize, $model;

	public function __construct(Request $request)
	{
		// Call the parent constructor
		//parent::__construct();

		$slug = $request->route('slug');
		$this->slug = 'v1/' . $slug;

		$env = config('app.env');
		$this->env = $env;

		$model = new CredentialsModel($request, $slug);
		$this->model = $model;

		$helper = new IRKHelp($request);
		$this->helper = $helper;

		$segment = $helper->Segment($slug);
		$this->authorize = $segment['authorize'];
		$this->config = $segment['config'];

		$idkey = $helper->Environment($env);
		$this->tokendraw = $idkey['tokendraw'];

	}

	public function LoginESS(Request $request)
	{
		try {
			if (count($request->json()->all())) {
				$postbody = $request['data'];

				$result = $this->model->Login($postbody);

				if ($result['wcf']['status'] == '1') {

					$hardcode['param'] = ['code' => 1, 'nik' => $request['data']['nik']];

					$this->resultresp = 'Token has Stored in Cookie';
					$this->dataresp = app(UtilityGateway::class)->WorkerESS($request, $hardcode);
					$this->messageresp = isset(app(UtilityGateway::class)->WorkerESS($request, $hardcode)->nik) ? $result['wcf']['message'] : 'Failed on Run';
					$this->statusresp = isset(app(UtilityGateway::class)->WorkerESS($request, $hardcode)->nik) ? 1 : 0;

					$running = $this->helper->RunningResp(
						$this->resultresp,
						$this->dataresp,
						$this->messageresp,
						$this->statusresp,
						$this->ttldataresp
					);

					$url = $_SERVER['HTTP_HOST'];
					$info = parse_url($url);
					$host = $info['host'];
					$host_names = explode(".", $host);
					$domain = $host_names[1] . "." . $host_names[2];
					$subdomain = $host_names[0];

					$set_domain = config(['app.domain' => $domain]);
					$set_subdomain = config(['app.subdomain' => $subdomain]);

					return response()->json($running)
						->withCookie(cookie($this->authorize, 'Bearer' . $result['token'], '120', '/', config('app.domain'), false, false))
						->withCookie(cookie('NameEncryption', 'ValueEncryption', '120', '/', config('app.domain'), false, false));

				} else {

					$this->resultresp = $result['wcf']['result'];
					$this->dataresp = null;
					$this->messageresp = $result['wcf']['message'];
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

	public function LogoutESS(Request $request)
	{

		try {
			if (count($request->json()->all())) {
				$postbody = $request['data'];

				$result = $this->model->Logout($postbody);

				if ($result['status'] == '1') {

					$this->resultresp = 'Token has Revoked on Cookie';
					$this->dataresp = null;
					$this->messageresp = $result['message'];
					$this->statusresp = 1;

					$running = $this->helper->RunningResp(
						$this->resultresp,
						$this->dataresp,
						$this->messageresp,
						$this->statusresp,
						$this->ttldataresp
					);

					return response()->json($running);

				} else {

					$this->resultresp = $result['result'];
					$this->dataresp = null;
					$this->messageresp = $result['message'];
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

	public function EncodeString(Request $request, $str)
	{ //this function is cannot be recover to the original
		$encoded_text = '';

		if (empty($request->all()) && $str != null) {
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
		} else {
			$raw_token = $this->$this->tokendraw;
			$split_token = explode('.', $raw_token);
			$decrypt_token = base64_decode($split_token[1]);
			$escapestring_token = json_decode($decrypt_token);

			if ($escapestring_token == $request['nik']) {
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
			} else {
				return ['result' => 'Your Data Is Not Identified', 'data' => $escapestring_token, 'message' => 'Bad Request', 'status' => 0, 'statuscode' => 400];
			}

		}

		return substr(str_shuffle($encoded_text), 0, 8);
	}

	public function Security(Request $request)
	{
		$type = $request['data']['type'];
		$category = $request['data']['category'];
		$signkey = $request['data']['signkey'];
		$payload = $request['data']['payload'];

		if ($signkey == explode(':', config('app.key'))[1]) {

			if ($type == 'decode') {
				if ($category = 'AES256CBC') {
					$decrypt = Crypt::decryptString($payload);
					$decode = $decrypt;
					//$decode = json_decode($decrypt);
				} else if ($category = 'BASE64') {
					$decode = base64_decode($payload);
				}

				return response()->json($decode);

			} else if ($type == 'encode') {
				if ($category = 'AES256CBC') {
					$encode = json_encode($payload);
					$encrypt = Crypt::encryptString($encode);
				} else if ($category = 'BASE64') {
					$encode = base64_encode($payload);
				}

				return response()->json($encode);
			}

		} else {
			return response()->json(['result' => 'Sign Key not Verified', 'data' => null, 'message' => 'Failed on Run', 'status' => 0, 'statuscode' => 400]);
		}

	}

}