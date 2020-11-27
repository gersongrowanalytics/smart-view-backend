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
            $tsusReal = tsutipospromocionessucursales::join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                ->where('suc.zonid', $zona->zonid)
                                                ->get(['tsutipospromocionessucursales.tsuvalorizadoreal']);

            $real = 0;
            foreach($tsusReal as $tsuReal){
                $real = $real + $tsuReal->tsuvalorizadoreal;
            }

            $tsusObje = tsutipospromocionessucursales::join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                ->where('suc.zonid', $zona->zonid)
                                                ->get(['tsuvalorizadoobjetivo']);

            $obj = 0;
            foreach($tsusObje as $tsuObje){
                $obj = $obj + $tsuObje->tsuvalorizadoobjetivo;
            }

                    
            $datos[$contador]['zona'] = $zona->zonnombre;
            $datos[$contador]['real'] = $real;
            $datos[$contador]['objetivo'] = $obj;

            $contador = $contador+1;

        }

        return response()->json([
            "datos"     => $datos,
            "respuesta" => true,
        ]);
    }
}
