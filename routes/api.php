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