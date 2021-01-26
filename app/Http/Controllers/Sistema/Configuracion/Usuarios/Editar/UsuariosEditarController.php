<?php

namespace App\Http\Controllers\Sistema\Configuracion\Usuarios\Editar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\Hash;
use App\usuusuarios;

class UsuariosEditarController extends Controller
{
    /**
     * Editar la contraseña, usuario, correo y tipo de usuario
     */
    public function editarUsuario(Request $request)
    {
        $usutoken   = $request->header('api-token');
        $nuevTpuid  = $request['nuevTpuid'];
        $usuid      = $request['usuid'];
        $nuevUsua   = $request['nuevUsua'];
        $nuevCorr   = $request['nuevCorr'];
        $editarCont = $request['editarCont'];
        $nuevCont   = $request['nuevCont'];

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log            = [];

        try{
            
            $usuario = usuusuarios::find($usuid);
            $usuario->tpuid         = $nuevTpuid; 
            $usuario->usuusuario    = $nuevUsua;
            $usuario->usucorreo     = $nuevCorr;
            if($editarCont == true){
                $usuario->usucontrasena = Hash::make($nuevCont);
                $log[] = "La contraseña se esta editando";
            }

            if($usuario->update()){
                $respuesta      = true;
                $datos          = $usuario;
                $linea          = __LINE__;
                $mensaje        = 'El usuario se edito correctamente';
                $mensajeDetalle = '';
                $log[] = "El usuario se edito correctamente";
            }else{
                $respuesta      = false;
                $datos          = [];
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos, no se pudo editar el usuario';
                $mensajeDetalle = '';
                $log[] = "Hubo problemas al momento de editar el usuario";
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[] = "ERROR DE SERVIDOR: ".$mensajedev;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            null,
            $request,
            $requestsalida,
            'Editar los datos del usuario, tipo de usuario, usuario, correo y contraseña',
            'EDITAR',
            '/configuracion/usuarios/editarUsuario', //ruta
            $usuid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
