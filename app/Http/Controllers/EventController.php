<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Event;
use App\Models\Message;
use App\Models\Event_Organizer;
use App\Models\User;
use App\Models\Attendee;
use App\Models\Tag;
use App\Models\Report;


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
    public function showOneEventInfo($id)
    {
      $event = Event::find($id);

      $showModal = false;

      $attendee = Attendee::where('id_user', '=', Auth::id())->where('id_event','=',$id)->exists();

      $event_organizer=Event_Organizer::where('id_user', '=', Auth::id())->where('id_event','=',$event->id)->exists();

      return view('pages.eventInfo', ['event' => $event, 'showModal' => $showModal, 'attendee' => $attendee, 'event_organizer' => $event_organizer]);
    }
    public function showOneEventForum($id){
      $setMessage = [];
      $event = Event::find($id);
      $messages = $event->messages;
      foreach($event->messages as $message) {
        $user=User::find($message->id_user);
        $setMessage[$message->id]=$user;
      }

      $showModal = false;

      $attendee = Attendee::where('id_user', '=', Auth::id())->where('id_event','=',$id)->exists();

      $event_organizer=Event_Organizer::where('id_user', '=', Auth::id())->where('id_event','=',$event->id)->exists();

      return view('pages.eventForum', ['event' => $event, 'messages' => $messages, 'setMessage' => $setMessage, 'showModal' => $showModal, 'attendee' => $attendee, 'event_organizer' => $event_organizer]);
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
    
    public function showEventsAttend(){
      $user = Auth::user()->id;
      /*
      $myeventsid = Event_Organizer::where('id_user','=',$user)->get(['id_event']);
      $myevents = Event::where('id_event','=',$myeventsid)->get();*/
      
      $dt = Carbon::now();
      $eventstoattend = DB::table('event')
          ->join('attendee', 'event.id', '=', 'attendee.id_event')
          ->where('attendee.id_user', $user)->where('event.final_date','>=', $dt)
          ->orderby('event.start_date')
          ->get();
      $eventsattended = DB::table('event')
          ->join('attendee', 'event.id', '=', 'attendee.id_event')
          ->where('attendee.id_user', $user)->where('event.final_date','<', $dt)
          ->orderby('event.start_date')
          ->get();
      //pq q o authorize n funciona?
      return view('pages.calendar', ['eventstoattend' => $eventstoattend, 'eventsattended' => $eventsattended]);
    }


    public function showEvents(){ 
      $tags = Tag::all();
      if(Auth::check()){
        $events = DB::table('event')->orderBy('id')->paginate(6);
        $event_organizer = [];
        foreach ($events as $event) {
          $event_organizer[$event->id] = Event_Organizer::where('id_user', '=', Auth::id())->where('id_event','=',$event->id)->exists();
          $attendee[$event->id] = Attendee::where('id_user', '=', Auth::id())->where('id_event','=',$event->id)->exists();
        }
        return view('pages.feed',['events' => $events, 'event_organizer' => $event_organizer, 'attendee' => $attendee]);
      }
      else{
        $event_organizer = [];
        $events = Event::where('visibility', 1)->orderBy('id')->get();
        foreach ($events as $event) {
          $attendee[$event->id] = false;
        }
        return view('pages.feed',['events' => $events, 'event_organizer' => $event_organizer, 'attendee' => $attendee]);
      }

    }


    public static function searchEvents(Request $request){
      return DB::table('event')
      ->where('title', 'ILIKE', '%'.$request->search.'%')
      ->get();
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

      if (($start_date >= $final_date) || ($start_date < $current_date) || ($final_date < $current_date)) {
        return redirect()->back(); 
      }

      $event = new Event();

      //$this->authorize('createEvent', $event);

      $event->title = $request->input('title');
      $event->description = $request->input('description');
      $event->visibility = $request->input('visibility');
      $request->validate([
        'picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      ]);

      if($request->picture != null){
        $img = $request->picture; 

        $uuid = Str::uuid()->toString();
        $mytime = now()->toDateTimeString();

        $file = public_path('img_events/').$event->picture;

        if($img != null){
          if(file_exists($file) && $event->picture != null) {
            unlink($file);
          }
          $imageName =  $mytime. $uuid . '.' . $img->extension();
          $img -> move(public_path('img_events/'), $imageName);
          $event->picture = $imageName;
        }   
    } 

      $event->local = $request->input('local');
      $event->publish_date = $current_date;
      $event->start_date = $start_date;
      $event->final_date = $final_date;
      $event->save();

      $event_organizer = new Event_Organizer();
      $event_organizer->id_user = Auth::id();
      $event_organizer->id_event = $event->id;
      $event_organizer->save();

      $messages = [];
      $showModal = true;
      return redirect()->route('event',['event' => $event, 'messages' => $messages, 'showModal' => $showModal, 'id' => $event->id]);
    }

    /**
     * Shows the form to edit an event.
     *
     * @return Response
     */
    public function showEditEventForm($id)
    {
      $event = Event::find($id);
      return view('pages.eventsEdit', ['event'=>$event,'id'=>$id]);
    }

    /**
     * Edits event's info.
     *
     * @return Event The event changed.
     */
    public function editEvent(Request $request, $id)
    {
      $current_date = Carbon::now();
      $event = Event::find($id);
      $start_date = $request->input('start_date');
      $final_date = $request->input('final_date');

      if (($start_date >= $final_date) || ($start_date < $current_date) || ($final_date < $current_date)) {
        return redirect()->back(); 
      }

      //$this->authorize('createEvent', $event);

      $event->title = $request->input('title');
      $event->description = $request->input('description');
      $event->visibility = $request->input('visibility');
      $request->validate([
        'picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      ]);

      if($request->picture != null){
          $img = $request->picture; 

          $uuid = Str::uuid()->toString();
          $mytime = now()->toDateTimeString();

          $file = public_path('img_events/').$event->picture;

          if($img != null){
            if(file_exists($file) && $event->picture != null) {
              unlink($file);
            }
            $imageName =  $mytime. $uuid . '.' . $img->extension();
            $img -> move(public_path('img_events/'), $imageName);
            $event->picture = $imageName;
          }   
      }

      $event->local = $request->input('local');
      $event->start_date = $start_date;
      $event->final_date = $final_date;
      $event->save();

      $showModal = false; 
      return redirect()->route('event',['event' => $event, 'messages' => $event->messages, 'showModal' => $showModal, 'id' => $event->id]);
    }

    public function join(Request $request, Event $event)
    {
      if (!Auth::check()) return redirect('/login');
      $user = User::find(Auth::user()->id);
      $this->authorize('attendee', [$user, $event]);
      $event->invites()->attach($user->id);
      $messages = Message::where('idevent','=',$id)->get();
      $showModal = false;
      $event_organizer = false;
      return view('pages.event', [
        'event' => $event,
        'messages'=> $messages,
        'showModal' => $showModal,
        'user' => User::find(Auth::user()->id),
        'event_organizer' => $event_organizer]);
    }

    /**
     * The user joins a event.
     *
     * @return Redirect back to the page
     */
    public function joinEvent($id) {
      
      $attendee = new Attendee;

      $attendee->id_user = Auth::id();
      $attendee->id_event = $id;
      $attendee->save();

      return redirect()->back();
    }

    /**
     * The user abstains from a event.
     *
     * @return Redirect back to the page
     */
    public function abstainEvent($id) {

      $event_organizer_count = Event_Organizer::where(['id_event' => $id, 'id_user' => Auth::id()])->count();
      $attendee = Attendee::where(['id_user' => Auth::id(),'id_event' => $id]);

      if($event_organizer_count > 0){
        $count = Event_Organizer::where(['id_event' => $id])->count();
        $event_organizer = Event_Organizer::where(['id_event' => $id, 'id_user' => Auth::id()]);
        $event_organizer->delete();
        if($count == 1){
          Event::where('id', $id)->update(['is_canceled' => 1]);
        }
        else{
          $attendee->delete();
        }
      } 
      else{
        $attendee->delete();
      }
 
      return redirect()->back();
    }
         /**
     * An attendee is removed from an event.
     *
     * @return Redirect back to the page
     */
    public function removeFromEvent($id_attendee,$id_event) {

      $attendee = Attendee::where(['id_user' => $id_attendee,'id_event' => $id_event]);
      $attendee->delete();
      return redirect()->back();
    }

    public function cancelEvent($id){
      $event = Event::find($id);
      $user = Auth::user();

      $this->authorize('cancelEvent', [$user, $event]);
      $event->is_canceled = 1;

      $event->save();

      return redirect()->back();
    }
    public function reportEvent(Request $request, $id){
      $report = new Report();
      $report->id_reporter = Auth::id();
      $report->id_event = $id;
      $report->motive = $request->get('motive');
      $report->date = Carbon::now();
      $report->save();
      return redirect()->back();
    }
}
