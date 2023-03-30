<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dev;
use App\Http\Controllers\Stag;
use App\Http\Controllers\Live;


/*
|--------------------------------------------------------------------------
| API Routes
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

	#DEV
	Route::post('login/dev', [Dev\UtilityGateway::class, 'LoginESS']);
	Route::post('logout/dev', [Dev\UtilityGateway::class, 'LogoutESS']);
	Route::post('uploadFisik/dev', [Dev\UtilityGateway::class, 'UploadFisik']);
	Route::post('uploadBlob/dev', [Dev\UtilityGateway::class, 'UploadBlob']);
	Route::post('downloadFisik/dev', [Dev\UtilityGateway::class, 'DownloadFile93']);
	Route::post('firebase/dev', [Dev\UtilityGateway::class, 'Firebase']);

    #STAG
	Route::post('login/stag', [Stag\UtilityGateway::class, 'LoginESS']);
	Route::post('logout/stag', [Stag\UtilityGateway::class, 'LogoutESS']);
	Route::post('uploadFisik/stag', [Stag\UtilityGateway::class, 'UploadFisik']);
	Route::post('uploadBlob/stag', [Stag\UtilityGateway::class, 'UploadBlob']);
	Route::post('downloadFisik/stag', [Stag\UtilityGateway::class, 'DownloadFile93']);
	Route::post('firebase/stag', [Stag\UtilityGateway::class, 'Firebase']);

    #LIVE
	Route::post('login', [Live\UtilityGateway::class, 'LoginESS']);
	Route::post('logout', [Live\UtilityGateway::class, 'LogoutESS']);
	Route::post('uploadFisik', [Live\UtilityGateway::class, 'UploadFisik']);
	Route::post('uploadBlob', [Live\UtilityGateway::class, 'UploadBlob']);
	Route::post('downloadFisik', [Live\UtilityGateway::class, 'DownloadFile93']);
	Route::post('firebase', [Live\UtilityGateway::class, 'Firebase']);

});

//IRK Endpoint DEV
Route::group(['prefix' => 'dev', 'middleware' => ['cors']], function () {
	//CeritaKita Endpoint
	Route::group(['prefix' => 'ceritakita'], function () {

		Route::post('signin', [Dev\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('auth', [Dev\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout', [Dev\IRKCeritaKitaGateway::class, 'signout']);
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
			Route::post('get/dev', [Dev\IRKCommentGateway::class, 'get']);
			Route::post('post/dev', [Dev\IRKCommentGateway::class, 'post']);
			Route::post('put/dev', [Dev\IRKCommentGateway::class, 'put']);
			Route::post('delete/dev', [Dev\IRKCommentGateway::class, 'delete']);
		});

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

});

//IRK Endpoint LIVE
Route::group(['prefix' => 'live', 'middleware' => ['cors']], function () {

	//CeritaKita Endpoint
	Route::group(['prefix' => 'ceritakita'], function () {
		
		Route::post('signin', [Live\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('auth', [Live\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout', [Live\IRKCeritaKitaGateway::class, 'signout']);

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

});