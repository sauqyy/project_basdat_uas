<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page
     */
    public function index()
    {
        return view('landing.index');
    }

    /**
     * Display the learn more page
     */
    public function learnMore()
    {
        return view('landing.learn-more');
    }
}