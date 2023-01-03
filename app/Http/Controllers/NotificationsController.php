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

    
    public function clearAll($id) {
        $notifications = Notification::where('id_user','=',$id)->get();
        foreach ($notifications as $notification) {
            $notification->delete();
        }
        return redirect()->back();
    }
    
}