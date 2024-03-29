<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\Dev;
// use App\Http\Controllers\Stag;
// use App\Http\Controllers\Live;



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

//-----------------------START OLD SCHEME-----------------------------------------
//Utility Endpoint
// Route::group(['middleware' => 'cors'], function () {
// //WCF Services
// 	// #DEV
// 	// Route::post('dev/login', [Dev\UtilityGateway::class, 'LoginESS']);
// 	// Route::post('dev/logout', [Dev\UtilityGateway::class, 'LogoutESS']);
// 	// Route::post('dev/uploadFisik', [Dev\UtilityGateway::class, 'UploadFisik']);
// 	// Route::post('dev/uploadBlob', [Dev\UtilityGateway::class, 'UploadBlob']);
// 	// Route::post('dev/downloadFisik', [Dev\UtilityGateway::class, 'DownloadFile93']);
// 	// Route::post('dev/firebase', [Dev\UtilityGateway::class, 'Firebase']);
// 	// Route::post('dev/worker', [Dev\UtilityGateway::class, 'WorkerESS']);

//     // #STAG
// 	// Route::post('stag/login', [Stag\UtilityGateway::class, 'LoginESS']);
// 	// Route::post('stag/logout', [Stag\UtilityGateway::class, 'LogoutESS']);
// 	// Route::post('stag/uploadFisik', [Stag\UtilityGateway::class, 'UploadFisik']);
// 	// Route::post('stag/uploadBlob', [Stag\UtilityGateway::class, 'UploadBlob']);
// 	// Route::post('stag/downloadFisik', [Stag\UtilityGateway::class, 'DownloadFile93']);
// 	// Route::post('stag/firebase', [Stag\UtilityGateway::class, 'Firebase']);
// 	// Route::post('stag/worker', [Stag\UtilityGateway::class, 'WorkerESS']);

//  // #LIVE
// 	// Route::post('live/login', [Live\UtilityGateway::class, 'LoginESS']);
// 	// Route::post('live/logout', [Live\UtilityGateway::class, 'LogoutESS']);
// 	// Route::post('live/uploadFisik', [Live\UtilityGateway::class, 'UploadFisik']);
// 	// Route::post('live/uploadBlob', [Live\UtilityGateway::class, 'UploadBlob']);
// 	// Route::post('live/downloadFisik', [Live\UtilityGateway::class, 'DownloadFile93']);
// 	// Route::post('live/firebase', [Live\UtilityGateway::class, 'Firebase']);
// 	// Route::post('live/worker', [Live\UtilityGateway::class, 'WorkerESS']);

// });

// //IRK Endpoint DEV
// Route::group(['prefix' => 'dev', 'middleware' => ['cors']], function () {
// 	//CeritaKita Endpoint
// 	Route::group(['prefix' => 'ceritakita'], function () {

// 		Route::post('signin', [Dev\IRKCeritaKitaGateway::class, 'signin']);
// 		Route::group(['middleware' => ['tokenverifydev']], function () {
// 			Route::post('auth', [Dev\IRKCeritaKitaGateway::class, 'auth']);
// 			Route::post('signout', [Dev\IRKCeritaKitaGateway::class, 'signout']);

// 			Route::post('get', [Dev\IRKCeritaKitaGateway::class, 'get']);
// 			Route::post('post', [Dev\IRKCeritaKitaGateway::class, 'post']);
// 			Route::post('put', [Dev\IRKCeritaKitaGateway::class, 'put']);
// 			Route::post('delete', [Dev\IRKCeritaKitaGateway::class, 'delete']);
// 		});

// 	});

// 	//Motivasi Endpoint
// 	Route::group(['prefix' => 'motivasi'], function () {

// 		Route::group(['middleware' => ['tokenverifydev']], function () {
// 			Route::post('get', [Dev\IRKMotivasiGateway::class, 'get']);
// 			Route::post('post', [Dev\IRKMotivasiGateway::class, 'post']);
// 			Route::post('put', [Dev\IRKMotivasiGateway::class, 'put']);
// 			Route::post('delete', [Dev\IRKMotivasiGateway::class, 'delete']);
// 		});

// 	});

// 	// //Curhatku Endpoint
// 	// Route::group(['prefix' => 'curhatku'], function () {

