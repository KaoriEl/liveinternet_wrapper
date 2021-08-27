<?php

namespace App\Http\Controllers;

use App\Models\Proxy;
use Illuminate\Http\Request;

class ProxyController extends Controller
{
    public function index()
    {
        $proxies = Proxy::all();
        return view('dashboard.proxy.proxy', compact('proxies',));
    }

    public function save(Request $request){
        if ($request->has('proxy')) {
            $proxy = new Proxy();
            $proxy->proxy= $request->input('proxy');
            $proxy->save();
        }
        return redirect()->route('add_proxy');
    }

    public function destroy($id)
    {
        $site = Proxy::find($id);
        $site->delete();
        return redirect()->route('add_proxy');
    }
}
