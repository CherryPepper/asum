<?php

namespace App\Http\Controllers;

use App\Helpers\Addresses;
use App\Http\Requests\AddAddressRequest;
use App\Http\Requests\GetAddressListRequest;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function getAddressList(GetAddressListRequest $request){
        $parent_id = (int)$request->input('parent_id', 0);
        $addresses = Address::where('parent_id', $parent_id)->get([
            'id as value', 'name as text'
        ]);

        return response()->json($addresses);
    }

    public function addAddress(AddAddressRequest $request){
        $parent_id = (int)$request->input('parent_id', 0);
        $name = strtr(trim(mb_convert_case((mb_strtolower($request->input('name'))), MB_CASE_TITLE)), ['ё' => 'е']);

        $parent = Address::where('id', $parent_id)->first();
        $nst_lvl = $parent ? ++$parent->nst_lvl : 1;

        $name = $nst_lvl <= 2 ? $name : strtr($name, [' ' => '']);

        $address = Address::firstOrCreate([
            'parent_id' => $parent_id,
            'nst_lvl' => $nst_lvl,
            'name' => $name
        ]);

        $path = $address->nst_lvl > 2 ? $address->name : $address->id;
        $address->path = $parent ? $parent->path.','.$path : $path;
        $address->path_childs = $address->id;
        $address->hash = Addresses::adrHash($address->path);
        $address->save();


        for ($i = $nst_lvl; $i > 1; $i--){
            $adr = Address::where('id', $parent_id)->first();

            if(!in_array($address->id, explode(',', $adr->path_childs))){
                $adr->path_childs = $adr->path_childs.','.$address->id;
                $adr->save();
            }

            $parent_id = $adr->parent_id;
        }

        return response()->json([
            'value' => $address->id,
            'text' => $address->name
        ]);
    }
}
