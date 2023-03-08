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
	Route::post('Login/Dev', [Dev\UtilityGateway::class, 'LoginESS']);
	Route::post('Logout/Dev', [Dev\UtilityGateway::class, 'LogoutESS']);
	Route::post('UploadFisik/Dev', [Dev\UtilityGateway::class, 'UploadFisik']);
	Route::post('UploadBlob/Dev', [Dev\UtilityGateway::class, 'UploadBlob']);
	Route::post('DownloadFisik/Dev', [Dev\UtilityGateway::class, 'DownloadFile93']);
	Route::post('Firebase/Dev', [Dev\UtilityGateway::class, 'Firebase']);

    #STAG
	Route::post('Login/Stag', [Stag\UtilityGateway::class, 'LoginESS']);
	Route::post('Logout/Stag', [Stag\UtilityGateway::class, 'LogoutESS']);
	Route::post('UploadFisik/Stag', [Stag\UtilityGateway::class, 'UploadFisik']);
	Route::post('UploadBlob/Stag', [Stag\UtilityGateway::class, 'UploadBlob']);
	Route::post('DownloadFisik/Stag', [Stag\UtilityGateway::class, 'DownloadFile93']);
	Route::post('Firebase/Stag', [Stag\UtilityGateway::class, 'Firebase']);

    #LIVE
	Route::post('Login', [Live\UtilityGateway::class, 'LoginESS']);
	Route::post('Logout', [Live\UtilityGateway::class, 'LogoutESS']);
	Route::post('UploadFisik', [Live\UtilityGateway::class, 'UploadFisik']);
	Route::post('UploadBlob', [Live\UtilityGateway::class, 'UploadBlob']);
	Route::post('DownloadFisik', [Live\UtilityGateway::class, 'DownloadFile93']);
	Route::post('Firebase', [Live\UtilityGateway::class, 'Firebase']);

});

//IRK Endpoint
Route::group(['middleware' => ['cors']], function () {

    //CeritaKita Endpoint
	Route::group(['prefix' => 'CeritaKita'], function () {

		//DEV
		Route::post('signin/Dev', [Dev\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('auth/Dev', [Dev\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout/Dev', [Dev\IRKCeritaKitaGateway::class, 'signout']);
		});

		//STAG
		Route::post('signin/Stag', [Stag\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('auth/Stag', [Stag\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout/Stag', [Stag\IRKCeritaKitaGateway::class, 'signout']);

		});

		//LIVE
		Route::post('signin', [Live\IRKCeritaKitaGateway::class, 'signin']);
		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('auth', [Live\IRKCeritaKitaGateway::class, 'auth']);
			Route::post('signout', [Live\IRKCeritaKitaGateway::class, 'signout']);

		});
	
	});


});