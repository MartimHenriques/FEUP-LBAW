<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;

class EventController extends Controller
{
    /**
     * Shows the card for a given id.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
      $card = Card::find($id);
      $this->authorize('show', $card);
      return view('pages.card', ['card' => $card]);
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
