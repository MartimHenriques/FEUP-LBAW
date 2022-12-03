<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
      $users = User::all();
      return view('pages.adminUsers',['users'=>$users]);
    }

    /**
     * Shows the adminEvents page.
     *
     * @return Response
     */
    public function showEvents()
    {
      $events = Event::all();
      return view('pages.adminEvents',['events'=>$events]);
    }

    /**
     * Shows the adminReports page.
     *
     * @return Response
     */
    public function showReports()
    {
      $reports = Report::all();
      return view('pages.adminReports',['reports'=>$reports]);
    }

    /**
     * The user is deleted.
     *
     * @return Redirect back to the page
     */
    public function deleteUser($id){
        //to improve, not the best use; ON DELETE CASCADE
        $poll = Poll::where(['id_user' => $id]);
        $poll->delete();
        $notification = Notification::where(['id_user' => $id]);
        $notification->delete();
        $ch_option = ChooseOption::where(['id_user' => $id]);
        $ch_option->delete();
        $attendee = Attendee::where(['id_user' => $id]);
        $attendee->delete();
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
}