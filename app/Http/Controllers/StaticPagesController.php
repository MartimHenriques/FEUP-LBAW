<?php

namespace App\Http\Controllers;

class StaticPagesController extends Controller
{
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