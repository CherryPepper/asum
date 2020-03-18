<?php
namespace App\Helpers;

use App\Models\Address;

class Addresses
{
     public static function dec2base($dec,$base,$digits) {
        if($base<2 or $base>256) die("Invalid Base: ".$base);
        bcscale(0);
        $value="";

        while($dec>$base-1) {
            $rest=bcmod($dec,$base);
            $dec=bcdiv($dec,$base);
            $value=$digits[$rest].$value;
        }
        $value=$digits[intval($dec)].$value;
        return (string) $value;
    }

    public static function adrHash($mpath, $sep = ',') {
        define('BASE95', ' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~');
        $adtHash = '';
        $chunks = explode($sep, trim($mpath, $sep));

        foreach($chunks as $chunk)
            $adtHash .= str_pad(self::dec2base($chunk, 95, BASE95), 5, ' ', STR_PAD_LEFT);


        return $adtHash;
    }

    public static function AdrString($recursive){
         $string = '';


         $obj = self::AdrObj($recursive);

         if(isset($obj->region)){
             $string .= $obj->region.' '.$obj->street.' '.$obj->home;
             $string .= isset($obj->apartment) ? ' кв.'.$obj->apartment : '';
         }

         return $string;
    }

    public static function AdrObj($recursive){
        $arr = [];

        if(@$recursive->nst_lvl == 4){
            $arr['apartment_id'] = $recursive->id;
            $arr['apartment'] = $recursive->name;

            $arr['home_id'] = $recursive->parentRecursive->id;
            $arr['home'] = $recursive->parentRecursive->name;

            $arr['street_id'] = $recursive->parentRecursive->parentRecursive->id;
            $arr['street'] = $recursive->parentRecursive->parentRecursive->name;

            $arr['region_id'] = $recursive->parentRecursive->parentRecursive->parentRecursive->id;
            $arr['region'] = $recursive->parentRecursive->parentRecursive->parentRecursive->name;
        }
        else if(@$recursive->nst_lvl == 3){
            $arr['home_id'] = $recursive->id;
            $arr['home'] = $recursive->name;

            $arr['street_id'] = $recursive->parentRecursive->id;
            $arr['street'] = $recursive->parentRecursive->name;

            $arr['region_id'] = $recursive->parentRecursive->parentRecursive->id;
            $arr['region'] = $recursive->parentRecursive->parentRecursive->name;
        }

        return (object)$arr;
    }

    public static function getLast($addresses){
        $id = null;

        if(!empty($addresses)){
            foreach ($addresses as $adr_id){
                if($adr_id > 0) $id = $adr_id;
            }
        }

        return $id;
    }

    public static function getAdrStringFromRequest(){
        $request = app('request')->input('address');
        $adr_string = '';
        $adr_types = ['region' => 'Р-н', 'street' => 'ул.', 'home' => 'д.', 'apartment' => 'кв.'];

        foreach ($adr_types as $key=>$type){
            if(isset($request[$key]) && (!empty($request[$key]))){
                $data = Address::where('id', $request[$key])->first();
                $adr_string .= $type.' '.$data->name.' ';
            }
        }

        return $adr_string;
    }
}