<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;



abstract class RootController extends Controller
{
    public $api_url;
    public function __construct(Request $request)
    {
        $api_url=substr(\Request::path(),strlen('api/'));
        $this->api_url=substr($api_url,0,strrpos($api_url,'/'));
    }
    public function sendErrorResponse($errorResponse){
        $response = response()->json($errorResponse);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->send();
        exit;
    }
}
