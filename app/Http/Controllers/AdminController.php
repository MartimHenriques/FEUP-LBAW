<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\NottificationsController;

use App\Models\Poll;
use App\Models\Attendee;
use App\Models\ChooseOption;
use App\Models\Event;
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
        $user = User::where(['id'=>$id]);
        $user->delete();
        return redirect()->back();
      }

      /**
     * The user is blocked.
     *
     * @return Redirect back to the page
     */
      public function blockUser($id){
        DB::table('users')->where(['id'=>$id])->update(['is_blocked'=>TRUE]);
        return redirect()->back();
      }

      /**
     * The user is unblocked.
     *
     * @return Redirect back to the page
     */
    public function unblockUser($id){
      DB::table('users')->where(['id'=>$id])->update(['is_blocked'=>FALSE]);
      return redirect()->back();
    }

    /**
     * The user is deleted.
     *
     * @return Redirect back to the page
     */
    public function deleteEvent($id){
      $event = Event::where(['id'=>$id]);
      $event->delete();
      return redirect()->back();
    }
}