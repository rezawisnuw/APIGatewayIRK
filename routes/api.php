<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dev;
use App\Http\Controllers\Stag;
use App\Http\Controllers\Live;


/*
|--------------------------------------------------------------------------
| API Routes Guidance
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//Utility EndPoint
Route::group(['middleware' => 'cors'], function () {
//WCF Services
	#DEV
	Route::post('dev/login', [Dev\UtilityGateway::class, 'LoginESS']);
	Route::post('dev/logout', [Dev\UtilityGateway::class, 'LogoutESS']);
	Route::post('dev/uploadFisik', [Dev\UtilityGateway::class, 'UploadFisik']);
	Route::post('dev/uploadBlob', [Dev\UtilityGateway::class, 'UploadBlob']);
	Route::post('dev/downloadFisik', [Dev\UtilityGateway::class, 'DownloadFile93']);
	Route::post('dev/firebase', [Dev\UtilityGateway::class, 'Firebase']);
	Route::post('dev/worker', [Dev\UtilityGateway::class, 'WorkerESS']);

    #STAG
	Route::post('stag/login', [Stag\UtilityGateway::class, 'LoginESS']);
	Route::post('stag/logout', [Stag\UtilityGateway::class, 'LogoutESS']);
	Route::post('stag/uploadFisik', [Stag\UtilityGateway::class, 'UploadFisik']);
	Route::post('stag/uploadBlob', [Stag\UtilityGateway::class, 'UploadBlob']);
	Route::post('stag/downloadFisik', [Stag\UtilityGateway::class, 'DownloadFile93']);
	Route::post('stag/firebase', [Stag\UtilityGateway::class, 'Firebase']);
	Route::post('stag/worker', [Stag\UtilityGateway::class, 'WorkerESS']);

    #LIVE
	Route::post('live/login', [Live\UtilityGateway::class, 'LoginESS']);
	Route::post('live/logout', [Live\UtilityGateway::class, 'LogoutESS']);
	Route::post('live/uploadFisik', [Live\UtilityGateway::class, 'UploadFisik']);
	Route::post('live/uploadBlob', [Live\UtilityGateway::class, 'UploadBlob']);
	Route::post('live/downloadFisik', [Live\UtilityGateway::class, 'DownloadFile93']);
	Route::post('live/firebase', [Live\UtilityGateway::class, 'Firebase']);
	Route::post('live/worker', [Live\UtilityGateway::class, 'WorkerESS']);

});

//IRK Endpoint DEV
Route::group(['prefix' => 'dev', 'middleware' => ['cors']], function () {
	//CeritaKita Endpoint
	Route::group(['prefix' => 'ceritakita'], function () {

		Route::post('signin', [Dev\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('auth', [Dev\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout', [Dev\IRKCeritaKitaGateway::class, 'signout']);

			Route::post('get', [Dev\IRKCeritaKitaGateway::class, 'get']);
			Route::post('post', [Dev\IRKCeritaKitaGateway::class, 'post']);
			Route::post('put', [Dev\IRKCeritaKitaGateway::class, 'put']);
			Route::post('delete', [Dev\IRKCeritaKitaGateway::class, 'delete']);
		});

	});

	//Motivasi Endpoint
	Route::group(['prefix' => 'motivasi'], function () {

		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get', [Dev\IRKMotivasiGateway::class, 'get']);
			Route::post('post', [Dev\IRKMotivasiGateway::class, 'post']);
			Route::post('put', [Dev\IRKMotivasiGateway::class, 'put']);
			Route::post('delete', [Dev\IRKMotivasiGateway::class, 'delete']);
		});

	});

	//Curhatku Endpoint
	Route::group(['prefix' => 'curhatku'], function () {

		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get', [Dev\IRKCurhatkuGateway::class, 'get']);
			Route::post('post', [Dev\IRKCurhatkuGateway::class, 'post']);
			Route::post('put', [Dev\IRKCurhatkuGateway::class, 'put']);
			Route::post('delete', [Dev\IRKCurhatkuGateway::class, 'delete']);
		});
		
	});

	//Comment Endpoint
	Route::group(['prefix' => 'comment'], function () {

		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get', [Dev\IRKCommentGateway::class, 'get']);
			Route::post('post', [Dev\IRKCommentGateway::class, 'post']);
			Route::post('put', [Dev\IRKCommentGateway::class, 'put']);
			Route::post('delete', [Dev\IRKCommentGateway::class, 'delete']);
		});

	});

	//Like Endpoint
	Route::group(['prefix' => 'like'], function () {

		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get', [Dev\IRKLikeGateway::class, 'get']);
			Route::post('post', [Dev\IRKLikeGateway::class, 'post']);
			Route::post('put', [Dev\IRKLikeGateway::class, 'put']);
			Route::post('delete', [Dev\IRKLikeGateway::class, 'delete']);
		});

	});

	//Report Endpoint
	Route::group(['prefix' => 'report'], function () {

		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get', [Dev\IRKReportGateway::class, 'get']);
			Route::post('post', [Dev\IRKReportGateway::class, 'post']);
			Route::post('put', [Dev\IRKReportGateway::class, 'put']);
			Route::post('delete', [Dev\IRKReportGateway::class, 'delete']);
		});

	});

	//Profile Endpoint
	Route::group(['prefix' => 'profile'], function () {

		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get', [Dev\IRKProfileGateway::class, 'get']);
			Route::post('post', [Dev\IRKProfileGateway::class, 'post']);
			Route::post('put', [Dev\IRKProfileGateway::class, 'put']);
			Route::post('delete', [Dev\IRKProfileGateway::class, 'delete']);
		});

	});

	//Version Endpoint
	Route::group(['prefix' => 'version'], function () {

		//Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get', [Dev\IRKVersionGateway::class, 'get']);
			Route::post('post', [Dev\IRKVersionGateway::class, 'post']);
			Route::post('put', [Dev\IRKVersionGateway::class, 'put']);
			Route::post('delete', [Dev\IRKVersionGateway::class, 'delete']);
		//});

	});
});

//IRK Endpoint STAG
Route::group(['prefix' => 'stag', 'middleware' => ['cors']], function () {

	//CeritaKita Endpoint
	Route::group(['prefix' => 'ceritakita'], function () {

		Route::post('signin', [Stag\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('auth', [Stag\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout', [Stag\IRKCeritaKitaGateway::class, 'signout']);

			Route::post('get', [Stag\IRKCeritaKitaGateway::class, 'get']);
			Route::post('post', [Stag\IRKCeritaKitaGateway::class, 'post']);
			Route::post('put', [Stag\IRKCeritaKitaGateway::class, 'put']);
			Route::post('delete', [Stag\IRKCeritaKitaGateway::class, 'delete']);
		});

	});

	//Motivasi Endpoint
	Route::group(['prefix' => 'motivasi'], function () {

		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get', [Stag\IRKMotivasiGateway::class, 'get']);
			Route::post('post', [Stag\IRKMotivasiGateway::class, 'post']);
			Route::post('put', [Stag\IRKMotivasiGateway::class, 'put']);
			Route::post('delete', [Stag\IRKMotivasiGateway::class, 'delete']);
		});

	});

	//Curhatku Endpoint
	Route::group(['prefix' => 'curhatku'], function () {

		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get', [Stag\IRKCurhatkuGateway::class, 'get']);
			Route::post('post', [Stag\IRKCurhatkuGateway::class, 'post']);
			Route::post('put', [Stag\IRKCurhatkuGateway::class, 'put']);
			Route::post('delete', [Stag\IRKCurhatkuGateway::class, 'delete']);
		});

	});

	//Comment Endpoint
	Route::group(['prefix' => 'comment'], function () {

		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get', [Stag\IRKCommentGateway::class, 'get']);
			Route::post('post', [Stag\IRKCommentGateway::class, 'post']);
			Route::post('put', [Stag\IRKCommentGateway::class, 'put']);
			Route::post('delete', [Stag\IRKCommentGateway::class, 'delete']);
		});

	});

	//Like Endpoint
	Route::group(['prefix' => 'like'], function () {

		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get', [Stag\IRKLikeGateway::class, 'get']);
			Route::post('post', [Stag\IRKLikeGateway::class, 'post']);
			Route::post('put', [Stag\IRKLikeGateway::class, 'put']);
			Route::post('delete', [Stag\IRKLikeGateway::class, 'delete']);
		});

	});

	//Report Endpoint
	Route::group(['prefix' => 'report'], function () {

		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get', [Stag\IRKReportGateway::class, 'get']);
			Route::post('post', [Stag\IRKReportGateway::class, 'post']);
			Route::post('put', [Stag\IRKReportGateway::class, 'put']);
			Route::post('delete', [Stag\IRKReportGateway::class, 'delete']);
		});

	});

	//Profile Endpoint
	Route::group(['prefix' => 'profile'], function () {

		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get', [Stag\IRKProfileGateway::class, 'get']);
			Route::post('post', [Stag\IRKProfileGateway::class, 'post']);
			Route::post('put', [Stag\IRKProfileGateway::class, 'put']);
			Route::post('delete', [Stag\IRKProfileGateway::class, 'delete']);
		});

	});

	//Version Endpoint
	Route::group(['prefix' => 'version'], function () {

		//Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get', [Stag\IRKVersionGateway::class, 'get']);
			Route::post('post', [Stag\IRKVersionGateway::class, 'post']);
			Route::post('put', [Stag\IRKVersionGateway::class, 'put']);
			Route::post('delete', [Stag\IRKVersionGateway::class, 'delete']);
		//});

	});

});

//IRK Endpoint LIVE
Route::group(['prefix' => 'live', 'middleware' => ['cors']], function () {

	//CeritaKita Endpoint
	Route::group(['prefix' => 'ceritakita'], function () {
		
		Route::post('signin', [Live\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('auth', [Live\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout', [Live\IRKCeritaKitaGateway::class, 'signout']);

			Route::post('get', [Live\IRKCeritaKitaGateway::class, 'get']);
			Route::post('post', [Live\IRKCeritaKitaGateway::class, 'post']);
			Route::post('put', [Live\IRKCeritaKitaGateway::class, 'put']);
			Route::post('delete', [Live\IRKCeritaKitaGateway::class, 'delete']);
		});

	});

	//Motivasi Endpoint
	Route::group(['prefix' => 'motivasi'], function () {

		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKMotivasiGateway::class, 'get']);
			Route::post('post', [Live\IRKMotivasiGateway::class, 'post']);
			Route::post('put', [Live\IRKMotivasiGateway::class, 'put']);
			Route::post('delete', [Live\IRKMotivasiGateway::class, 'delete']);
		});

	});

	//Curhatku Endpoint
	Route::group(['prefix' => 'curhatku'], function () {

		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKCurhatkuGateway::class, 'get']);
			Route::post('post', [Live\IRKCurhatkuGateway::class, 'post']);
			Route::post('put', [Live\IRKCurhatkuGateway::class, 'put']);
			Route::post('delete', [Live\IRKCurhatkuGateway::class, 'delete']);
		});

	});

	//Comment Endpoint
	Route::group(['prefix' => 'comment'], function () {

		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKCommentGateway::class, 'get']);
			Route::post('post', [Live\IRKCommentGateway::class, 'post']);
			Route::post('put', [Live\IRKCommentGateway::class, 'put']);
			Route::post('delete', [Live\IRKCommentGateway::class, 'delete']);
		});

	});

	//Like Endpoint
	Route::group(['prefix' => 'like'], function () {

		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKLikeGateway::class, 'get']);
			Route::post('post', [Live\IRKLikeGateway::class, 'post']);
			Route::post('put', [Live\IRKLikeGateway::class, 'put']);
			Route::post('delete', [Live\IRKLikeGateway::class, 'delete']);
		});

	});

	//Report Endpoint
	Route::group(['prefix' => 'report'], function () {

		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKReportGateway::class, 'get']);
			Route::post('post', [Live\IRKReportGateway::class, 'post']);
			Route::post('put', [Live\IRKReportGateway::class, 'put']);
			Route::post('delete', [Live\IRKReportGateway::class, 'delete']);
		});

	});

	//Profile Endpoint
	Route::group(['prefix' => 'profile'], function () {

		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKProfileGateway::class, 'get']);
			Route::post('post', [Live\IRKProfileGateway::class, 'post']);
			Route::post('put', [Live\IRKProfileGateway::class, 'put']);
			Route::post('delete', [Live\IRKProfileGateway::class, 'delete']);
		});

	});

	//Version Endpoint
	Route::group(['prefix' => 'version'], function () {

		//Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKVersionGateway::class, 'get']);
			Route::post('post', [Live\IRKVersionGateway::class, 'post']);
			Route::post('put', [Live\IRKVersionGateway::class, 'put']);
			Route::post('delete', [Live\IRKVersionGateway::class, 'delete']);
		//});

	});

});