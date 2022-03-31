<?php

namespace App\Http\Controllers\Sistema\Pai\Mostrar;

use App\Http\Controllers\Controller;
use App\paipaises;
use Illuminate\Http\Request;

class PaisMostrarController extends Controller
{
    public function MostrarPais(Request $request)
    {
        $respuesta  = true;
        $mensaje    = "Se cargaron todos los Pais satisfactoriamente";
        $data       = [];

        $pais = paipaises::get(['paiid', 'painombre','paiicono','paiiconocircular','paiiconomas','estid']);

        if(sizeof($pais) > 0){
            $data = $pais;
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, no se encontraron Pais registrados";
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $data
        ]);
    }
}
