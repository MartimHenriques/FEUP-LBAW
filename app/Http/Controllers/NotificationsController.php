<?php

namespace App\Http\Controllers;


use App\Models\Notification;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    static function getNotifications($id)
    {
        $my_notifications = Notification::where('id_user','=',$id)->orderByDesc('date')->get();
        return $my_notifications;
    }

    static function showNotifications()
    {
        $my_notifications = NotificationsController::getNotifications(Auth::id());
        return view('pages.notifications', ['notifications' => $my_notifications]);
    }

    public function managerNotification($id) {
        $notification = Notification::find($id);
        $notification->read = true;
        $notification->save();
        return redirect('/event/'.$notification->invitation_project_id.'/members');
    }

    /*
    public function clearNotifications($id) {
        $notifications = Notification::where('user_id','=',$id)->get();
        foreach ($notifications as $notification) {
            $notification->seen = true;
            $notification->save();
        }
        return redirect()->back();
    }
    */
}