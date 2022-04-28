<?php

namespace App\Http\Controllers\Sistema\Tca\Mostrar;

use App\Http\Controllers\Controller;
use App\tcatiposcargasarchivos;
use Illuminate\Http\Request;

class TcasMostrarController extends Controller
{
    public function MostrarTcas()
    {
        $respuesta  = true;
        $mensaje    = "Se cargaron todos los tcas satisfactoriamente";
        $data       = [];

        $tcas = tcatiposcargasarchivos::get(['tcaid', 'tcanombre']);

        if(sizeof($tcas) > 0){
            $data = $tcas;
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, no se encontraron Tcas registrados";
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $data
        ]);
    }
}
