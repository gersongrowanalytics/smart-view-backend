<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios\Mail;

use App\Http\Controllers\Controller;
use App\Mail\MailUsuariosInformacion;
use App\usuusuarios;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Mail;

class MetCorreoUsuarioController extends Controller
{
    public function MetCorreoUsuario (Request $request)
    {
        $respuesta   = true;
        $mensaje     = "";
        $mensajeserv = [];
        $estado      = 200;
        $asunto      = "";

        $req_correo      = $request['req_correo'];
        $req_usuario     = $request['req_usuario'];
        $req_contrasenia = $request['req_contrasenia'];
        $req_asunto      = $request['req_asunto'];

        $usu = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                            ->where('usuusuarios.usuusuario', $req_usuario)
                            ->first("per.pernombre");

        if ($usu) {
            try {
                if ($req_asunto == 'true') {
                    $asunto  = "¡Bienvenido a Creciendo Juntos! ¿Listo para colaborar?";
                    $mensaje = "El correo de bienvenida fue enviado correctamente";
                }else{
                    // $asunto  = "CORREO DE ACTUALIZACIÓN DE USUARIO";
                    $asunto  = "¡Bienvenido a Creciendo Juntos! ¿Listo para colaborar?";
                    // $mensaje = "El correo de actualización de usuario fue enviado correctamente";
                    $mensaje = "El correo de bienvenida fue enviado correctamente";
                }

                $data = [
                    "usuario"     => $req_usuario,
                    "contrasenia" => $req_contrasenia,
                    "nombre"      => $usu->pernombre
                ];
                Mail::to([$req_correo])
                    ->cc(['mzorrilla@kcc.com', 'miguel.caballero@grow-analytics.com.pe', 'gerson.vilca@grow-analytics.com.pe', 'eunice.calle@grow-analytics.com.pe'])
                    ->send( new MailUsuariosInformacion($data, $asunto));
            } catch (Exception $e) {
                $mensajeserv[]  = $e->getMessage();
                $respuesta      = false;
                $mensaje        = ["Lo siento, surgió un error al momento de enviar el correo"];
                $estado         = 406;
            }
        }else{
            $respuesta = false;
            $mensaje   = ['Lo siento, los datos del usuario no se encuentran registrados'];
            $estado    = 406;
        }
        
        return response()->json([
            "respuesta"   => $respuesta,
            "mensaje"     => $mensaje,
            "mensajeserv" => $mensajeserv
        ], $estado);
    }
}
