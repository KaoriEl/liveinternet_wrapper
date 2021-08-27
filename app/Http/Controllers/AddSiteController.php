<?php

namespace App\Http\Controllers;

use App\Models\Sites;
use Illuminate\Http\Request;

class AddSiteController extends Controller
{
    public function index()
    {
        return view('dashboard.add_site');
    }

    public function save(Request $request){
        if ($request->has('url')) {
            $site = new Sites();
            $site->site_url= $request->input('url');
            $site->save();
        }
        return view('dashboard.add_site');
    }

    public function destroy($id)
    {
        $site = Sites::find($id);
        $site->delete();
        return redirect()->route('dashboard');
    }


}
