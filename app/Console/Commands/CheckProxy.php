<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
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
     * @throws GuzzleException
     */
    public function handle()
    {
        $proxies = DB::table("proxies")->get();
        $quantity = count($proxies);


        $arr = array();
        foreach ($proxies as $proxy) {

            array_push($arr, $proxy->proxy_address . ":" . $proxy->proxy_port);

        }

        $count_chunk = 20;
        $size = ceil(count($arr) / $count_chunk);
        $arr = array_chunk($arr, $size);


        for ($i = 0; $i < $count_chunk; $i++) {
            $string = implode(PHP_EOL, $arr[$i]);

            $http = new Client;
            $response = $http->post('https://proxy-checker.net/api/proxy-checker/', [
                'form_params' => [
                    'proxy_list' => $string,
                ],
            ]);

            $proxies = json_decode($response->getBody()->getContents(), true);
            sleep(1);
            foreach ($proxies as $proxy) {
                if ($proxy["valid"] == "true") {
                    DB::table("proxies")->where("proxy_address", $proxy["ip"])->update(['status' => "ACTIVE",]);
                    echo '[Chunk_number = ' . $i . '] ONLINE: ' . $proxy["ip"] . ":" . $proxy["port"] . PHP_EOL;
                } else {
                    DB::table("proxies")->where("proxy_address", $proxy["ip"])->update(['status' => "INACTIVE",]);
                    echo '[Chunk_number = ' . $i . '] OFFLINE: ' . $proxy["ip"] . ":" . $proxy["port"] . PHP_EOL;
                }
            }
        }


    }
}
