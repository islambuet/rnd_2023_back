<?php
namespace App\Http\Controllers\user;

use App\Helpers\ConfigurationHelper;
use App\Helpers\UserHelper;
use App\Http\Controllers\RootController;
use Carbon\Carbon;
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
        $response['error'] ='';
        if($this->user){
            $response['user']=$this->getUserForApi($this->user);
        }
        return response()->json($response);
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
            $time=Carbon::now();
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
                    else if(!ConfigurationHelper::isLoginMobileVerificationOn()){
                        $mobile_verification_required=false;
                    }
                    //3.check browser validated before
                    else{
                        $authTokenInfo=UserHelper::getAuthTokenInfo();
                        if($authTokenInfo){
                            //was Logged within 10 days
                            if(($authTokenInfo->user_id== $user->id) &&($authTokenInfo->expires_at> $time->copy()->subDays(10))){
                                $mobile_verification_required=false;
                            }
                        }
                    }
                    if($mobile_verification_required){
                        //send otp
                        return response()->json(['error' => 'MOBILE_VERIFICATION_REQUIRED', 'messages' => __('Verify your mobile')]);
                    }
                    else{
                        //user
                        $user->authToken=UserHelper::getNewAuthToken($user);
                        //menus
                        $response['error']='';
                        $response['messages']=__('Logged in successfully');
                        $response['user']=$this->getUserForApi($user);
                        return response()->json($response);
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
    private function getUserForApi($user): object
    {
        $apiUser= (object) [];
        foreach(['id','name','authToken'] as $key){
            $apiUser->$key=$user->$key;
        }
        $apiUser->infos = (object)($user->infos ? json_decode($user->infos, true) :  []);
        $apiUser->profile_picture_url = property_exists($apiUser->infos,'profile_picture')?ConfigurationHelper::getUploadedImageBaseurl().$apiUser->infos->profile_picture:'';
        //include tasks
        return $apiUser;
    }
    public function logout(): JsonResponse
    {
        $authTokenInfo=UserHelper::getAuthTokenInfo();
        if($authTokenInfo){
            DB::table(TABLE_USER_AUTH_TOKENS)->where('id',$authTokenInfo->id)->update(['expires_at'=>Carbon::now()]);
        }
        return response()->json(['error' => '', 'messages' => __('Logout success')]);
    }
}
