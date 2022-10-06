<?php
namespace App\Http\Controllers;
use App\Helpers\ConfigurationHelper;
use Illuminate\Http\Request;

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
                $this->sendErrorResponse(['error'=>'API_OFFLINE','errorMessage' => __('Site is Currently Offline.')]);
            }
        }
    }

}
