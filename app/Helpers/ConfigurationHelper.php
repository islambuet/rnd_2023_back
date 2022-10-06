<?php
    namespace App\Helpers;
    use Illuminate\Support\Facades\DB;
    class ConfigurationHelper
    {
        public static array $config = array();
        public static function load_config()
        {
            $results = DB::table(TABLE_CONFIGURATIONS)->where('status', SYSTEM_STATUS_ACTIVE)->get();
            foreach($results as $result){
                self::$config[$result->purpose]=$result->config_value;
            }
        }
        public static function isApiOffline(): bool
        {
            return isset(self::$config[SYSTEM_CONFIGURATIONS_SITE_OFF_LINE])&&(self::$config[SYSTEM_CONFIGURATIONS_SITE_OFF_LINE]==1);
        }
        public static function isLoginMobileVerificationOn(): bool
        {
            return isset(self::$config[SYSTEM_CONFIGURATIONS_LOGIN_MOBILE_VERIFICATION])&&(self::$config[SYSTEM_CONFIGURATIONS_LOGIN_MOBILE_VERIFICATION]==1);
        }
    }
