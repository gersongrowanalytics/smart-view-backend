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

        $datos = array(
            array(
                "zona" => "",
                "real" => "",
                "objetivo" => "",

            )
        );

        $zonas = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                    ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                    ->join('zonzonas as zon', 'zon.zonid', 'usu.zonid')
                                    ->where('usu.estid', 1)
                                    ->distinct('zon.zonid')
                                    ->get([
                                        'zon.zonid',
                                        'zon.zonnombre',
                                    ]);
        
        $contador = 0;

        foreach($zonas as $posicion => $zona){
            $sumValReal = tsutipospromocionessucursales::join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                ->where('suc.zonid', $zona->zonid)
                                                ->sum(['tsutipospromocionessucursales.tsuvalorizadoreal']);

            // $sumValObje = tsutipospromocionessucursales::join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
            //                                     ->where('suc.zonid', $zona->zonid)
            //                                     ->sum(['tsuvalorizadoobjetivo']);

                    
            $datos[$contador]['zona'] = $zona->zonnombre;
            $datos[$contador]['real'] = $sumValReal;
            $datos[$contador]['objetivo'] = "20";

            $contador = $contador+1;

        }

        return response()->json([
            "datos"     => $datos,
            "respuesta" => true,
        ]);
    }
}
