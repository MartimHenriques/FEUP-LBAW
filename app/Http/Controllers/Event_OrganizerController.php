<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Event_Organizer;
use App\Models\Attendee;

class Event_OrganizerController extends Controller
{
    public function makeAnOrganizer($id_user, $id_event){
        $attendee = Attendee::where('id_user', '=', $id_user)->where('id_event','=', $id_event);
        $attendee->delete();
        
        DB::table('event_organizer')->insert(
            array(
                'id_user' => $id_user,
                'id_event' => $id_event,
            )
        );
        return redirect()->back();
    }

}
