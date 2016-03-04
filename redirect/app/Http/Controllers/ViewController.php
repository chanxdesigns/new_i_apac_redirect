<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ViewController extends Controller
{
    //Return the View for Completed
    public function completed ($respid) {
        return view('completed', compact('respid'));
    }

    //Return the View for Terminated
    public function terminated ($respid) {
        return view('terminated', compact('respid'));
    }

    //Return the View for Quotafull
    public function quotafull ($respid) {
        return view('quotafull', compact('respid'));
    }
}
