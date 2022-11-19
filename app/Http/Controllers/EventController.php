<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;
use App\Models\Message;

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
}
