<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Message;
use App\Models\Vote;
use App\Models\User;

class MessageController extends Controller
{

    public function createComment(Request $request){
        $msg = new Message();
        $msg->content =  $request->get('content');
        $msg->date =  now();
        $msg->id_user =  Auth::id();
        $msg->id_event =  $request->get('id');
        $msg->save();
        
        $user=Auth::user();
        $setMessage[$msg->id]=$user;

        return json_encode(view('partials.message', ['message' => $msg, 'setMessage' => $setMessage])->render());
    }
    public function createReply(Request $request){
        $msg = new Message();
        $msg->content =  $request->get('content');
        $msg->date =  now();
        $msg->id_user =  Auth::id();
        $msg->id_event =  $request->get('id');
        $msg->parent =  $request->get('id_parent');
        $msg->save();
        
        $user=Auth::user();
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