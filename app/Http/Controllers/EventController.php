<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Event;
use App\Models\Message;
use App\Models\Event_Organizer;
use App\Models\User;

class EventController extends Controller
{
    /**
     * Shows the form to create an event.
     *
     * @return Response
     */
    public function showForm()
    {
        return view('pages.eventsCreate');
    }

    /**
     * Shows the event for a given id.
     *
     * @return Response
     */
    public function showOneEvent($id)
    {
      $setMessage = [];
      $event = Event::find($id);
      $messages = $event->messages;
      foreach($messages as $message){
        $user=User::find($message->id_user);
        $setMessage[$message->id]=$user;
      }

      //pq q o authorize n funciona?
      $showModal = false;
      return view('pages.event', ['event' => $event, 'messages' => $messages, 'setMessage' => $setMessage, 'showModal' => $showModal]);
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
    
    public function showEventsAttend()
    {
      $user = Auth::user()->id;
      /*
      $myeventsid = Event_Organizer::where('id_user','=',$user)->get(['id_event']);
      $myevents = Event::where('id_event','=',$myeventsid)->get();*/
      $myevents = DB::table('event')
          ->join('attendee', 'event.id', '=', 'attendee.id_event')
          ->where('attendee.id_user', $user)
          ->get();
      //pq q o authorize n funciona?
      return view('pages.calendar', ['myevents' => $myevents]);
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
     * Get a validator for an incoming event request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'title' => 'required|string|max:255',
            'description' => 'string',
            'visibility' => 'required|boolean',
            'picture' => 'file',
            'local' => 'required|string',
            'publish_date' => 'required|date',
            'start_date' => 'required|date',
            'final_date' => 'required|date',
        ]);
    }

    /**
     * Creates a new event.
     *
     * @return Redirect The page of the event created.
     */
    public function createEvent(Request $request)
    {
      $current_date = Carbon::now();
      $start_date = $request->input('start_date');
      $final_date = $request->input('final_date');

      if (($start_date > $final_date)) {
        return redirect()->back(); //TODO  add hours:min to add condition ($start_date < $current_date) || ($final_date < $current_date)
      }

      $event = new Event();

      //$this->authorize('createEvent', $event);

      $event->title = $request->input('title');
      $event->description = $request->input('description');
      $event->visibility = $request->input('visibility');
      $event->picture = $request->input('picture');
      $event->local = $request->input('local');
      $event->publish_date = $current_date;
      $event->start_date = $start_date;
      $event->final_date = $final_date;
      $event->save();

      $event_organizer = new EventOrganizer();
      $event_organizer->id = Auth::user()->id;
      $event_organizer->idevent = $event->id;
      $event_organizer->save();

      $messages = [];
      $showModal = true;
      return redirect()->route('event',['event' => $event, 'messages' => $messages, 'showModal' => $showModal, 'id' => $event->id]);
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
      $messages = Message::where('idevent','=',$id)->get();
      $showModal = false;
      return view('pages.event', [
        'event' => $event,
        'messages'=> $messages,
        'showModal' => $showModal,
        'user' => User::find(Auth::user()->id)]);
    }
}
