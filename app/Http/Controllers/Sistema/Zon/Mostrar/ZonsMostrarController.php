<?php

namespace App\Http\Controllers\Sistema\Zon\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\zonzonas;

class ZonsMostrarController extends Controller
{
    public function MostrarZons(Request $request)
    {
        $respuesta  = true;
        $mensaje    = "Se cargaron todos los tpus satisfactoriamente";
        $data       = [];

        $zons = zonzonas::get(['zonid', 'zonnombre']);

        if(sizeof($zons) > 0){
            $data = $zons;
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, no se encontraron Zons registrados";
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $data
        ]);
    }
}