// 	// 	Route::group(['middleware' => ['tokenverifydev']], function () {
// 	// 		Route::post('get', [Dev\IRKCurhatkuGateway::class, 'get']);
// 	// 		Route::post('post', [Dev\IRKCurhatkuGateway::class, 'post']);
// 	// 		Route::post('put', [Dev\IRKCurhatkuGateway::class, 'put']);
// 	// 		Route::post('delete', [Dev\IRKCurhatkuGateway::class, 'delete']);
// 	// 	});

// 	// });

// 	//Comment Endpoint
// 	Route::group(['prefix' => 'comment'], function () {

// 		Route::group(['middleware' => ['tokenverifydev']], function () {
// 			Route::post('get', [Dev\IRKCommentGateway::class, 'get']);
// 			Route::post('post', [Dev\IRKCommentGateway::class, 'post']);
// 			Route::post('put', [Dev\IRKCommentGateway::class, 'put']);
// 			Route::post('delete', [Dev\IRKCommentGateway::class, 'delete']);
// 		});

// 	});

// 	//Like Endpoint
// 	Route::group(['prefix' => 'like'], function () {

// 		Route::group(['middleware' => ['tokenverifydev']], function () {
// 			Route::post('get', [Dev\IRKLikeGateway::class, 'get']);
// 			Route::post('post', [Dev\IRKLikeGateway::class, 'post']);
// 			Route::post('put', [Dev\IRKLikeGateway::class, 'put']);
// 			Route::post('delete', [Dev\IRKLikeGateway::class, 'delete']);
// 		});

// 	});

// 	//Report Endpoint
// 	Route::group(['prefix' => 'report'], function () {

// 		Route::group(['middleware' => ['tokenverifydev']], function () {
// 			Route::post('get', [Dev\IRKReportGateway::class, 'get']);
// 			Route::post('post', [Dev\IRKReportGateway::class, 'post']);
// 			Route::post('put', [Dev\IRKReportGateway::class, 'put']);
// 			Route::post('delete', [Dev\IRKReportGateway::class, 'delete']);
// 		});

// 	});

// 	//Profile Endpoint
// 	Route::group(['prefix' => 'profile'], function () {

// 		Route::group(['middleware' => ['tokenverifydev']], function () {
// 			Route::post('get', [Dev\IRKProfileGateway::class, 'get']);
// 			Route::post('post', [Dev\IRKProfileGateway::class, 'post']);
// 			Route::post('put', [Dev\IRKProfileGateway::class, 'put']);
// 			Route::post('delete', [Dev\IRKProfileGateway::class, 'delete']);
// 		});

// 	});

// 	//Version Endpoint
// 	Route::group(['prefix' => 'version'], function () {

// 		//Route::group(['middleware' => ['tokenverifydev']], function () {
// 			Route::post('get', [Dev\IRKVersionGateway::class, 'get']);
// 			Route::post('post', [Dev\IRKVersionGateway::class, 'post']);
// 			Route::post('put', [Dev\IRKVersionGateway::class, 'put']);
// 			Route::post('delete', [Dev\IRKVersionGateway::class, 'delete']);
// 		//});

// 	});
// });

// //IRK Endpoint STAG
// Route::group(['prefix' => 'stag', 'middleware' => ['cors']], function () {

// 	//CeritaKita Endpoint
// 	Route::group(['prefix' => 'ceritakita'], function () {

// 		Route::post('signin', [Stag\IRKCeritaKitaGateway::class, 'signin']);
// 		Route::group(['middleware' => ['tokenverifystag']], function () {
// 			Route::post('auth', [Stag\IRKCeritaKitaGateway::class, 'auth']);
// 			Route::post('signout', [Stag\IRKCeritaKitaGateway::class, 'signout']);

// 			Route::post('get', [Stag\IRKCeritaKitaGateway::class, 'get']);
// 			Route::post('post', [Stag\IRKCeritaKitaGateway::class, 'post']);
// 			Route::post('put', [Stag\IRKCeritaKitaGateway::class, 'put']);
// 			Route::post('delete', [Stag\IRKCeritaKitaGateway::class, 'delete']);
// 		});

// 	});

// 	//Motivasi Endpoint
// 	Route::group(['prefix' => 'motivasi'], function () {

