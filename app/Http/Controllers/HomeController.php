<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class HomeController extends Controller
{
    /**
     * Shows the card for a given id.
     * 
     * @return Response
     */
    public function show()
    {
      return view('pages.home');
    }

}
