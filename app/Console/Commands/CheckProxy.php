<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use GuzzleHttp\RequestOptions;

class CheckProxy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxy:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'запускается по крону и проверяет прокси на работоспособность';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Чекает прокси на активность
     *
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $proxies = DB::table("proxies")->get();

        $arr = array();
        foreach ($proxies as $proxy){

            array_push($arr, $proxy->proxy_address . ":" . $proxy->proxy_port);



//            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//            $connection =  @socket_connect($socket, $proxy->proxy_address, $proxy->proxy_port);
//
//            if( $connection ){
//                DB::table("proxies")->where("proxy_address", $proxy->proxy_address)->update(['status'=>"ACTIVE",]);
//                echo 'ONLINE';
//            }
//            else {
//                DB::table("proxies")->where("proxy_address", $proxy->proxy_address)->update(['status'=>"INACTIVE",]);
//                echo 'OFFLINE: ' . socket_strerror(socket_last_error( $socket ));
//            }
        }
        $string = implode(PHP_EOL, $arr);

        $http = new Client;
        $response = $http->post('https://proxy-checker.net/api/proxy-checker/', [
            'form_params' => [
                'proxy_list' => $string,
            ],
        ]);

        $proxies = json_decode($response->getBody()->getContents(), true);

        foreach ($proxies as $proxy){
            if($proxy["valid"] == "true"){
                DB::table("proxies")->where("proxy_address", $proxy["ip"])->update(['status'=>"ACTIVE",]);
                echo 'ONLINE: ' . $proxy["ip"] . ":" . $proxy["port"] . PHP_EOL;
            }
            else {
                DB::table("proxies")->where("proxy_address", $proxy["ip"])->update(['status'=>"INACTIVE",]);
                echo 'OFFLINE: ' . $proxy["ip"] . ":" . $proxy["port"] . PHP_EOL;
            }
        }

    }
}
