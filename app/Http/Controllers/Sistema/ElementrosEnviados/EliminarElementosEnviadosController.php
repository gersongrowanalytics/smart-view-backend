<?php

namespace App\Http\Controllers\Sistema\ElementrosEnviados;

use App\Http\Controllers\Controller;
use App\uceusuarioscorreosenviados;
use Illuminate\Http\Request;

class EliminarElementosEnviadosController extends Controller
{
    public function EliminarElementosEnviados (Request $request)
    {
        $respuesta = false;
        $mensaje = "";

        $re_uceid = $request['re_uceid'];

        $uce = uceusuarioscorreosenviados::where('uceid', $re_uceid)->first();

        if ($uce->delete()) {
            $respuesta = true;
            $mensaje = "El item de la lista elementos enviados se eliminó correctamente";
        }else{
            $respuesta = false;
            $mensaje = "Ingrese un id válido";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
        ]);
        
        return $requestsalida;
    }
}
