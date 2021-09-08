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

    public function save(Request $request)
    {
        if ($request->has('proxy')) {

            $raw_proxy = $request->input('proxy');
            $proxy = $this->regex($raw_proxy);
            $proxy_adress = $proxy[0][1];
            $proxy_port = $proxy[0][2];
            $proxy = new Proxy();
            $proxy->proxy_address = $proxy_adress;
            $proxy->proxy_port = $proxy_port;
            $proxy->save();
        }
        return redirect()->route('add_proxy');
    }

    public function regex($raw_proxy)
    {

        $re = '/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})[ \t:]+(\d{2,5})/m';
        $str = $raw_proxy;

        preg_match_all($re, $str, $proxy, PREG_SET_ORDER, 0);

        return $proxy;
    }

    public function destroy($id)
    {
        $site = Proxy::find($id);
        $site->delete();
        return redirect()->route('add_proxy');
    }
}
