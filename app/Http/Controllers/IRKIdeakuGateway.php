<?php

namespace App\Http\Controllers;



use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Crypt;

use Maatwebsite\Excel\Facades\Excel;

use App\Helper\IRKHelp;

class IRKIdeakuGateway extends Controller
{
    private $resultresp;
	private $dataresp;
	private $messageresp;
	private $statusresp;
	private $ttldataresp;
	private $statuscoderesp;

    public function __construct(Request $request)
    {
        // Call the parent constructor
        //parent::__construct();
        
        $slug = $request->route('slug');
		$this->slug = $slug;

        $env = config('app.env');
        $this->env = $env;

        $helper = new IRKHelp($request);
		$this->helper = $helper;

		$segment = $helper->Segment($slug);
		$this->authorize = $segment['authorize'];
		$this->config = $segment['config'];
        $this->path = $segment['path'];

		$idkey = $helper->Environment($env);
		$this->tokenid = $idkey['tokenid'];

        $signature = $helper->Identifer($request);
		$this->signature = $signature;

    }


    public function get(Request $request){
        
    }

    public function post(Request $request){
        
    }

    public function put(Request $request){
        
    }

    public function delete(Request $request){
        
    }

}