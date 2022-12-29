<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AboutUSController extends Controller
{
    /**
     * Shows the card for a given id.
     * 
     * @return Response
     */
    public function showAbout()
    {
      return view('pages.aboutUS');
    }

}
