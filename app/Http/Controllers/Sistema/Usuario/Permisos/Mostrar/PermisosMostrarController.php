<?php

namespace App\Http\Controllers\Sistema\Usuario\Permisos\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tuptiposusuariospermisos;
use App\usuusuarios;

class PermisosMostrarController extends Controller
{
    // Muestra los permisos de usuario 
    public function mostrarPermisosUsuario(Request $request)
    {
        $usutoken = $request->header('api_token');
        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['tpuid']);

        $respuesta = false;
        $mensaje   = '';
        $datos     = [];

        $tup = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                        ->where('tuptiposusuariospermisos.tpuid', $usuusuario->tpuid)
                                        ->get([
                                            'tuptiposusuariospermisos.tupid',
                                            'tuptiposusuariospermisos.tpuid',
                                            'pem.pemid',
                                            'pem.pemslug'
                                        ]);

        if(sizeof($tup) > 0){
            $respuesta  = true;
            $datos      = $tup;
            $mensajeDetalle = sizeof($tup).' registros encontrados.';
        }else{
            $respuesta = false;
            $datos      = [];
            $mensajeDetalle = sizeof($tup).' registros encontrados.';
        }

        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
        ]);
        
        return $requestsalida;
    }
}
