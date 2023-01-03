<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\NottificationsController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use App\Models\Poll;
use App\Models\Attendee;
use App\Models\ChooseOption;
use App\Models\Event;
use App\Models\Event_Organizer;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Shows the adminUsers page.
     *
     * @return Response
     */
    public function showUsers()
    {
      Gate::authorize('admin', Auth::user());
      $users = User::where('is_admin', '!=', true)->paginate(5);
      return view('pages.adminUsers',['users'=>$users]);
    }

    /**
     * Shows the adminEvents page.
     *
     * @return Response
     */
    public function showEvents()
    {
      Gate::authorize('admin', Auth::user());
      $events = Event::paginate(5);
      return view('pages.adminEvents',['events'=>$events]);
    }

    /**
     * Shows the adminReports page.
     *
     * @return Response
     */
    public function showReports()
    {
      Gate::authorize('admin', Auth::user());
      $reports = Report::paginate(5);
      $admin_notifications = NotificationsController::getNotifications(Auth::id());
      return view('pages.adminReports',['reports'=>$reports, 'notifications'=>$admin_notifications]);
    }

    /**
     * The user is deleted.
     *
     * @return Redirect back to the page
     */
    public function deleteUser($id){ 

      $user = User::find($id);

      $count = User::where('username','like','Anonymous_%')->count();
      $username = "Anonymous_" . strval($count);

      $user->username = $username;
      $user->picture = "default.png";
      $user->email = $username . "@anonymous.com";
      $user->password = Hash::make(Str::random(10));
      $user->save();
      
      DB::table('users')->where(['id'=>$id])->update(['picture'=>'', 'username'=>$username], );

      $events = Event_Organizer::where(['id_user'=>$id])->pluck('id_event');

      if (!empty($events)) {
        foreach ($events as $event){
          $count = DB::table('event_organizer')->where(['id_event'=>$event])->count();
          if ($count == 1) {
            DB::table('event_organizer')->insert(['id_user' => Auth::id(), 'id_event' => $event]);            
          }
        }
        DB::table('event_organizer')->where(['id_user' => $id])->delete();
        DB::table('attendee')->where(['id_user' => $id])->delete();
      }

      return redirect()->back();
      }

      /**
     * The user is blocked.
     *
     * @return Redirect back to the page
     */
      public function blockUser($id){

        Gate::authorize('admin', Auth::user());

        DB::table('users')->where(['id'=>$id])->update(['is_blocked'=>TRUE]);
        return redirect()->back();
      }

      /**
     * The user is unblocked.
     *
     * @return Redirect back to the page
     */
    public function unblockUser($id){
      Gate::authorize('admin', Auth::user());

      DB::table('users')->where(['id'=>$id])->update(['is_blocked'=>FALSE]);
      return redirect()->back();
    }

    /**
     * The event is deleted.
     *
     * @return Redirect back to the page
     */
    public function deleteEvent($id){
      Attendee::where(['id_event' => $id])
          ->whereNotIn('id_user',Event_Organizer::where(['id_event'=>$id])->pluck('id_user'))
          ->delete();
      Event::where(['id'=>$id])->delete();
      
      return redirect()->back();
    }
}