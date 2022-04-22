<?php

namespace App\Http\Controllers\Sistema\ElementrosEnviados;

use App\Http\Controllers\Controller;
use App\tnotiposnotificaciones;
use Illuminate\Http\Request;

class MostrarTiposElementosEnviadosController extends Controller
{
    public function MostrarTiposElementosEnviados ()
    {
        $respuesta = false;
        $mensaje = "";

        $tno = tnotiposnotificaciones::get();

        if (sizeof($tno) > 0) {
            $respuesta      = true;
            $mensaje        = 'Los tipos de notificaciones se cargaron satisfactoriamente';
        }else{
            $respuesta      = false;
            $mensaje        = 'Los tipos de notificaciones no se cargaron satisfactoriamente';
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $tno,
        ]);
        
        return $requestsalida;
    }
}
