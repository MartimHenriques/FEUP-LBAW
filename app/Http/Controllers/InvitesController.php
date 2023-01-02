<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Event;
use App\Models\Notification;    
use App\Models\Invite;
use App\Events\InviteN;
use App\Models\Attendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InvitesController extends Controller
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

    public function showInviteForm($id)
    {
        // Gate::authorize('manager',Project::find($id));
        // $notifications = NotificationsController::getNotifications(Auth::id());
        return view('pages.invitesCreate', ['event' => Event::find($id)]);
    }



    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     *
     */
    protected function create(int $event_id, Request $request)
    {
        $notifications = NotificationsController::getNotifications(Auth::id());
        $event = Event::find($event_id);
        $user = Auth::user();
        $id = Auth::id();

        $user_id = User::where('username', '=', $request->username)->get('id');

        $user_id = json_decode($user_id, true);
        if ($user_id === []) {
            return redirect()->back()->withErrors('User does not exist');
        }
        //dd($user_id);

        $old_invite = Invite::where('id_event', '=', $event_id)
                                    ->where('id_invitee', '=', $user_id[0]['id'])->where('accepted','=',true)->get();
        //dd($old_invite);
        $old_invite = json_decode($old_invite, true);

        if($old_invite !== []) {
            return redirect('/event/' . $event_id);
        }

        if($user_id !== null) {

            $invite = new Invite;
            $invite->id_invitee = $user_id[0]['id'];
            $invite->id_event = $event_id;
            $invite->accepted = false;
            $invite->id_organizer = $id;
            $invite->to_attend = ($request->user_role == 'ATTENDEE');
            $invite->save();

            $notification = Notification::where('type', '=', 'Invite')
                                            ->where('id_user', $user_id[0]['id'])
                                            ->where('id_event', $event_id)
                                            ->first();

            event(new InviteN($event->name,$user_id[0]['id'], $notification->id));

        }

        return redirect('/events/'.$event->id.'/info');
    }


    public function showInvite(int $id)
    {
        $not = Notification::find($id);
        $invite = Invite::where('id_invitee', '=', $not->id_user)
                                    ->where('id_event', '=', $not->id_event)->get();
        $event = Event::find($not->id_event);

        return view('pages.invites',['event' => $event, 'not'=> $not, 'invite' => $invite[0]]);
    }

    public function dealWithInvite(int $id, Request $request)
    {
        $not = Notification::find($id);
        $invite = Invite::where('id_invitee', '=', $not->id_user)
                                    ->where('id_event', '=', $not->id_event)->first();
        $user = Attendee::where('id_user', '=', $not->id_user)
                                    ->where('id_event', '=', $not->id_event)->first();
        if(!$user){
            if ($request->action == 'accept') {
                $invite->accepted = true;
                $invite->save();
                $not->read = true;
                $not->save();
                return redirect('/events/'. $not->id_event.'/info');
            }
            else {
                $not->read = true;
                $not->save();
            }
        }
        return redirect('/events');
    }
}

