<?php

namespace App\Http\Controllers\Sistema\Cat\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\catcategorias;

class CatsMostrarController extends Controller
{
    public function MostrarCats(Request $request)
    {
        $respuesta  = true;
        $mensaje    = "Se cargaron todos los tpus satisfactoriamente";
        $data       = [];

        $cats = catcategorias::get(['catid', 'catnombre']);

        if(sizeof($cats) > 0){
            $data = $cats;
        }else{
            $respuesta = false;
            $mensaje   = "Lo sentimos, no se encontraron categorias registradas";
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $data
        ]);

    }
}
