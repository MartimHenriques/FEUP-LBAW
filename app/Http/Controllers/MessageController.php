<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Message;
use App\Models\Vote;

class MessageController extends Controller
{

    public function create(Request $request, $id){
        $msg = new Message();
        $msg->content = $request->input('content');
        $msg->date = now();
        $msg->id_event = $id;
        $msg->id_user = Auth::id();
        $msg->save();

        return redirect()->back();
    }
    public function createReply(Request $request){
        $msg = new Message();
        $msg->content =  $request->get('content');
        $msg->date =  now();
        $msg->id_user =  Auth::id();
        $msg->id_event =  $request->get('id_event');
        $msg->parent =  $request->get('id_parent');
        $msg->save();
        
        $user=User::find(Auth::id());
        $setMessage[$msg->id]=$user;
        return json_encode(view('partials.message', ['message' => $msg, 'setMessage' => $setMessage])->render());
    }


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