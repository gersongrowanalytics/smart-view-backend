<?php

namespace App\Http\Controllers\Sistema\SellOut;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SmartViewSellsSO;
use App\SmartViewSellsSOxCategory;
use App\SmartViewSellsSOxMonth;

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
}
