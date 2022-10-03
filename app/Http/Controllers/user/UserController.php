<?php
namespace App\Http\Controllers\user;

use App\Http\Controllers\RootController;
use Illuminate\Http\Request;


class UserController extends RootController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    public function initialize(){
        $response = [];
        $response['error'] ='API_OFFLINE';
        $response['errorMessage'] =$this->api_url;
        $this->sendErrorResponse($response);
    }

}
