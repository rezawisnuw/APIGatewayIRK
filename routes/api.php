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
Route::group(['middleware' => ['cors']], function () {

    //CeritaKita Endpoint
	Route::group(['prefix' => 'ceritakita'], function () {

		//dEV
		Route::post('signin/dev', [Dev\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('auth/dev', [Dev\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout/dev', [Dev\IRKCeritaKitaGateway::class, 'signout']);
		});

		//sTAG
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


});