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
	Route::group(['prefix' => 'dev/ceritakita'], function () {

		Route::post('login', 'Dev\IRKCeritaKitaGateway@login');

		Route::group(['middleware' => ['tokenverifydev']], function () {
			Route::post('auth', 'Dev\IRKCeritaKitaGateway@auth');

		});
	
	});

	Route::group(['prefix' => 'stag/ceritakita'], function () {

		Route::group(['middleware' => ['verify_token.hris_stage']], function () {
			Route::post('auth', 'PKOnlineGatewayStaging@auth');
			Route::post('master/departments', 'PKOnlineGatewayStaging@department');
			Route::post('master/positions', 'PKOnlineGatewayStaging@position');
			Route::post('master/approvers', 'PKOnlineGatewayStaging@approver');
			Route::post('master/lead-times', 'PKOnlineGatewayStaging@leadTime');
			Route::post('master/users', 'PKOnlineGatewayStaging@user');
			Route::post('hrms', 'PKOnlineGatewayStaging@hrms');
			Route::post('budgets', 'PKOnlineGatewayStaging@budget');
			Route::post('pks', 'PKOnlineGatewayStaging@pk');
			Route::post('approval-statuses', 'PKOnlineGatewayStaging@approvalStatus');
			Route::post('pk-edits', 'PKOnlineGatewayStaging@pkEdit');
			Route::post('approver-substitutions', 'PKOnlineGatewayStaging@approverSubstitution');
			Route::post('warrants', 'PKOnlineGatewayStaging@warrant');
			Route::post('reports', 'PKOnlineGatewayStaging@report');
		});

		Route::post('recruitments', 'PKOnlineGatewayStaging@recruitment');
	});

	Route::group(['prefix' => 'ceritakita'], function () {

		Route::group(['middleware' => ['verify_token.hris']], function () {
			Route::post('auth', 'PKOnlineGateway@auth');
			Route::post('master/departments', 'PKOnlineGateway@department');
			Route::post('master/positions', 'PKOnlineGateway@position');
			Route::post('master/approvers', 'PKOnlineGateway@approver');
			Route::post('master/lead-times', 'PKOnlineGateway@leadTime');
			Route::post('master/users', 'PKOnlineGateway@user');
			Route::post('hrms', 'PKOnlineGateway@hrms');
			Route::post('budgets', 'PKOnlineGateway@budget');
			Route::post('pks', 'PKOnlineGateway@pk');
			Route::post('approval-statuses', 'PKOnlineGateway@approvalStatus');
			Route::post('pk-edits', 'PKOnlineGateway@pkEdit');
			Route::post('approver-substitutions', 'PKOnlineGateway@approverSubstitution');
			Route::post('warrants', 'PKOnlineGateway@warrant');
			Route::post('reports', 'PKOnlineGateway@report');
		});

		Route::post('recruitments', 'PKOnlineGateway@recruitment');
	});
});