// 		Route::group(['middleware' => ['tokenverifystag']], function () {
// 			Route::post('get', [Stag\IRKMotivasiGateway::class, 'get']);
// 			Route::post('post', [Stag\IRKMotivasiGateway::class, 'post']);
// 			Route::post('put', [Stag\IRKMotivasiGateway::class, 'put']);
// 			Route::post('delete', [Stag\IRKMotivasiGateway::class, 'delete']);
// 		});

// 	});

// 	//Curhatku Endpoint
// 	Route::group(['prefix' => 'curhatku'], function () {

// 		Route::group(['middleware' => ['tokenverifystag']], function () {
// 			Route::post('get', [Stag\IRKCurhatkuGateway::class, 'get']);
// 			Route::post('post', [Stag\IRKCurhatkuGateway::class, 'post']);
// 			Route::post('put', [Stag\IRKCurhatkuGateway::class, 'put']);
// 			Route::post('delete', [Stag\IRKCurhatkuGateway::class, 'delete']);
// 		});

// 	});

// 	//Comment Endpoint
// 	Route::group(['prefix' => 'comment'], function () {

// 		Route::group(['middleware' => ['tokenverifystag']], function () {
// 			Route::post('get', [Stag\IRKCommentGateway::class, 'get']);
// 			Route::post('post', [Stag\IRKCommentGateway::class, 'post']);
// 			Route::post('put', [Stag\IRKCommentGateway::class, 'put']);
// 			Route::post('delete', [Stag\IRKCommentGateway::class, 'delete']);
// 		});

// 	});

// 	//Like Endpoint
// 	Route::group(['prefix' => 'like'], function () {

// 		Route::group(['middleware' => ['tokenverifystag']], function () {
// 			Route::post('get', [Stag\IRKLikeGateway::class, 'get']);
// 			Route::post('post', [Stag\IRKLikeGateway::class, 'post']);
// 			Route::post('put', [Stag\IRKLikeGateway::class, 'put']);
// 			Route::post('delete', [Stag\IRKLikeGateway::class, 'delete']);
// 		});

// 	});

// 	//Report Endpoint
// 	Route::group(['prefix' => 'report'], function () {

// 		Route::group(['middleware' => ['tokenverifystag']], function () {
// 			Route::post('get', [Stag\IRKReportGateway::class, 'get']);
// 			Route::post('post', [Stag\IRKReportGateway::class, 'post']);
// 			Route::post('put', [Stag\IRKReportGateway::class, 'put']);
// 			Route::post('delete', [Stag\IRKReportGateway::class, 'delete']);
// 		});

// 	});

// 	//Profile Endpoint
// 	Route::group(['prefix' => 'profile'], function () {

// 		Route::group(['middleware' => ['tokenverifystag']], function () {
// 			Route::post('get', [Stag\IRKProfileGateway::class, 'get']);
// 			Route::post('post', [Stag\IRKProfileGateway::class, 'post']);
// 			Route::post('put', [Stag\IRKProfileGateway::class, 'put']);
// 			Route::post('delete', [Stag\IRKProfileGateway::class, 'delete']);
// 		});

// 	});

// 	//Version Endpoint
// 	Route::group(['prefix' => 'version'], function () {

// 		//Route::group(['middleware' => ['tokenverifystag']], function () {
// 			Route::post('get', [Stag\IRKVersionGateway::class, 'get']);
// 			Route::post('post', [Stag\IRKVersionGateway::class, 'post']);
// 			Route::post('put', [Stag\IRKVersionGateway::class, 'put']);
// 			Route::post('delete', [Stag\IRKVersionGateway::class, 'delete']);
// 		//});

// 	});

// });

//IRK Endpoint LIVE
// Route::group(['prefix' => 'live', 'middleware' => ['cors']], function () {

// 	//CeritaKita Endpoint
// 	Route::group(['prefix' => 'ceritakita'], function () {

// 		Route::post('signin', [Live\IRKCeritaKitaGateway::class, 'signin']);
// 		Route::group(['middleware' => ['tokenverify']], function () {
// 			Route::post('auth', [Live\IRKCeritaKitaGateway::class, 'auth']);
// 			Route::post('signout', [Live\IRKCeritaKitaGateway::class, 'signout']);

// 			Route::post('get', [Live\IRKCeritaKitaGateway::class, 'get']);
// 			Route::post('post', [Live\IRKCeritaKitaGateway::class, 'post']);
// 			Route::post('put', [Live\IRKCeritaKitaGateway::class, 'put']);
// 			Route::post('delete', [Live\IRKCeritaKitaGateway::class, 'delete']);
// 		});

