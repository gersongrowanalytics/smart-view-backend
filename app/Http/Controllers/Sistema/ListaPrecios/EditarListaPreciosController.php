<?php

namespace App\Http\Controllers\Sistema\ListaPrecios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ltplistaprecios;

class EditarListaPreciosController extends Controller
{
    public function EditarListaPrecios(Request $request)
    {

        $respuesta = true;
        $mensaje = "";

        $re_ltpid = $request['ltpid'];
        $re_descproducto = $request['descproducto'];

        $ltp = ltplistaprecios::where('ltpid', $re_ltpid)->first();

        if($ltp){
            
            $ltp->ltpeditandonombre = true;
            $ltp->ltpdescripcionproducto = $re_descproducto;
            $ltp->ltpduplicadocomplejo = false;
            if($ltp->update()){
                
            }else{
                $respuesta = false;
                $mensaje = "Lo sentimos, no se pudo editar la lista de precios";
            }

        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, la fila de la lista de precios no existe";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
        ]);
        
        return $requestsalida;

    }
}
