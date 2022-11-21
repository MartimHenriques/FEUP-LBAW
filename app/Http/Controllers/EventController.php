<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;
use App\Models\Message;
use App\Models\Event_Organizer;

class EventController extends Controller
{
    /**
     * Shows the event for a given id.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
      $event = Event::find($id);
      $messages = Message::where('id_event','=',$id)->get();
      //pq q o authorize n funciona?
      return view('pages.event', ['event' => $event, 'messages' => $messages]);
    }

    public function showMyEvents()
    {
      $user = Auth::user()->id;
      /*
      $myeventsid = Event_Organizer::where('id_user','=',$user)->get(['id_event']);
      $myevents = Event::where('id_event','=',$myeventsid)->get();*/
      $myevents = DB::table('event')
          ->join('event_organizer', 'event.id', '=', 'event_organizer.id_event')
          ->where('event_organizer.id_user', $user)
          ->get();
      //pq q o authorize n funciona?
      return view('pages.myevents', ['myevents' => $myevents]);
    }


    public static function showEvents(){
      if(Auth::check()){
        $events = Event::get();
        return view('pages.feed',['events' => $events]);
      }
      else{
        $events = Event::where('visibility', 1)->get();
        return view('pages.feed',['events' => $events]);
      }

    }
    /**
     * Creates a new card.
     *
     * @return Card The card created.
     */
    public function create(Request $request)
    {
      $card = new Card();

      $this->authorize('create', $card);

      $card->name = $request->input('name');
      $card->user_id = Auth::user()->id;
      $card->save();

      return $card;
    }

    public function delete(Request $request, $id)
    {
      $card = Card::find($id);

      $this->authorize('delete', $card);
      $card->delete();

      return $card;
    }

    //n terminei ainda
    public function join(Request $request, Event $event)
    {
      if (!Auth::check()) return redirect('/login');
      $user = User::find(Auth::user()->id);
      $this->authorize('attendee', [$user, $event]);
      $event->invites()->attach($user->id); //parei aqui
      return view('pages.event', [
        'event' => $event,
        'user' => User::find(Auth::user()->id)]);
    }
}
