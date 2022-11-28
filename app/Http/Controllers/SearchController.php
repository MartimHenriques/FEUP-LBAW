<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Event;
use App\Models\User;

class SearchController extends Controller
{
    
    public function show(Request $request)
    {
      

        $search = $request->input('search');
        $events;
         if(isset($search)){
            
            $events = Event::search($search)->get();
            //Event::whereRaw('tsvectors @@ plainto_tsquery(\'english\', ?)', [$search])->limit(5)->get();
        } 
        return $events;
        return view('pages.resultsevents', $data);
    }
}