<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Message;
use App\Models\Vote;

class MessageController extends Controller
{

    public function vote(Request $request){
        DB::table('vote')->insert(
            array(
                'id_user' => Auth::user()->id,
                'id_message' => $request->get('id'),
            )
        );
        return json_encode($request->get('id'));
    }

    /**
     * The vote is deleted.
     *
     * @return Redirect back to the page
     */
    public function deleteVote(Request $request){
        $vote = Vote::where('id_user', '=', Auth::id())->where('id_message','=',$request->get('id'));
        $vote->delete();
        return redirect()->back();
      }
}