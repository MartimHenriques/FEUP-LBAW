<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Event;
use App\Models\EventOrganizer;
use App\Models\Message;

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
    public function showOneEvent()
    {
      $event = Event::find($id);
      $messages = Message::where('idevent','=',$id)->get();
      //pq q o authorize n funciona?
      return view('pages.event', ['event' => $event, 'messages' => $messages]);
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
     * Creates a new event.
     *
     * @return Redirect The page of the event created.
     */
    public function createEvent(Request $request)
    {
      $event = new Event();

      //$this->authorize('createEvent', $event);

      $event->title = $request->input('title');
      $event->description = $request->input('description');
      $event->visibility = $request->input('visibility');
      $event->picture = $request->input('picture');
      $event->local = $request->input('local');
      $event->publish_date = Carbon::now();
      $event->start_date = $request->input('start_date');
      $event->final_date = $request->input('final_date');
      $event->save();

      $event_organizer = new EventOrganizer();
      $event_organizer->id = Auth::user()->id;
      $event_organizer->idevent = $event->id;
      $event_organizer->save();

      return redirect('/events/'.$event->id);
    }

    public function delete(Request $request, $id)
    {
      $card = Card::find($id);

      $this->authorize('delete', $card);
      $card->delete();

      return $card;
    }

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
