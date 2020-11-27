<?php

namespace App\Http\Controllers\Sistema\Modulos\ControlVentas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tsutipospromocionessucursales;
use App\ussusuariossucursales;

class VentasXZonasController extends Controller
{
    public function VentasXZonas(Request $request)
    {

        $datos = array();

        $zonas = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                    ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                    ->join('zonzonas as zon', 'zon.zonid', 'usu.zonid')
                                    ->where('usu.estid', 1)
                                    ->distinct('zon.zonid')
                                    ->get([
                                        'zon.zonid',
                                        'zon.zonnombre',
                                    ]);

        foreach($zonas as $posicion => $zona){
            $sumValReal = tsutipospromocionessucursales::join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                ->where('suc.zonid', $zona->zonid)
                                                ->sum(['tsuvalorizadoreal']);

            $sumValObje = tsutipospromocionessucursales::join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                ->where('suc.zonid', $zona->zonid)
                                                ->sum(['tsuvalorizadoobjetivo']);

            $datos[] = array(
                "zona" => $zona->zonnombre,
                "real" => $sumValReal,
                "objetivo" => $sumValObje,

            );
        }

        return response()->json([
            "datos"     => $datos
        ]);
    }
}