// 	});

// 	//Motivasi Endpoint
// 	Route::group(['prefix' => 'motivasi'], function () {

// 		Route::group(['middleware' => ['tokenverify']], function () {
// 			Route::post('get', [Live\IRKMotivasiGateway::class, 'get']);
// 			Route::post('post', [Live\IRKMotivasiGateway::class, 'post']);
// 			Route::post('put', [Live\IRKMotivasiGateway::class, 'put']);
// 			Route::post('delete', [Live\IRKMotivasiGateway::class, 'delete']);
// 		});

// 	});

// 	//Curhatku Endpoint
// 	Route::group(['prefix' => 'curhatku'], function () {

// 		Route::group(['middleware' => ['tokenverify']], function () {
// 			Route::post('get', [Live\IRKCurhatkuGateway::class, 'get']);
// 			Route::post('post', [Live\IRKCurhatkuGateway::class, 'post']);
// 			Route::post('put', [Live\IRKCurhatkuGateway::class, 'put']);
// 			Route::post('delete', [Live\IRKCurhatkuGateway::class, 'delete']);
// 		});

// 	});

// 	//Comment Endpoint
// 	Route::group(['prefix' => 'comment'], function () {

// 		Route::group(['middleware' => ['tokenverify']], function () {
// 			Route::post('get', [Live\IRKCommentGateway::class, 'get']);
// 			Route::post('post', [Live\IRKCommentGateway::class, 'post']);
// 			Route::post('put', [Live\IRKCommentGateway::class, 'put']);
// 			Route::post('delete', [Live\IRKCommentGateway::class, 'delete']);
// 		});

// 	});

// 	//Like Endpoint
// 	Route::group(['prefix' => 'like'], function () {

// 		Route::group(['middleware' => ['tokenverify']], function () {
// 			Route::post('get', [Live\IRKLikeGateway::class, 'get']);
// 			Route::post('post', [Live\IRKLikeGateway::class, 'post']);
// 			Route::post('put', [Live\IRKLikeGateway::class, 'put']);
// 			Route::post('delete', [Live\IRKLikeGateway::class, 'delete']);
// 		});

// 	});

// 	//Report Endpoint
// 	Route::group(['prefix' => 'report'], function () {

// 		Route::group(['middleware' => ['tokenverify']], function () {
// 			Route::post('get', [Live\IRKReportGateway::class, 'get']);
// 			Route::post('post', [Live\IRKReportGateway::class, 'post']);
// 			Route::post('put', [Live\IRKReportGateway::class, 'put']);
// 			Route::post('delete', [Live\IRKReportGateway::class, 'delete']);
// 		});

// 	});

// 	//Profile Endpoint
// 	Route::group(['prefix' => 'profile'], function () {

// 		Route::group(['middleware' => ['tokenverify']], function () {
// 			Route::post('get', [Live\IRKProfileGateway::class, 'get']);
// 			Route::post('post', [Live\IRKProfileGateway::class, 'post']);
// 			Route::post('put', [Live\IRKProfileGateway::class, 'put']);
// 			Route::post('delete', [Live\IRKProfileGateway::class, 'delete']);
// 		});

// 	});

// 	//Version Endpoint
// 	Route::group(['prefix' => 'version'], function () {

// 		//Route::group(['middleware' => ['tokenverify']], function () {
// 			Route::post('get', [Live\IRKVersionGateway::class, 'get']);
// 			Route::post('post', [Live\IRKVersionGateway::class, 'post']);
// 			Route::post('put', [Live\IRKVersionGateway::class, 'put']);
// 			Route::post('delete', [Live\IRKVersionGateway::class, 'delete']);
// 		//});

// 	});

// });
//-----------------------END OLD SCHEME-----------------------------------------




//-----------------------START NEW SCHEME-----------------------------------------
//IRK NEW Endpoint
// Route::group([
// 	'prefix' => '{slug}',
// 	'where' => [
// 		'slug' => 'dev|stag|live'
// 	],
// 	'middleware' => 'cors'
// ], function () {

// 	//Credentials Endpoint
// 	Route::post('login', [IRK\CredentialsGateway::class, 'LoginESS']);
// 	Route::post('logout', [IRK\CredentialsGateway::class, 'LogoutESS']);
// 	Route::post('security', [IRK\CredentialsGateway::class, 'Security']);

