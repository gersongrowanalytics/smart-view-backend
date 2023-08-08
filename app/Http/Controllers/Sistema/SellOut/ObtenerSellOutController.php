<?php

namespace App\Http\Controllers\Sistema\SellOut;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SmartViewSellsSO;
use App\SmartViewSellsSOxCategory;
use App\SmartViewSellsSOxMonth;
use App\PortafolioSales;

class ObtenerSellOutController extends Controller
{
    public function ObtenerSellOutConsolidado($anio, $mes)
    {
        $so = SmartViewSellsSOxMonth::where('YEAR', $anio)->where('MONTH', $mes)->get(['YEAR', 'MONTH', 'COD_SOLD_TO', 'CATEGORY', 'COD_MATERIAL', 'SELLS', 'NIV']);
        // $so = SmartViewSellsSO::where('YEAR', $anio)->where('MONTH', $mes)->count();
        // $so = SmartViewSellsSOxCategory::where('YEAR', $anio)
                                        //  ->where('MONTH', $mes)
                                        //  ->where('COD_SOLD_TO', '40093187')
                                        //  ->sum('NIV');
                                        // ->sum('');

        return $so;
    }




    public function ObtenerSOXCategoria($anio, $mes)
    {
        $so = SmartViewSellsSOxCategory::where('YEAR', $anio)
                                        ->where('MONTH', $mes)
                                        ->get();

        return $so;
    }

    public function ObtenerSOxPO($anio, $mes)
    {
        $cantidad_so = PortafolioSales::where('PERIOD', '=', '202212')
                                    // ->where('PERIOD', '<=', '202305')
                                    ->where('SALES', '>', 0)
                                    ->distinct('PK_CLIENT_SO')
                                    ->orderBy('PK_CLIENT_SO')
                                    ->count();


        $count_CLIENT_HML = PortafolioSales::where('PERIOD', '=', '202212')
                                // ->where('PERIOD', '<=', '202305')
                                ->where('SALES', '>', 0)
                                ->distinct('CLIENT_HML')
                                ->orderBy('CLIENT_HML')
                                // ->limit(100)
                                ->count(['CLIENT_HML']);

        $CLIENT_HML = PortafolioSales::where('PERIOD', '=', '202212')
                                // ->where('PERIOD', '<=', '202305')
                                ->where('SALES', '>', 0)
                                ->distinct('CLIENT_HML')
                                ->orderBy('CLIENT_HML')
                                // ->limit(100)
                                ->get(['CLIENT_HML']);

        // $so = PortafolioSales::where('PERIOD', '=', '202212')
        //                         // ->where('PERIOD', '<=', '202305')
        //                         ->where('SALES', '>', 0)
        //                         ->distinct('PK_CLIENT_SO')
        //                         ->orderBy('PK_CLIENT_SO')
        //                         // ->limit(100)
        //                         ->get();

        return array(
            "cantidad" => $cantidad_so,
            // "so" => $so,
            "count_CLIENT_HML" => $count_CLIENT_HML,
            "CLIENT_HML" => $CLIENT_HML,
        );
    }


    // SELECT
    //     'S1-2023' AS 'PERIOD',
    //     D_CSI.[CLIENT_HML],
    //     T.PK_CLIENT_SO,
    //     SUM(T.S_SELL_TOTAL) AS 'SALES'
    // FROM
    //     F_SELLS_SO T
    // INNER JOIN
    //     D_DATES D_DT
    //     ON
    //     D_DT.PK_DATE = T.PK_DATE
    // INNER JOIN
    //     D_CLIENTS_SI D_CSI
    //     ON
    //     D_CSI.PK_CLIENT_SI = T.PK_CLIENT_SI
    // WHERE
    //     T.PK_DATE >= 20221201 AND T.PK_DATE <=20230531
    // GROUP BY
    // D_CSI.[CLIENT_HML],
    // T.PK_CLIENT_SO

    // Periodo (202212; 202201)
    public function ObtenerSOxPODinamico(Request $request)
    {
        $re_periodo = $request['periodo'];
        $re_clienthml = $request['clienthml'];
        $period = 'S1-2023';
        
        $countso = PortafolioSales::selectRaw('COD_SHIP_TO, CLIENT_HML, PK_CLIENT_SO, SUM(SALES) as sales')
                                    ->groupby(['COD_SHIP_TO', 'CLIENT_HML', 'PK_CLIENT_SO'])
                                    ->where('PERIOD', '>=','202212')
                                    ->where('PERIOD', '<=','202305')
                                    ->count();
        

        $results = PortafolioSales::selectRaw('COD_SHIP_TO, CLIENT_HML, PK_CLIENT_SO, SUM(SALES) as sales')
                            ->groupby(['COD_SHIP_TO', 'CLIENT_HML', 'PK_CLIENT_SO'])
                            ->where('PERIOD', '>=','202212')
                            ->where('PERIOD', '<=','202305')
                            ->limit(10)
                            ->get();

        $resultsv2 = PortafolioSales::limit(1)
                                    ->get();

        return array(
            "resultsv2" => $resultsv2,
            "count" => $countso,
            "so" => $results,
        );

    }

}
