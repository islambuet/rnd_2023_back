<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
class CommonHelper
{
    public static function get_rnd_code($variety,$year,$full=false): string
    {

        $code=$variety->crop_code.'-'.$variety->crop_type_code;
        if($variety->rnd_ordering>0){
            $code.='-'.str_pad($variety->rnd_ordering,2, '0',STR_PAD_LEFT);
            if($full){
                if($variety->whose=='ARM'){
                    $code.='-ARM';
                }
                else if($variety->whose=='Principal'){
                    $code.='-'.$variety->principal_info->code;
                }
                else if($variety->whose=='Competitor'){
                    $code.='-'.$variety->competitor_info->code;
                }
                else{
                    $code.='-XXX';
                }
                $code.='-'.substr($year,-2);
            }
        }
        else{
            $code='';
        }
        return $code;
    }
}
