<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


//Util EndPoint
Route::group(['middleware' => 'cors'], function () {

	#DEV
	Route::group(['prefix' => 'Dev'], function () {
		Route::post('/Login', 'Dev\UtilityGateway@LoginESS');
		Route::post('/Logout', 'Dev\UtilityGateway@LogoutESS');
        Route::post('/UploadFisik', 'Dev\UtilityGateway@UploadFisik');
		Route::post('/UploadBlob', 'Dev\UtilityGateway@UploadBlob');
        Route::post('/DownloadFisik', 'Dev\UtilityGateway@DownloadFile93');
        Route::post('/Firebase', 'Dev\UtilityGateway@Firebase');
	});

    #STAG
	Route::group(['prefix' => 'Stag'], function () {
		Route::post('/Login', 'Stag\UtilityGateway@LoginESS');
		Route::post('/Logout', 'Stag\UtilityGateway@LogoutESS');
        Route::post('/UploadFisik', 'Stag\UtilityGateway@UploadFisik');
		Route::post('/UploadBlob', 'Stag\UtilityGateway@UploadBlob');
        Route::post('/DownloadFisik', 'Stag\UtilityGateway@DownloadFile93');
        Route::post('/Firebase', 'Stag\UtilityGateway@Firebase');
	});

    #LIVE
		Route::post('/Login', 'Live\UtilityGateway@LoginESS');
		Route::post('/Logout', 'Live\UtilityGateway@LogoutESS');
		Route::post('/UploadFisik', 'Live\UtilityGateway@UploadFisik');
		Route::post('/UploadBlob', 'Live\UtilityGateway@UploadBlob');
		Route::post('/DownloadFisik', 'Live\UtilityGateway@DownloadFile93');
		Route::post('/Firebase', 'Live\UtilityGateway@Firebase');

});

//IRK Endpoint
Route::group(['middleware' => ['cors']], function () {

    //CeritaKita Endpoint Dev
	Route::group(['prefix' => 'CeritaKita/Dev'], function () {

		Route::post('signin', 'Dev\IRKCeritaKitaGateway@signin');

		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('auth', 'Dev\IRKCeritaKitaGateway@auth');
			Route::post('signout', 'Dev\IRKCeritaKitaGateway@signout');

		});
	
	});

	//CeritaKita Endpoint Stag
	Route::group(['prefix' => 'CeritaKita/Stag'], function () {

		Route::post('signin', 'Stag\IRKCeritaKitaGateway@signin');

		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('auth', 'Stag\IRKCeritaKitaGateway@auth');
			Route::post('signout', 'Stag\IRKCeritaKitaGateway@signout');

		});

	});

	//CeritaKita Endpoint Live
	Route::group(['prefix' => 'ceritakita'], function () {

		Route::post('signin', 'Live\IRKCeritaKitaGateway@signin');

		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('auth', 'Live\IRKCeritaKitaGateway@auth');
			Route::post('signout', 'Live\IRKCeritaKitaGateway@signout');

		});
	});
});