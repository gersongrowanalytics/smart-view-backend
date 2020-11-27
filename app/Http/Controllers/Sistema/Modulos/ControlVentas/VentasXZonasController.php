<?php

namespace App\Http\Controllers\Sistema\Modulos\ControlVentas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

        foreach($zonas as $zona){
            $tsuc = tsutipospromocionessucursales::join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                ->join('ussusuariossucursales as uss', 'uss.sucid', 'suc.sucid')
                                                ->join('usuusuarios as usu', 'usu.usuid', 'uss.usuid')
                                                ->where('usu.zonid', $zona->zonid)
                                                ->sum(['tsuvalorizadoreal']);

        }



        



    }
}
