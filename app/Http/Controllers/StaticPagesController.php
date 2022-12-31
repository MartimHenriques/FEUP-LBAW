<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StaticPagesController extends Controller
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

    public function showUserHelp()
    {
      return view('pages.userHelp');
    }

        /**
     * Shows the contact us static page.
     *
     * @return Response
     */
    public function showContactUs()
    {
        return view('pages.contactUs');
    }

}
