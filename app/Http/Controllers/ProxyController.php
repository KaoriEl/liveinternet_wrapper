<?php

namespace App\Http\Controllers;

use App\Models\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class ProxyController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $proxies = DB::table("proxies")->orderBy("status", "ASC")->paginate(20);
        return view('dashboard.proxy.proxy', compact('proxies'));
    }

    /**
     * Сохроаниение в бд
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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


    /**
     * Сохроаниение и распарсинг листа прокси
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveList(Request $request)
    {
        //Сохранение файла
        if ($request->isMethod('post')) {
            if ($request->hasFile("proxy_list")) {
                $file = $request->file('proxy_list');
                $file->move(public_path() . '/proxyList', 'proxylist.txt');
            }
        }

        //Парсинг файла
        $filename = public_path() . '/proxyList/proxylist.txt';

        foreach (file($filename) as $line) {
            $proxy = $this->regex($line);
            $proxy_adress = $proxy[0][1];
            $proxy_port = $proxy[0][2];
            $proxy = new Proxy();
            $proxy->proxy_address = $proxy_adress;
            $proxy->proxy_port = $proxy_port;
            $proxy->save();
        }


        return redirect()->route('add_proxy');
    }


    /**
     * Обрезаю прокси для разбивки на порт и айпишник
     * @param $raw_proxy
     * @return mixed
     */
    public function regex($raw_proxy)
    {

        $re = '/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})[ \t:]+(\d{2,5})/m';
        $str = $raw_proxy;

        preg_match_all($re, $str, $proxy, PREG_SET_ORDER, 0);

        return $proxy;
    }

    /**
     * Удаление из бд прокси
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $site = Proxy::find($id);
        $site->delete();
        return redirect()->route('add_proxy');
    }
}
