<?php

use App\Models\UserPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('getMedicineSalePrice')) {
    function getMedicineSalePrice($medicineId) {
        $lastTwoEntry = \App\Models\Pharmacy::where('medicine_id', $medicineId)->select('sale_price')->latest()->take(1)->pluck('sale_price')->toArray();
  $maxValue = 0;
       if(!empty($lastTwoEntry)){
        $maxValue = max($lastTwoEntry);
       }
        return $maxValue;
    }
}
if(!function_exists('get_avatar')){
    function get_avatar($str){
        $acronym = '';
        $word = '';
        $words = preg_split("/(\s|\-|\.)/", $str);
        foreach($words as $w) {
            $acronym .= substr($w,0,1);
        }
        $word = $word . $acronym ;
        return $word;
    }
}

if(!function_exists('user_permissions')){
    function user_permissions(){
        $userPermissions = UserPermission::where('user_id', Auth::user()->id)->with('permission_name')
            ->get()->pluck('permission_name.permission_name', 'permission_name.id')->toArray();
        return $userPermissions;
    }
}




