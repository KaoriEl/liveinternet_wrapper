<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $proxies = DB::table("proxies")->where("status", "Not verified")->orWhere("status", "INACTIVE")->get();

        echo $proxies;

        foreach ($proxies as $proxy){

            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            $connection =  @socket_connect($socket, $proxy->proxy_address, $proxy->proxy_port);

            if( $connection ){
                DB::table("proxies")->where("proxy_address", $proxy->proxy_address)->update(['status'=>"ACTIVE",]);
                echo 'ONLINE';
            }
            else {
                DB::table("proxies")->where("proxy_address", $proxy->proxy_address)->update(['status'=>"INACTIVE",]);
                echo 'OFFLINE: ' . socket_strerror(socket_last_error( $socket ));
            }
        }
    }
}
