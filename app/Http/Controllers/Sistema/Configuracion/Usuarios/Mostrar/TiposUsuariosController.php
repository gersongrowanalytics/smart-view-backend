<?php

namespace App\Http\Controllers\Sistema\Configuracion\Usuarios\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tputiposusuarios;

class TiposUsuariosController extends Controller
{
    public function mostrarTiposUsuarios(Request $request)
    {
        $usutoken = $request->header('api_token');
        
        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{

            $tiposUsuarios = tputiposusuarios::get(['tpuid', 'tpunombre', 'tpuprivilegio']);
            if(sizeof($tiposUsuarios) > 0){
                $respuesta      = true;
                $datos          = $tiposUsuarios;
                $linea          = __LINE__;
                $mensaje        = 'Los tipos de usuario se cargaron satisfactoriamente.';
                $mensajeDetalle = sizeof($tiposUsuarios).' registros encontrados.';
            }else{
                $respuesta      = false;
                $datos          = [];
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos, no se econtraron tipos de usuarios registradas';
                $mensajeDetalle = sizeof($tiposUsuarios).' registros encontrados.';
            }


        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }
        
        return $requestsalida;
    }
}
