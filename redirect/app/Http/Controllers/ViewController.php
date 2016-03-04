<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ViewController extends Controller
{
    public function completed ($respid) {
        return view('completed', compact('respid'));
    }

    public function terminated ($respid) {
        return view('terminated', compact('respid'));
    }

    public function quotafull ($respid) {
        return view('quotafull', compact('respid'));
    }
}
