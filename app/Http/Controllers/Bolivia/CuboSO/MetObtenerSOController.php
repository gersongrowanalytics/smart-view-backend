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
    public function ObtenerSO(Request $request)
    {

        $re_anio = $request['anio'];
        $re_mes  = $request['mes'];
        $re_dia  = $request['dia'];

        // $spider = spider_view_1::limit(10);
        // $table = DB::select('DESCRIBE SmartViewSellsSO');
        $table = spider_view_1::where('fecha', $re_anio.$re_mes.$re_dia)->limit(50)->get();
        // dd($table);

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
