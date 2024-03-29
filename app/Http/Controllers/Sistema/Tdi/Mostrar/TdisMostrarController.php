<?php

namespace App\Http\Controllers\Sistema\Tdi\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tditiposdocumentosidentidades;

class TdisMostrarController extends Controller
{
    public function MostrarTdis()
    {
        $respuesta  = true;
        $mensaje    = "Se cargaron todos los tdis satisfactoriamente";
        $data       = [];

        $tdis = tditiposdocumentosidentidades::get(['tdiid', 'tdiabreviacion']);
        if(sizeof($tdis) > 0){
            $data = $tdis;
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, no se encontraron Tdis registrados";
            $data = [];
        }
        
        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $data
        ]);
    }
}
