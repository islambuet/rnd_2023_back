<?php
namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserHelper {

	public static $loggedUser = null;
	public static function getLoggedUser(){
        //self::getAuthToken();
        //if expires return null user
        //get user
        //update used_at and expire at
        //echo \Request::bearerToken();
    }
    public static function getAuthToken(){
        //bet bearerToken
        //split id and token
        //get from database
    }
    public static function getNewAuthToken($user): string
    {
        //generate token
        $authToken=Hash::make(bin2hex(random_bytes(rand(10,15))));
        $clientInfo=array();
        $clientInfo['REMOTE_ADDR']=\Request::server('REMOTE_ADDR');
        $clientInfo['HTTP_USER_AGENT']=\Request::server('HTTP_USER_AGENT');

        $time=Carbon::now();
        //inactive browsers ids
        $removeTokenIds=array();
        $query=DB::table(TABLE_USER_AUTH_TOKENS);
        $query->where('user_id',$user->id);
        $query->where('expires_at','>=',$time);
        $query->orderBy('id','DESC');
        $query->offset($user->max_logged_browser-1);
        $query->limit(500);
        $results = $query->get();
        foreach ($results as $result) {
            $removeTokenIds[]=$result->id;
        }

        DB::beginTransaction();
        try{
            //save token with client info

            $itemNew=array();
            $itemNew['user_id']=$user->id;
            $itemNew['token']=$authToken;
            $itemNew['device_info']=json_encode($clientInfo);
            $itemNew['created_at']=$time;
            $itemNew['last_used_at']=$time;
            $itemNew['expires_at']=$time->copy()->addHours(ConfigurationHelper::getLoginSessionExpireHours());
            $id = DB::table(TABLE_USER_AUTH_TOKENS)->insertGetId($itemNew);
            // and inactive max browser token
            if($removeTokenIds){
                DB::table(TABLE_USER_AUTH_TOKENS)->whereIn('id',$removeTokenIds)->update(['expires_at'=>$time]);
            }
            DB::commit();
        }
        catch (\Exception $ex) {
            print_r($ex);
            // ELSE rollback & throw exception
            DB::rollback();
            return response()->json(['error' => 'DB_SAVE_FAILED', 'errorMessage'=>__('response.DB_SAVE_FAILED')],408);
        }


        //return with id_token
        return $id.'_'.$authToken;
    }
}
