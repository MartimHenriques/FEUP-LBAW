<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Poll;
use App\Models\Attendee;
use App\Models\ChooseOption;
use App\Models\Notification;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Shows the admin page.
     *
     * @return Response
     */
    public function show()
    {
      $users = User::all();
      return view('pages.admin',['users'=>$users]);
    }

    /**
     * The user is deleted.
     *
     * @return Redirect back to the page
     */
    public function deleteUser($id){
        //to improve, not the best use
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
}