<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationSendRequest;
use App\Models\Notification;
use App\User;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function getSend(){
        $data['notifications'] = Notification::groupBy('chunk')
            ->where('chunk', '!=', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('notifications.send', $data);
    }

    public function postSend(NotificationSendRequest $request){
        $users = User::where('role_id', 4)
            ->where('display', 1)->get();

        $last_group = @(int)Notification::orderBy('chunk', 'desc')->first()->chunk+1;

        $notifications = [];
        $created_at = Carbon::now();

        foreach ($users as $user){
            $notifications[] = [
                'chunk' => $last_group,
                'user_id' => $user->id,
                'title' => $request->title,
                'message' => $request->message,
                'created_at' => $created_at,
                'unread' => 1
            ];
        }

        $chunks = collect($notifications)->chunk(2000);
        $chunks->each(function($chunk){
            Notification::insert($chunk->toArray());
        });

        return redirect(route('notifications.send'))->with('toast_messages', [
            [
                'status' => 'success',
                'message' => 'Уведомления успешно отправлены'
            ]
        ]);
    }

    public function getList(){
        Notification::where('user_id', $this->user->id)->update([
            'unread' => 0
        ]);

        $data['notifications'] = Notification::where('user_id', $this->user->id)
            ->orderBy('created_at', 'desc')->paginate(12);

        return view('notifications.list', $data);
    }
}
