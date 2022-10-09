<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;

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
    public static function getNewAuthToken($user_id){
        //generate token
        //save to db with browser info ip
        // and inactive max browser token
        //return with id_token

    }
}
