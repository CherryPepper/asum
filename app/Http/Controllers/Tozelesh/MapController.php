<?php

namespace App\Http\Controllers\Tozelesh;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditObjectRequest;
use App\Http\Requests\PasswordConfirmationRequest;
use App\Http\Requests\SaveLampsRequest;
use App\Http\Requests\SaveObjectRequest;
use App\Models\Tozelesh\Lamp;
use App\Models\Tozelesh\LampType;
use App\Models\Tozelesh\TzObject;
use Carbon\Carbon;

class MapController extends Controller
{
    public function getMap(){
        $data['objects'] = TzObject::with(['meter.address.parentRecursive', 'lamps.type'])
            ->where('display', 1)->get();

        $data['lamp_types'] = LampType::where('display', 1)->get();

        return view('tozelesh.map', $data);
    }

    public function postSaveLamps(SaveLampsRequest $request){
        $this->saveLamps($request);

        return [
            'status' => 'success',
            'message' => 'Фонари успешно добавлены к объекту'
        ];
    }

    public function postSaveObject(SaveObjectRequest $request){
        TzObject::where('id', $request->object_id)->update([
            'display' => 1,
            'time_on' => $request->time_on,
            'time_off' => $request->time_off
        ]);

        return [
            'status' => 'success',
            'message' => 'Объект успешно добавлен. Обновление страницы через 5 сек'
        ];
    }

    public function postEditObject(EditObjectRequest $request){
        Lamp::where('object_id', $request->object_id)->delete();
        $this->saveLamps($request);

        TzObject::where('id', $request->object_id)->update([
            'time_on' => $request->time_on,
            'time_off' => $request->time_off
        ]);

        return [
            'status' => 'success',
            'message' => 'Объект успешно изменен. Обновление страницы через 5 сек'
        ];
    }

    public function postDeleteObject(PasswordConfirmationRequest $request){
        TzObject::deleteObject($request->id);

        return [
            'status' => 'success',
            'message' => 'Объект успешно удален.',
            'clearMap' => ['object_id' => $request->id]
        ];
    }

    public function getCancelObjectCreation($id){
        TzObject::deleteObject($id);
    }

    private function saveLamps($request){
        $lamps = [];

        $i = 0;
        foreach ($request->lamps as $lamp){
            $lamp = json_decode($lamp);

            $lamps[$i]['object_id'] = $request->object_id;
            $lamps[$i]['lamp_id'] = $lamp->type;
            $lamps[$i]['coordinates'] = json_encode($lamp->coordinates);
            $lamps[$i]['created_at'] = Carbon::now();

            $i++;
        }

        return Lamp::insert($lamps);
    }
}
