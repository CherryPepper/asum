<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployerRequest;
use App\Http\Requests\EditEmployerRequest;
use App\Models\Role;
use App\Models\UserRegionsAccess;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class EmployerController extends Controller
{
    public function getCreate(){
        $data['roles'] = Role::where('id', '!=', 4)->get();
        $data['addresses'] = $this->getSelectAddresses();

        return view('employers.create', $data);
    }

    public function postCreate(CreateEmployerRequest $request){
        $employer = User::create(collect($request->all())->only([
            'first_name', 'last_name', 'email', 'login', 'role_id'
        ])->merge([
                'password' => \Hash::make($request->password)]
        )->toArray());

        $this->access_regions($request, $employer->id);

        return redirect()->back()->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Сотрудник успешно добавлен'
            ]
        ]);
    }

    public function getEdit($id){
        $data['roles'] = Role::where('id', '!=', 4)->get();
        $data['addresses'] = $this->getSelectAddresses();
        $data['access_regions'] = implode(',', collect(UserRegionsAccess::where('user_id', $id)->get(['address_id'])->toArray())->flatten()->toArray());

        $data['employer'] = User::with('role')->where([
            ['role_id', '!=', 4],
            'display' => 1,
            'id' => $id
        ])->firstOrFail();

        $data['menu_uri'] = route('employer.list');

        return view('employers.edit', $data);
    }

    public function postEdit(EditEmployerRequest $request){
        $data = collect($request->all())->only([
            'first_name', 'last_name', 'email', 'login', 'role_id'
        ]);
        if(isset($request->password))
            $data = $data->merge(['password' => \Hash::make($request->password)]);

        User::where('id', $request->id)->update($data->toArray());
        $this->access_regions($request, $request->id);

        return redirect(route('employer.list'))->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Данные о сотруднике успешно изменены'
            ]
        ]);
    }

    public function getDelete($id){
        User::where('id', $id)->update([
            'deleted_at' => Carbon::now(),
            'display' => 0
        ]);

        return redirect()->back()->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Сотрудник успешно удален'
            ]
        ]);
    }

    public function getList(Request $request){
        $data['roles'] = Role::where('id', '!=', 4)->get();

        $query = User::with('role')->where([
            ['role_id', '!=', 4],
            'display' => 1
        ]);
        if((int)$request->role_id)
            $query->where('role_id', (int)$request->role_id);

        $data['employers'] = $query->paginate(10);

        return view('employers.list', $data);
    }

    public function getEmployers(Request $request){
        $role_id = $request->role_id ? $request->role_id : old('role_id');
        $employers = User::where('role_id', '!=', 4)->where('role_id', $role_id)
            ->get(['id as value', DB::raw('CONCAT(first_name, " ",last_name) as text')]);

        if($request->ajax())
            return response()->json($employers);
        else
            return $employers;
    }

    private function access_regions($request, $user_id){
        UserRegionsAccess::where('user_id', $user_id)->delete();

        if(!empty($request->access_regions)){
            $data = [];
            $access_regions = explode(',', $request->access_regions);

            foreach ($access_regions as $id)
                $data[] = [
                    'user_id' => $user_id,
                    'address_id' => $id
                ];

            UserRegionsAccess::insert($data);
        }
    }
}
