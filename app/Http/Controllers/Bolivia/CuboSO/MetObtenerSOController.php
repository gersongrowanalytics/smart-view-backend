<?php

namespace App\Http\Controllers\Bolivia\CuboSO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\spider_view_1;
use Illuminate\Support\Facades\DB;
use App\SmartViewSellsSO;
use App\SmartViewSellsSOxCategory;
use App\SmartViewSellsSOxMonth;

class MetObtenerSOController extends Controller
{
    public function ObtenerSO($anio, $mes, $dia, $limit)
    {

        $re_anio  = $anio;
        $re_mes   = $mes;
        $re_dia   = $dia;
        $re_limit = $limit;

        if($re_limit == "NA"){
            $table = spider_view_1::where('fecha', $re_anio.$re_mes.$re_dia)->get();
        }else{
            $table = spider_view_1::where('fecha', $re_anio.$re_mes.$re_dia)->limit($re_limit)->get();
        }

        $requestsalida = response()->json([
            "data" => $table,
            "tamaño" => sizeof($table)
        ]);

        return $requestsalida;

        // return $spider;

        // $tables = DB::select('SHOW TABLES');
        // foreach($tables as $table)
        // {
        //     echo $table->Tables_in_db_name;
        // }


        // $so = SmartViewSellsSOxCategory::where('YEAR', $anio)
        //                                 ->where('MONTH', $mes)
        //                                 ->get();

        // return $so;

        // $so = SmartViewSellsSO::where('YEAR', 2022)->count();

        // return $so;


    }
}
