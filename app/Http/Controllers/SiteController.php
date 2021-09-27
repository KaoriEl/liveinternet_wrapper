<?php

namespace App\Http\Controllers;

use App\Console\Commands\CheckProxy;
use App\Jobs\WrappingJob;
use App\Models\Proxy;
use App\Models\Sites;
use Bschmitt\Amqp\Amqp;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redirect;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class SiteController extends Controller
{
    /**
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $sites = DB::table("sites")->orderBy("status", "ASC")->paginate(20);
        $proxy_count = DB::table("proxies")->where("status","ACTIVE")->count();
        return view('dashboard.dashboard', compact('sites','proxy_count'));
    }

    /**
     * Крч, тут идет разделение на кучу мелких задач, так как если надо крутить больше 10 то начинается сильная загрузка сервера
     * @param Request $request
     * @return Application|RedirectResponse|Redirector|void
     * @throws Exception
     */
    public function wrapper(Request $request)
    {

        //Это лютейший костыль
        $siteUrl = $request->input('SiteUrl');
        if ($request->input('count_wrapp') > 10 && $request->input('count_wrapp') < 300){
            $count = intdiv($request->input('count_wrapp'),  10);
            $delimiter = 10;
            $this->SmartWrapper($count,$siteUrl,$delimiter);
        }elseif ($request->input('count_wrapp') > 300 && $request->input('count_wrapp') < 1000){
            $count = intdiv($request->input('count_wrapp'),  50);
            $delimiter = 50;
            $this->SmartWrapper($count,$siteUrl,$delimiter);
        }elseif ($request->input('count_wrapp') >= 1000 && $request->input('count_wrapp') <= 2000){
            $count = intdiv($request->input('count_wrapp'),  100);
            $delimiter = 100;
            $this->SmartWrapper($count,$siteUrl,$delimiter);
        }elseif ($request->input('count_wrapp') > 2000 && $request->input('count_wrapp') <= 4000){
            $count = intdiv($request->input('count_wrapp'),  200);
            $delimiter = 200;
            $this->SmartWrapper($count,$siteUrl,$delimiter);
        }elseif ($request->input('count_wrapp') > 4000 && $request->input('count_wrapp') <= 10000){
            $count = intdiv($request->input('count_wrapp'),  500);
            $delimiter = 500;
            $this->SmartWrapper($count,$siteUrl,$delimiter);
        }elseif ($request->input('count_wrapp') < 10){
            $connection = new AMQPStreamConnection(env("RABBITMQ_HOST"), env("RABBITMQ_PORT"), env("RABBITMQ_LOGIN"), env("RABBITMQ_PASSWORD"));
            $channel = $connection->channel();

            if ($request->input('count_wrapp') !== null){
                $proxyList = DB::table("proxies")->inRandomOrder()->where("status", "ACTIVE")->take($request->input('count_wrapp'))->get();
            }else{
                dd("Нет кол-ва для накрутки");
            }
            $msg = [
                'site_url' => $siteUrl,
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
        return redirect('/');
    }

    /**
     * Складываю в очереди рэббита разбитые подзадачи
     * @param $count
     * @param $siteUrl
     * @return Application|RedirectResponse|Redirector
     * @throws Exception
     */
    public function SmartWrapper($count,$siteUrl,$delimiter) {

        $connection = new AMQPStreamConnection(env("RABBITMQ_HOST"), env("RABBITMQ_PORT"), env("RABBITMQ_LOGIN"), env("RABBITMQ_PASSWORD"));
        $channel = $connection->channel();

        for ($i = 0; $i <= $delimiter; $i++){

            if ($count !== null){
                $proxyList = DB::table("proxies")->inRandomOrder()->where("status", "ACTIVE")->take($count)->get();
//               dd($proxyList);
//                $proxyList = $this->CheckProxy($proxyList);
            }else{
//                dd("Нет кол-ва для накрутки");
            }
            $msg = [
                'site_url' => $siteUrl,
                'count_wrapp' => "$count",
                'proxyList' => json_encode($proxyList),
            ];
            $msg = json_encode($msg);
            $msg = new AMQPMessage($msg);
            $channel->basic_publish($msg, '', 'Queue_wrapper');
        }
        $channel->close();
        $connection->close();
        return redirect('/');
    }

//    public function CheckProxy($proxies){
//        $arr = array();
//        foreach ($proxies as $proxy) {
//            array_push($arr, $proxy->proxy_address . ":" . $proxy->proxy_port);
//        }
//
//
//        $count_chunk = 1;
//        $size = ceil(count($arr) / $count_chunk);
//        $arr = array_chunk($arr, $size);
//        $proxyList = array();
//
//        for ($i = 0; $i < $count_chunk; $i++) {
//            $string = implode(PHP_EOL, $arr[$i]);
//            try {
//                $http = new Client;
//                $response = $http->post('https://proxy-checker.net/api/proxy-checker/', [
//                    'form_params' => [
//                        'proxy_list' => $string,
//                    ],
//                ]);
//
//                $proxies = json_decode($response->getBody()->getContents(), true);
//
//                sleep(1);
//                foreach ($proxies as $proxy) {
//                    if ($proxy["valid"] == "true") {
//                        array_push($proxyList, $proxy->proxy_address . ":" . $proxy->proxy_port);
//                        $proxyList[]["proxy_address"][]= $proxy["ip"] "proxy_port"=> $proxy["port"]];
//                        $proxyList[]["proxy_address"][] =
//                        dd($proxyList);
//                        DB::table("proxies")->where("proxy_address", $proxy["ip"])->update(['status' => "ACTIVE",]);
//                        echo '[Chunk_number = ' . $i . '] ONLINE: ' . $proxy["ip"] . ":" . $proxy["port"] . PHP_EOL;
//                    } else {
//
//                        DB::table("proxies")->where("proxy_address", $proxy["ip"])->update(['status' => "INACTIVE",]);
//                        dump("zalupa");
//                        echo '[Chunk_number = ' . $i . '] OFFLINE: ' . $proxy["ip"] . ":" . $proxy["port"] . PHP_EOL;
//                    }
//                }
//            }catch (\Exception $exception){
//                return "ok";
//            }
//
//        }
//    }
}
