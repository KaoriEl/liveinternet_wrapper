<?php

namespace App\Http\Controllers;

use App\Jobs\WrappingJob;
use App\Models\Proxy;
use App\Models\Sites;
use Bschmitt\Amqp\Amqp;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redirect;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class SiteController extends Controller
{
    public function index()
    {
        $sites = DB::table("sites")->orderBy("status", "ASC")->paginate(20);
        $proxy_count = DB::table("proxies")->where("status","ACTIVE")->count();
        return view('dashboard.dashboard', compact('sites','proxy_count'));
    }

    /**
     * @throws Exception
     */
    public function wrapper(Request $request)
    {
        $connection = new AMQPStreamConnection(env("RABBITMQ_HOST"), env("RABBITMQ_PORT"), env("RABBITMQ_LOGIN"), env("RABBITMQ_PASSWORD"));
        $channel = $connection->channel();

        if ($request->input('count_wrapp') !== null){
            $proxyList = DB::table("proxies")->inRandomOrder()->where("status", "ACTIVE")->take($request->input('count_wrapp'))->get();
        }else{
            dd("Нет кол-ва для накрутки");
        }

        $msg = [
            'site_url' => $request->input('SiteUrl'),
            'count_wrapp' => $request->input('count_wrapp'),
            'proxyList' => json_encode($proxyList),
        ];

        $msg = json_encode($msg);
        $msg = new AMQPMessage($msg);
        $channel->basic_publish($msg, '', 'Queue_wrapper');
        $channel->close();
        $connection->close();
        return redirect('/');
    }



}
