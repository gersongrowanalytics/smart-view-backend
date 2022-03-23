<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios;

use App\Http\Controllers\Controller;
use App\usuusuarios;
use Illuminate\Http\Request;

class MostrarUsuariosController extends Controller
{
    public function MostrarUsuarios(Request $request)
    {
        $respuesta      = false;
        $mensaje        = '';

        $usutoken = $request->header('api_token');
        
        $usuarios = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->orderBy('usuusuarios.created_at', 'DESC')
                                ->get([
                                    'usuusuarios.usuid',
                                    'tpu.tpuid',
                                    'tpu.tpunombre',
                                    'per.perid',
                                    'per.pernombrecompleto',
                                    'usuusuarios.usucorreo',
                                    'usuusuarios.usucontrasena',
                                    'usuusuarios.estid'
                                ]);

        if(sizeof($usuarios) > 0){
            $respuesta      = true;
            $mensaje        = 'Los usuarios se cargaron satisfactoriamente';
        }else{
            $respuesta      = false;
            $mensaje        = 'Los usuarios no se cargaron satisfactoriamente';
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $usuarios
        ]);

        return $requestsalida;
    }
}
