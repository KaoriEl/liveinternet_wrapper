<?php

namespace App\Http\Controllers;

use App\Models\Sites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteControllerAjax extends Controller
{
    public function index($id){
        return DB::table("sites")->find($id);
    }


}
