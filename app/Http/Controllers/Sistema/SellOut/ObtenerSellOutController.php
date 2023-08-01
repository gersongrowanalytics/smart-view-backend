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

    // Periodo (202212; 202201)
    public function ObtenerSOxPODinamico($period, $clienthml)
    {

        $count_sos = PortafolioSales::where('PERIOD', '=', $period)
                                ->where('SALES', '!=', 0)
                                ->where('CLIENT_HML', "PUNTO BLANCO")
                                // ->distinct('PK_CLIENT_SO')
                                ->orderBy('PK_CLIENT_SO')
                                ->groupBy('PK_CLIENT_SO')
                                ->count([
                                    'CLIENT_HML',
                                    'PK_CLIENT_SO',
                                    'SALES'
                                ]);

        $sos = PortafolioSales::where('PERIOD', '=', $period)
                                ->where('SALES', '!=', 0)
                                ->where('CLIENT_HML', "PUNTO BLANCO")
                                // ->distinct('PK_CLIENT_SO')
                                ->orderBy('PK_CLIENT_SO')
                                ->groupBy('PK_CLIENT_SO')
                                ->limit(10)
                                ->get([
                                    'CLIENT_HML',
                                    'PK_CLIENT_SO',
                                    'SALES'
                                ]);

        return array(
            "count" => $count_sos,
            "so" => $sos,
        );

    }

}
