<?php

namespace App\Http\Controllers;

use App\Models\Sites;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Sites::all();

        return view('dashboard.dashboard', compact('sites',));
    }

}
