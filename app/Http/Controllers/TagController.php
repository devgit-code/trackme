<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class TagController extends Controller
{
    public function view($uid): View
    {
        return view('view-tag', []);
    }

    public function create(): View
    {
        return view('create-tag', []);
    }
}
