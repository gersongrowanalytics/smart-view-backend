<?php

namespace App\Http\Controllers\Sistema\ListaPrecios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ltplistaprecios;

class EliminarListaPreciosController extends Controller
{
    public function EliminarListaPrecios(Request $request)
    {

        $respuesta = true;
        $mensaje = "El item de lista de precios se elimino correctamente";

        $re_ltpid = $request['ltpid'];

        $ltpd = ltplistaprecios::where('ltpid', $re_ltpid)->first();

        if($ltpd){

            if($ltpd->delete()){

            }else{
                $respuesta = false;
                $mensaje = "El item de lista de precios se elimino correctamente";    
            }

        }else{
            $respuesta = false;
            $mensaje = "El item de la lista de precios no existe";
        }

        return array(
            "respuesta" => $respuesta,
            "mensaje" => $mensaje,
        );

    }
}
