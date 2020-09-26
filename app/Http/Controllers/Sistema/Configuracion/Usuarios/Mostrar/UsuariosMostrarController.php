<?php

namespace App\Http\Controllers\Sistema\Configuracion\Usuarios\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\usuusuarios;

class UsuariosMostrarController extends Controller
{
    public function mostrarUsuarios(Request $request)
    {
        $usutoken = $request->header('api_token');

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{
            $usuarios = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->get([
                                    'usuusuarios.usuid',
                                    'tpu.tpuid',
                                    'tpu.tpunombre',
                                    'tpu.tpuprivilegio',
                                    'per.perid',
                                    'per.pernombrecompleto',
                                    'usuusuarios.usuusuario',
                                    'usuusuarios.usucorreo',
                                ]);

            if(sizeof($usuarios) > 0){
                $respuesta      = true;
                $datos          = $usuarios;
                $linea          = __LINE__;
                $mensaje        = 'Los usuarios se cargaron satisfactoriamente.';
                $mensajeDetalle = sizeof($usuarios).' registros encontrados.';
            }else{
                $respuesta      = false;
                $datos          = [];
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos, no se econtraron usuarios registradas';
                $mensajeDetalle = sizeof($usuarios).' registros encontrados.';
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);

        return $requestsalida;
    }
}
