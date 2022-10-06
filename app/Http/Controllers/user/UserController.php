<?php
namespace App\Http\Controllers\user;

use App\Helpers\ConfigurationHelper;
use App\Http\Controllers\RootController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends RootController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    public function initialize(){
        $response = [];
        $response['error'] ='API_OFFLINE';
        $response['messages'] =$this->api_url;
        $this->sendErrorResponse($response);
    }
    public function login(Request $request): JsonResponse
    {
        //input validation start
        $validation_rule = [];
        $validation_rule['username'] = ['required', 'alpha_dash'];
        $validation_rule['password'] = ['required'];
        $itemNew = $request->input('item');
        $this->validateInputKeys($itemNew,array_keys($validation_rule));
        $this->validateInputValues($itemNew, $validation_rule);
        //input validation end
        $user = DB::table(TABLE_USERS)->where('username', $itemNew['username'])->first();
        if($user){
            $time=time();
            if ($user->status == SYSTEM_STATUS_ACTIVE) {
                if (Hash::check($itemNew['password'], $user->password)) {
                    $mobile_verification_required=true;
                    //check mobile verification required
                    //1.if personal verification off
                    if($user->mobile_authentication_off_end>$time)//for user if inactive
                    {
                        $mobile_verification_required=false;
                    }
                    //2.if global verification off
                    if(!ConfigurationHelper::isLoginMobileVerificationOn()){
                        $mobile_verification_required=false;
                    }
                    //3.check browser already validated or maximum browser

                    if($mobile_verification_required){
                        return response()->json(['error' => 'MOBILE_VERIFICATION_REQUIRED', 'messages' => __('Verify your mobile')]);
                    }
                    else{
                        //user
                        //menus
                        $response['user']=$user;
                        return response()->json(['error' => '', 'messages' => __('Logged in successfully'), 'data' =>$response]);
                    }

                }
                else{
                    //TODO wrong consecutive password settings
                    return response()->json(['error' => 'INVALID_CREDENTIALS', 'messages' => __('Wrong Password')]);
                }

            }
            else{
                return response()->json(['error' => 'USER_INACTIVE', 'messages' => __('This user account has been suspended')]);
            }
        }
        else{
            return response()->json(['error' => 'USER_NOT_FOUND', 'messages' => __('This user does not exits')]);
        }
    }
}
