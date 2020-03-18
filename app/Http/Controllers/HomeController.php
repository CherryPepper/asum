<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        switch ($this->user->role->slug){
            case 'administrator':
                return redirect(route('client.list'));
                break;
            case 'manager':
                return redirect(route('client.list'));
                break;
            case 'technician':
                return redirect(route('tasks.list', ['type' => 'process']));
                break;
            case 'user':
                return redirect(route('user.info'));
                break;
            case 'tozelesh':
                return redirect(route('tozelesh.map'));
                break;

            default:
                return redirect(route('inProgress'));
        }
    }

    public function inProgress(){
        return view('in_progress');
    }
}