// 	//Utility Endpoint
// 	Route::group(['middleware' => ['tokenauth']], function () {
// 		Route::post('worker', [IRK\UtilityGateway::class, 'WorkerESS']);
// 		Route::post('unitcabang', [IRK\UtilityGateway::class, 'UnitCabang']);
// 		Route::post('jabatan', [IRK\UtilityGateway::class, 'Jabatan']);
// 		Route::post('presensi', [IRK\UtilityGateway::class, 'PresensiWFH']);
// 	});

// 	//Version Endpoint
// 	Route::group(['prefix' => 'version'], function () {
// 		Route::post('get', [IRK\VersionGateway::class, 'get']);
// 		Route::post('post', [IRK\VersionGateway::class, 'post']);
// 		Route::post('put', [IRK\VersionGateway::class, 'put']);
// 		Route::post('delete', [IRK\VersionGateway::class, 'delete']);
// 	});

// 	//Ceritakita Endpoint
// 	Route::group(['prefix' => 'ceritakita', 'middleware' => 'tokenauth'], function () {
// 		Route::post('get', [IRK\CeritakitaGateway::class, 'get']);
// 		Route::post('post', [IRK\CeritakitaGateway::class, 'post']);
// 		Route::post('put', [IRK\CeritakitaGateway::class, 'put']);
// 		Route::post('delete', [IRK\CeritakitaGateway::class, 'delete']);
// 	});

// 	//Curhatku Endpoint
// 	Route::group(['prefix' => 'curhatku', 'middleware' => 'tokenauth'], function () {
// 		Route::post('get', [IRK\CurhatkuGateway::class, 'get']);
// 		Route::post('post', [IRK\CurhatkuGateway::class, 'post']);
// 		Route::post('put', [IRK\CurhatkuGateway::class, 'put']);
// 		Route::post('delete', [IRK\CurhatkuGateway::class, 'delete']);
// 	});

// 	//Motivasi Endpoint
// 	Route::group(['prefix' => 'motivasi', 'middleware' => 'tokenauth'], function () {
// 		Route::post('get', [IRK\MotivasiGateway::class, 'get']);
// 		Route::post('post', [IRK\MotivasiGateway::class, 'post']);
// 		Route::post('put', [IRK\MotivasiGateway::class, 'put']);
// 		Route::post('delete', [IRK\MotivasiGateway::class, 'delete']);
// 	});

// 	//Ideaku Endpoint
// 	Route::group(['prefix' => 'ideaku', 'middleware' => 'tokenauth'], function () {
// 		Route::post('get', [IRK\IdeakuGateway::class, 'get']);
// 		Route::post('post', [IRK\IdeakuGateway::class, 'post']);
// 		Route::post('put', [IRK\IdeakuGateway::class, 'put']);
// 		Route::post('delete', [IRK\IdeakuGateway::class, 'delete']);
// 	});

// 	//Comment Endpoint
// 	Route::group(['prefix' => 'comment', 'middleware' => 'tokenauth'], function () {
// 		Route::post('get', [IRK\CommentGateway::class, 'get']);
// 		Route::post('post', [IRK\CommentGateway::class, 'post']);
// 		Route::post('put', [IRK\CommentGateway::class, 'put']);
// 		Route::post('delete', [IRK\CommentGateway::class, 'delete']);
// 	});

// 	//Like Endpoint
// 	Route::group(['prefix' => 'like', 'middleware' => 'tokenauth'], function () {
// 		Route::post('get', [IRK\LikeGateway::class, 'get']);
// 		Route::post('post', [IRK\LikeGateway::class, 'post']);
// 		Route::post('put', [IRK\LikeGateway::class, 'put']);
// 		Route::post('delete', [IRK\LikeGateway::class, 'delete']);
// 	});

// 	//Report Endpoint
// 	Route::group(['prefix' => 'report', 'middleware' => 'tokenauth'], function () {
// 		Route::post('get', [IRK\ReportGateway::class, 'get']);
// 		Route::post('post', [IRK\ReportGateway::class, 'post']);
// 		Route::post('put', [IRK\ReportGateway::class, 'put']);
// 		Route::post('delete', [IRK\ReportGateway::class, 'delete']);
// 	});

