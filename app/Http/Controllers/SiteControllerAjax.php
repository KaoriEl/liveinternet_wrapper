<?php

namespace App\Http\Controllers;

use App\Models\Sites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteControllerAjax extends Controller
{
    /**
     * Ну аяксик, все понятно.
     * @param $id
     * @return \Illuminate\Database\Query\Builder|mixed
     */
    public function index($id){
        return DB::table("sites")->find($id);
    }
}
