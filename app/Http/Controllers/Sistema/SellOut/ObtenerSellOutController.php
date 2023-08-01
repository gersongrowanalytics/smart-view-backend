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

        $so = PortafolioSales::where('PERIOD', '=', '202212')
                                // ->where('PERIOD', '<=', '202305')
                                ->where('SALES', '>', 0)
                                ->distinct('PK_CLIENT_SO')
                                ->orderBy('PK_CLIENT_SO')
                                // ->limit(100)
                                ->get();

        return array(
            "cantidad" => $cantidad_so,
            "so" => $so
        );
    }
}
