<?php
namespace App\Http\Controllers;
use App\Helpers\ConfigurationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class RootController extends Controller
{
    public $api_url;
    public function __construct(Request $request)
    {
        $api_url=substr($request->path(),strlen('api/'));
        $this->api_url=substr($api_url,0,strrpos($api_url,'/'));
        ConfigurationHelper::load_config();
        $this->checkApiOffline($request);
    }
    public function sendErrorResponse($errorResponse){
        $response = response()->json($errorResponse);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->send();
        exit;
    }
    private function checkApiOffline(Request $request)
    {
        if(ConfigurationHelper::isApiOffline())
        {
            $path=$request->path();
            if(!(
                str_starts_with($path, 'api/user/')||
                str_starts_with($path, 'api/system-configurations/')

            ))
            {
                $this->sendErrorResponse(['error'=>'API_OFFLINE','messages' => __('Site is Currently Offline.')]);
            }
        }
    }
    public function validateInputKeys($inputs,$keys){
        if(!is_array($inputs)){
            $this->sendErrorResponse(['error'=>'INPUT_NOT_FOUND','messages' => __("Input Not Found")]);
        }
        //checking if any invalid input
        foreach($inputs as $key=>$value){
            if( !$key || (!in_array ($key,$keys))){
                $this->sendErrorResponse(['error'=>'VALIDATION_FAILED','messages'=>__($key." is not a valid Input")]);
            }
        }
    }
    public function validateInputValues($inputs, $validation_rule)
    {
        $validator = Validator::make($inputs, $validation_rule);
        if ($validator->fails()) {
            $this->sendErrorResponse(['error'=>'VALIDATION_FAILED','messages'=>$validator->errors()]);
        }
    }

}
