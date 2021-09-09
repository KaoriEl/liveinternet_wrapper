<?php

namespace App\Http\Controllers;

use App\Models\Sites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ApiController extends Controller
{

    /**
     * Принимает из го статусы, и обновляет их в бд
     * @param Request $request
     */
    public function status(Request $request)
    {
        if ($request->has('url') && $request->has('status')) {
            switch ($request->input('status')) {
                case "START":
                    DB::table("sites")->where("site_url", $request->input('url'))->update(['status'=>"START",]);
                    break;
                case "STOP":
                    DB::table("sites")->where("site_url", $request->input('url'))->update(['status'=>"STOP",]);
                    break;
                case "ERR":
                    DB::table("sites")->where("site_url", $request->input('url'))->update(['status'=>"ERR",]);
                    break;
            }
        }

    }
}
