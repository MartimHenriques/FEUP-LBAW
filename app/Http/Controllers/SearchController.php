<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;

use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request) {

        $validated = $request->validate([
          'filter' => Rule::in(['votes', 'new', 'relevance']),
          'page' => 'integer',
        ]);
  
        $user = User::has('username')->get();
        $event = $this->filterEvents($request);
  
        session()->flashInput($request->input());
        return view('pages.search', ['users' => $user, 'events' => $event->simplePaginate(10)]);
      }
  
      /**
       * Get the rendered questions that match the search for Ajax calls
       */
      public function advancedSearch(Request $request){
  
        $validated = $request->validate([
          'filter' => Rule::in(['votes', 'new', 'relevance']),
          'page' => 'integer'
        ]);
  
  
        $questions = $this->filterEvents($request);
  
        $response = view('partials.search.search-events', ['events' => $event->simplePaginate(10)])->render();
        return response()->json(array('success' => true, 'html' => $response));
      }
  
      public function filterEvents(Request $request){
  
        $trimSearch = trim($request->input('search-input'));
        $pattern = "/[^0-9a-zA-ZÀ-ú\s]/";
        $stripSearch = preg_replace($pattern, "", $trimSearch);
       
  
        $hasTextSearch = $trimSearch != '';
        $courses = json_decode($request->input('courses'));
        $tags = json_decode($request->input('tags'));
  
        $rules = array('courses'=>'numericarray',
                      'tags'=>'numericarray');
  
        $questions = Question::with(['owner','courses', 'tags'])->where('deleted', '=', false);
  
        // Filter by course
        if($courses != null && count($courses) > 0){
          $questions = $questions->whereHas('courses', function ($query) use ($courses){
            $query->whereIn('id', $courses);
          });
        }
  
        // Filter by tag
        if($tags != null && count($tags) > 0){
          $questions = $questions->whereHas('tags', function ($query) use ($tags){
            $query->whereIn('id', $tags);
          });
        }
  
        // Filter by text search
        if($hasTextSearch){
          $search = str_replace(' ',' | ', $stripSearch);
          $questions = $questions->whereRaw("search||Coalesce(answers_search,'') @@ to_tsquery('simple',?)", [$search]);
        }
  
        // Order questions
        if($request->input('filter') == 'votes'){
          $questions = $questions->orderBy('score', 'desc');
        }
        else if($request->input('filter') == 'relevance' && $hasTextSearch){
          $search = str_replace(' ',' | ', $stripSearch);
          $questions = $questions->orderByRaw("ts_rank(search||Coalesce(answers_search,''),to_tsquery('simple',?)) DESC", [$search]);
        }
        else {
          $questions = $questions->orderBy('id', 'desc');
        }
  
        return $questions;
      }
  
}