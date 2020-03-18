<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Helpers\Addresses;
use App\Models\Address;
use App\Models\Role;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function getCreate(Request $request){
        $data['addresses'] = $this->getSelectAddresses();
        $data['roles'] = Role::where('id', '!=', 4)->get();

        $emp_controller = new EmployerController();
        $data['employers'] = $emp_controller->getEmployers($request);

        return view('task.create', $data);
    }

    public function postCreate(CreateTaskRequest $request){

        Task::create(collect($request->all())->only([
            'message', 'role_id', 'employer_id'
        ])->merge([
                'address_id' => Addresses::getLast($request->address),
                'date_start' => Carbon::createFromFormat('d.m.Y', $request->date_start)->format('Y-m-d 00:00:00'),
                'date_end' => Carbon::createFromFormat('d.m.Y', $request->date_end)->format('Y-m-d 00:00:00'),
                'from_user_id' => \Auth::user()->id
        ])->toArray());

        return redirect()->back()->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Задание успешно добавлено'
            ]
        ]);
    }

    public function getList($type = true, Request $request){
        $query = Task::with(['employer', 'address.parentRecursive'])->checkAccess()
            ->where('deleted_at', null)->orderBy('id', 'DESC');

        if($type == 'completed')
            $query->whereNotNull('date_complete');
        if($type == 'process')
            $query->where('date_complete', null);
        if($this->user->role->slug == 'technician')
            $query->where('employer_id', $this->user->id);
        if($request->date_from)
            $query->where('date_start', '>=', Carbon::createFromFormat('d.m.Y', $request->date_from)->format('Y-m-d 00:00:00'));
        if($request->date_to)
            $query->where('date_start', '<=', Carbon::createFromFormat('d.m.Y', $request->date_to)->format('Y-m-d 00:00:00'));
        if($request->q)
            $query->where('message', 'LIKE', "%{$request->q}%");

        $data['tasks'] = $query->paginate(10);
        $data['type'] = $type;

        return view('task.list', $data);
    }

    public function setComplete($id){
        Task::where('id', $id)->update([
            'date_complete' => Carbon::now()
        ]);

        return redirect()->back()->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Задание отмечено как завершенное'
            ]
        ]);
    }

    public function getDelete($id){
        Task::where('id', $id)->update([
            'deleted_at' => Carbon::now()
        ]);

        return redirect()->back()->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Задание успешно удалено'
            ]
        ]);
    }
}
