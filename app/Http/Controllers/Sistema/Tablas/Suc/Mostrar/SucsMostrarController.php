<?php

namespace App\Http\Controllers\Sistema\Tablas\Suc\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\zonzonas;
use App\sucsucursales;

class SucsMostrarController extends Controller
{
    public function MostrarSucsXZona(Request $request)
    {

        $zons = zonzonas::get(['zonid', 'zonnombre']);

        foreach($zons as $posicion => $zon){
            $sucs = sucsucursales::where('zonid', $zon->zonid)
                                    ->get(['sucid', 'sucnombre']);

            $zons[$posicion]['value'] = $zon->zonid;
            $zons[$posicion]['label'] = $zon->zonnombre;

            foreach($sucs as $posicionSuc => $suc){
                $sucs[$posicionSuc]['value'] = $suc->sucid;
                $sucs[$posicionSuc]['label'] = $suc->sucnombre;
            }

            $zons[$posicion]['children'] = $sucs;

        }

        $requestsalida = response()->json([
            'respuesta' => true,
            'mensaje'   => "Sucursales cargadas por zona",
            'datos'     => $zons
        ]);
        
        return $requestsalida;

    }
}
