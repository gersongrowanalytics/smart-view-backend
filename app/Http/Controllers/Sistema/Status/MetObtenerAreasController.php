<?php

namespace App\Http\Controllers\Sistema\Status;

use App\areareas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MetObtenerAreasController extends Controller
{
    public function MetObtenerAreas (Request $request)
    {
        $respuesta = false;
        $mensaje   = "";

        $ares = areareas::get();

        if (sizeof($ares) > 0) {
            $respuesta = true;
            $mensaje   = "Se obtuvieron los registros con exito";
        }else{
            $respuesta = false;
            $mensaje   = "Lo siento, no se encontraron registros";
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $ares
        ]);
    }
}
