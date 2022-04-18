<?php

namespace App\Http\Controllers\Sistema\Notificaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\nusnotificacionesusuarios;
use App\usuusuarios;

class VerNotificacionesUsuarioController extends Controller
{
    public function VerNotificacionesUsuario(Request $request)
    {   
        $respuesta = true;
        $mensaje = "Las notificaciones se leyeron correctamente";

        $usutoken = $request->header('api_token');
        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        if($usu){

            $nuss = nusnotificacionesusuarios::where('usuid', $usu->usuid)
                                            ->update(['nusleyo' => true]);

        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, no encontramos el usuario";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje" => $mensaje
        ]);
        
        return $requestsalida;

    }
}