// 	//Profile Endpoint
// 	Route::group(['prefix' => 'profile', 'middleware' => 'tokenauth'], function () {
// 		Route::post('get', [IRK\ProfileGateway::class, 'get']);
// 		Route::post('post', [IRK\ProfileGateway::class, 'post']);
// 		Route::post('put', [IRK\ProfileGateway::class, 'put']);
// 		Route::post('delete', [IRK\ProfileGateway::class, 'delete']);
// 	});

// });
//-----------------------END NEW SCHEME-----------------------------------------





//-----------------------START SCHEME VERSE-----------------------------------------
//IRK NEW Endpoint
Route::group([
	'prefix' => 'v{x}/{slug}',
	'where' => [
		'slug' => 'dev|stag|live',
		'x' => '[1-9]+'
	],
	'middleware' => 'cors'
], function () {

	//Credentials Endpoint
	Route::post('login', function ($x) {
		return app("App\\Http\\Controllers\\IRK_v{$x}\\CredentialsGateway")->LoginESS(request());
	});
	Route::post('logout', function ($x) {
		return app("App\\Http\\Controllers\\IRK_v{$x}\\CredentialsGateway")->LogoutESS(request());
	});
	Route::post('security', function ($x) {
		return app("App\\Http\\Controllers\\IRK_v{$x}\\CredentialsGateway")->Security(request());
	});

	//Utility Endpoint
	Route::group(['middleware' => 'tokenauth'], function () {
		Route::post('worker', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\UtilityGateway")->WorkerESS(request());
		});
		Route::post('unitcabang', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\UtilityGateway")->UnitCabang(request());
		});
		Route::post('direktorat', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\UtilityGateway")->Direktorat(request());
		});
		Route::post('jabatan', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\UtilityGateway")->Jabatan(request());
		});
		Route::post('presensi', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\UtilityGateway")->PresensiWFH(request());
		});
		Route::post('export', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\UtilityGateway")->FileManager(request());
		})->middleware('irkauth');
		Route::post('import', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\UtilityGateway")->FileManager(request());
		})->middleware('irkauth');
	});

	//Version Endpoint
	Route::group(['prefix' => 'version'], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\VersionGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\VersionGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\VersionGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\VersionGateway")->delete(request());
		});
	});

	//Ceritakita Endpoint
	Route::group(['prefix' => 'ceritakita', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CeritakitaGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CeritakitaGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CeritakitaGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CeritakitaGateway")->delete(request());
		});
	});

	//Curhatku Endpoint
	Route::group(['prefix' => 'curhatku', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CurhatkuGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CurhatkuGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CurhatkuGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CurhatkuGateway")->delete(request());
		});
	});

	//Motivasi Endpoint
	Route::group(['prefix' => 'motivasi', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\MotivasiGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\MotivasiGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\MotivasiGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\MotivasiGateway")->delete(request());
		});
	});

	//Ideaku Endpoint
	Route::group(['prefix' => 'ideaku', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\IdeakuGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\IdeakuGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\IdeakuGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\IdeakuGateway")->delete(request());
		});
	});

	//Ceritaku Endpoint
	Route::group(['prefix' => 'ceritaku', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CeritakuGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CeritakuGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CeritakuGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CeritakuGateway")->delete(request());
		});
	});

	//Comment Endpoint
	Route::group(['prefix' => 'comment', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CommentGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CommentGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CommentGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\CommentGateway")->delete(request());
		});
	});

	//Like Endpoint
	Route::group(['prefix' => 'like', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\LikeGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\LikeGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\LikeGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\LikeGateway")->delete(request());
		});
	});

	//Report Endpoint
	Route::group(['prefix' => 'report', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\ReportGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\ReportGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\ReportGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\ReportGateway")->delete(request());
		});
	});

	//Profile Endpoint
	Route::group(['prefix' => 'profile', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\ProfileGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\ProfileGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\ProfileGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\ProfileGateway")->delete(request());
		});
	});

	//Faq Endpoint
	Route::group(['prefix' => 'faq', 'middleware' => ['tokenauth', 'irkauth']], function () {
		Route::post('get', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\FaqGateway")->get(request());
		});
		Route::post('post', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\FaqGateway")->post(request());
		});
		Route::post('put', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\FaqGateway")->put(request());
		});
		Route::post('delete', function ($x) {
			return app("App\\Http\\Controllers\\IRK_v{$x}\\FaqGateway")->delete(request());
		});
	});

});
//-----------------------END SCHEME VERSE-----------------------------------------