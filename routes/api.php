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

//IRK Endpoint
Route::group(['prefix' => 'irk', 'middleware' => ['cors']], function () {

    //CeritaKita Endpoint
	Route::group(['prefix' => 'ceritakita'], function () {

		//DEV
		Route::post('signin/dev', [Dev\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('auth/dev', [Dev\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout/dev', [Dev\IRKCeritaKitaGateway::class, 'signout']);
		});

		//STAG
		Route::post('signin/stag', [Stag\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('auth/stag', [Stag\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout/stag', [Stag\IRKCeritaKitaGateway::class, 'signout']);

		});

		//LIVE
		Route::post('signin', [Live\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('auth', [Live\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout', [Live\IRKCeritaKitaGateway::class, 'signout']);

		});
	
	});

	//Motivasi Endpoint
	Route::group(['prefix' => 'motivasi'], function () {

		//DEV
		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get/dev', [Dev\IRKMotivasiGateway::class, 'get']);
			Route::post('post/dev', [Dev\IRKMotivasiGateway::class, 'post']);
			Route::post('put/dev', [Dev\IRKMotivasiGateway::class, 'put']);
			Route::post('delete/dev', [Dev\IRKMotivasiGateway::class, 'delete']);
		});

		//STAG
		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get/stag', [Stag\IRKMotivasiGateway::class, 'get']);
			Route::post('post/stag', [Stag\IRKMotivasiGateway::class, 'post']);
			Route::post('put/stag', [Stag\IRKMotivasiGateway::class, 'put']);
			Route::post('delete/stag', [Stag\IRKMotivasiGateway::class, 'delete']);
		});

		//LIVE
		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKMotivasiGateway::class, 'get']);
			Route::post('post', [Live\IRKMotivasiGateway::class, 'post']);
			Route::post('put', [Live\IRKMotivasiGateway::class, 'put']);
			Route::post('delete', [Live\IRKMotivasiGateway::class, 'delete']);
		});
	
	});

	//Curhatku Endpoint
	Route::group(['prefix' => 'curhatku'], function () {

		//DEV
		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get/dev', [Dev\IRKCurhatkuGateway::class, 'get']);
			Route::post('post/dev', [Dev\IRKCurhatkuGateway::class, 'post']);
			Route::post('put/dev', [Dev\IRKCurhatkuGateway::class, 'put']);
			Route::post('delete/dev', [Dev\IRKCurhatkuGateway::class, 'delete']);
		});

		//STAG
		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get/stag', [Stag\IRKCurhatkuGateway::class, 'get']);
			Route::post('post/stag', [Stag\IRKCurhatkuGateway::class, 'post']);
			Route::post('put/stag', [Stag\IRKCurhatkuGateway::class, 'put']);
			Route::post('delete/stag', [Stag\IRKCurhatkuGateway::class, 'delete']);
		});

		//LIVE
		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKCurhatkuGateway::class, 'get']);
			Route::post('post', [Live\IRKCurhatkuGateway::class, 'post']);
			Route::post('put', [Live\IRKCurhatkuGateway::class, 'put']);
			Route::post('delete', [Live\IRKCurhatkuGateway::class, 'delete']);
		});
	
	});

	//Comment Endpoint
	Route::group(['prefix' => 'comment'], function () {

		//DEV
		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('get/dev', [Dev\IRKCommentGateway::class, 'get']);
			Route::post('post/dev', [Dev\IRKCommentGateway::class, 'post']);
			Route::post('put/dev', [Dev\IRKCommentGateway::class, 'put']);
			Route::post('delete/dev', [Dev\IRKCommentGateway::class, 'delete']);
		});

		//STAG
		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('get/stag', [Stag\IRKCommentGateway::class, 'get']);
			Route::post('post/stag', [Stag\IRKCommentGateway::class, 'post']);
			Route::post('put/stag', [Stag\IRKCommentGateway::class, 'put']);
			Route::post('delete/stag', [Stag\IRKCommentGateway::class, 'delete']);
		});

		//LIVE
		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('get', [Live\IRKCommentGateway::class, 'get']);
			Route::post('post', [Live\IRKCommentGateway::class, 'post']);
			Route::post('put', [Live\IRKCommentGateway::class, 'put']);
			Route::post('delete', [Live\IRKCommentGateway::class, 'delete']);
		});
	
	});


});