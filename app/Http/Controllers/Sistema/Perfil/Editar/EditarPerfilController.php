<?php

namespace App\Http\Controllers\Sistema\Perfil\Editar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuditoriaController;
use App\usuusuarios;
use App\perpersonas;

class EditarPerfilController extends Controller
{
    public function EditarPerfil(Request $request)
    {
        $respuesta      = true;
        $mensaje        = 'Tu perfil se actualizo correctamente';
        $datos          = [];
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log            = [];

        $usutoken           = $request->header('api-token');
        $usuorganizacion    = $request['usuorganizacion'];
        $perfechanacimiento = $request['perfechanacimiento'];
        $editarCont         = $request['editarCont'];
        $usucontrasena      = $request['usucontrasena'];
        $usuusuario         = $request['usuusuario'];
        $perdireccion       = $request['perdireccion'];
        // $usucorreo          = $request['usucorreo'];
        // $percelular         = $request['percelular'];

        $usue = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                ->where('usutoken', $usutoken)
                                ->first();

        if($usue){
            $usuid = $usue->usuid;
            $usue->usuorganizacion = $usuorganizacion;
            // $usue->usucorreo       = $usucorreo;
            $usue->usuusuario      = $usuusuario;

            if($editarCont == true){
                $usue->usucontrasena   = Hash::make($usucontrasena);
            }

            if($usue->update()){
                $pere = perpersonas::find($usue->perid);
                $pere->perfechanacimiento = $perfechanacimiento;
                $pere->perdireccion       = $perdireccion;
                // $pere->percelular         = $percelular;
                if($pere->update()){

                }else{
                    $respuesta = false;
                    $mensaje   = "Lo sentimos no se pudo editar tu persona, porfavor verifica tus campos de perfil";
                }
            }else{
                $respuesta = false;
                $mensaje   = "Lo sentimos no se pudo editar tu usuario, porfavor verifica tus campos de perfil";
            }


        }else{
            $respuesta      = false;
            $mensaje        = 'Lo sentimos no se encontro tu perfil, te recomendamos volver a iniciar sesión';
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
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
            'Editar el perfil de un usuario',
            'EDITAR',
            '/perfil/editar/editarPerfil', //ruta
            $usuid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;


    }
}
