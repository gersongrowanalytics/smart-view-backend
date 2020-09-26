<?php

namespace App\Http\Controllers\Sistema\Configuracion\Rebate\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tretiposrebates;

class GrupoRebateMostrarController extends Controller
{
    public function GrupoRebateMostrar(Request $request)
    {

        $usutoken = $request->header('api_token');

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $tre = tretiposrebates::get(['treid', 'trenombre']);

        if(sizeof($tre) > 0){
            $respuesta  = true;
            $datos      = $tre;
            $linea      = __LINE__;

        }else{
            $datos      = [];
            $respuesta  = false;
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);
        
        return $requestsalida;
    }
}
