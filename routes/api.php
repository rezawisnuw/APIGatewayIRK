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


//Utility EndPoint
Route::group(['middleware' => 'cors'], function () {

	#DEV
	Route::post('Login/Dev', 'Dev\UtilityGateway@LoginESS');
	Route::post('Logout/Dev', 'Dev\UtilityGateway@LogoutESS');
	Route::post('UploadFisik/Dev', 'Dev\UtilityGateway@UploadFisik');
	Route::post('UploadBlob/Dev', 'Dev\UtilityGateway@UploadBlob');
	Route::post('DownloadFisik/Dev', 'Dev\UtilityGateway@DownloadFile93');
	Route::post('Firebase/Dev', 'Dev\UtilityGateway@Firebase');

    #STAG
	Route::post('Login/Stag', 'Stag\UtilityGateway@LoginESS');
	Route::post('Logout/Stag', 'Stag\UtilityGateway@LogoutESS');
	Route::post('UploadFisik/Stag', 'Stag\UtilityGateway@UploadFisik');
	Route::post('UploadBlob/Stag', 'Stag\UtilityGateway@UploadBlob');
	Route::post('DownloadFisik/Stag', 'Stag\UtilityGateway@DownloadFile93');
	Route::post('Firebase/Stag', 'Stag\UtilityGateway@Firebase');

    #LIVE
	Route::post('Login', 'Live\UtilityGateway@LoginESS');
	Route::post('Logout', 'Live\UtilityGateway@LogoutESS');
	Route::post('UploadFisik', 'Live\UtilityGateway@UploadFisik');
	Route::post('UploadBlob', 'Live\UtilityGateway@UploadBlob');
	Route::post('DownloadFisik', 'Live\UtilityGateway@DownloadFile93');
	Route::post('Firebase', 'Live\UtilityGateway@Firebase');

});

//IRK Endpoint
Route::group(['middleware' => ['cors']], function () {

    //CeritaKita Endpoint
	Route::group(['prefix' => 'CeritaKita'], function () {

		//DEV
		Route::post('signin/Dev', 'Dev\IRKCeritaKitaGateway@signin');
		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('auth/Dev', 'Dev\IRKCeritaKitaGateway@auth');
			Route::post('signout/Dev', 'Dev\IRKCeritaKitaGateway@signout');
		});

		//STAG
		Route::post('signin/Stag', 'Stag\IRKCeritaKitaGateway@signin');
		Route::group(['middleware' => ['tokenverifystag']], function () {
			Route::post('auth/Stag', 'Stag\IRKCeritaKitaGateway@auth');
			Route::post('signout/Stag', 'Stag\IRKCeritaKitaGateway@signout');

		});

		//LIVE
		Route::post('signin', 'Live\IRKCeritaKitaGateway@signin');
		Route::group(['middleware' => ['tokenverify']], function () {
			Route::post('auth', 'Live\IRKCeritaKitaGateway@auth');
			Route::post('signout', 'Live\IRKCeritaKitaGateway@signout');

		});
	
	});


});