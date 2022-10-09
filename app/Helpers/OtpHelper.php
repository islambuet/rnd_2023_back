<?php
namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OtpHelper {
    //reason 0=login
    public static function setOtp($user_id,$reason=0): array
    {
        $time=Carbon::now();
        $result = DB::table(TABLE_USER_OTPS)->where('user_id',$user_id)->orderBy('id','DESC')->first();
        if($result && ($result->last_used_at==null) && ($result->expires_at>$time)){
                return ['error'=>'OLD_OTP','messages'=>'old otp','otp'=>$result];
        }
        else{
            $itemNew=array();
            $itemNew['user_id']=$user_id;
            $itemNew['reason']=$reason;
            $itemNew['otp']=rand(1000,999999);
            $itemNew['created_at']=$time;
            $itemNew['expires_at']=$time->copy()->addSeconds(ConfigurationHelper::getOtpExpireDuration());
            $itemNew['id'] = DB::table(TABLE_USER_OTPS)->insertGetId($itemNew);
            return ['error'=>'','messages'=>'new otp','otp'=>$itemNew];
        }
    }

}
