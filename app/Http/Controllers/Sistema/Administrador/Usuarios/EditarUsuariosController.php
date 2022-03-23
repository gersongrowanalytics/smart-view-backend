<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\perpersonas;
use App\usuusuarios;
use Illuminate\Http\Request;

class EditarUsuariosController extends Controller
{
    public function EditarUsuarios (Request $request)
    {
        $respuesta      = false;
        $mensaje        = '';
        $log            = [];
        $pkid           = [];

        $usutoken   = $request->header('api_token');
        
        $usuid        = $request['usuid'];
        $nombre       = $request['nombre'];
        $apellidos    = $request['apellidos'];
        $correo       = $request['correo'];
        $contrasenia  = $request['contrasenia'];
        $tipo_usuario = $request['tipo_usuario'];
        $estado       = $request['estado'];
        
        $per = usuusuarios::join('perpersonas as per','per.perid', 'usuusuarios.perid')
                            ->where('usuusuarios.usuid', $usuid)
                            ->first(['usuusuarios.perid']);
        $perid = 0;
        if ($per) {
            $perid = $per->perid;
            $pere = perpersonas::find($perid);
            $pere->pernombre          = $nombre;
            $pere->perapellidopaterno = $apellidos;
            $pere->pernombrecompleto  = $nombre." ".$apellidos;
            if($pere->update()){
                $log[] = "La persona se edito correctamente perid".$perid;

                $usue = usuusuarios::find($usuid);
                $usue->tpuid = $tipo_usuario;
                $usue->estid = $estado;
                $usue->usucontrasena = $contrasenia;
                $usue->usucorreo = $correo;
                if ($usue->update()) {
                    $respuesta  = true;
                    $mensaje    = "El usuario se edito correctamente";
                    $log[]      = "El usuario se edito se edito correctamente usuid".$usuid;
                }else{
                    $respuesta  = false;
                    $mensaje    = "Lo sentimos no se pudo editar el usuario";
                    $log[]      = "El usuario no se edito correctamente";
                }

            }else{
                $respuesta  = false;
                $mensaje    = "Lo sentimos no se pudo editar el usuario, error al editar los datos de la persona";
                $log[]      = "La persona no se edito correctamente";
            }
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $usue
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
            '/administrativo/usuarios/editar/usuario', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }

        return $requestsalida;
    }
}
