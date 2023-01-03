<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\ContactUs;
use Mail;

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

    public function sendEmail(Request $request) {

      $mailData = [
          'name' => $request->name,
          'email' => $request->email,
          'message' => $request->message
      ];

      Mail::to('wemeet33@gmail.com')->send(new ContactUs($request));
         
      return redirect()->route('contact_us');
  }


}
