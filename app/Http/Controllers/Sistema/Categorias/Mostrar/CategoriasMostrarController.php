<?php

namespace App\Http\Controllers\Sistema\Categorias\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\catcategorias;

class CategoriasMostrarController extends Controller
{
    public function mostrarTodasCategorias(Request $request)
    {
        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{
            
            $categorias = catcategorias::get([]);

            if(sizeof($categorias) > 0){
                $respuesta      = true;
                $linea          = __LINE__;
                $datos          = $categorias;
                $mensaje        = 'Categorias obtenidas';
                $mensajeDetalle = 'Se encontraron '.sizeof($categorias).' categorias';
            }else{
                $respuesta      = false;
                $linea          = __LINE__;
                $datos          = [];
                $mensaje        = 'No existen categorias registradas';
                $mensajeDetalle = 'Se encontraron '.sizeof($categorias).' categorias';
            }


        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        return response()->json([
            $respuesta      => $respuesta,
            $mensaje        => $mensaje,
            $datos          => $datos,
            $linea          => $linea,
            $mensajeDetalle => $mensajeDetalle,
            $mensajedev     => $mensajedev
        ]);
    }
}
