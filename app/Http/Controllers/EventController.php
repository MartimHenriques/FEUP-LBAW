<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Event;
use App\Models\EventOrganizer;

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
     * Shows all cards.
     *
     * @return Response
     */
    public function list()
    {
      $this->authorize('list', Card::class);
      $cards = Auth::user()->cards()->orderBy('id')->get();
      return view('pages.cards', ['cards' => $cards]);
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
}
