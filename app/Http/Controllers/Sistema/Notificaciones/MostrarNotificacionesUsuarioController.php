<?php

namespace App\Http\Controllers\Sistema\Notificaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\nusnotificacionesusuarios;
use App\usuusuarios;

class MostrarNotificacionesUsuarioController extends Controller
{
    public function MostrarNotificacionesUsuario(Request $request)
    {

        $usutoken = $request->header('api_token');
        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        $nsus = array();

        if($usu){

            $nsus = nusnotificacionesusuarios::where('usuid', $usu->usuid)
                                            ->get();

        }else{

        }

        $requestsalida = response()->json([
            "data" => $nsus
        ]);
        
        return $requestsalida;

    }
}
