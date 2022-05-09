<?php

namespace App\Http\Controllers\Sistema\Perfil\Editar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuditoriaController;
use App\usuusuarios;
use App\perpersonas;
use Illuminate\Support\Str;

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

        $usutoken           = $request->header('api_token');
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

    public function EditarPerfilNuevo (Request $request)
    {
        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];

        $usutoken             = $request->header('api_token');
        $re_imagen            = $request['re_imagen'];
        $re_nombre            = $request['re_nombre'];//ok
        $re_apellidoPaterno   = $request['re_apellidoPaterno'];//ok
        $re_apellidoMaterno   = $request['re_apellidoMaterno']; //ok
        $re_correo            = $request['re_correo'];//ok
        $re_telefono          = $request['re_telefono'];//ok
        // $re_idioma          = $request['re_idioma'];
        // $re_pais            = $request['re_pais'];
        $re_direccion         = $request['re_direccion'];//ok
        $re_contrasenia       = $request['re_contrasenia'];
        $re_nuevaContrasenia  = $request['re_nuevaContrasenia'];
        $re_editarContrasenia = $request['re_editarContrasenia'];

        $usu = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                ->where('usutoken', $usutoken)
                                ->first();
      
        if($usu){
            if ($re_editarContrasenia == true) {
                if (Hash::check($re_contrasenia, $usu->usucontrasena)) {

                    $perid = $usu->perid;
                    $pere = perpersonas::find($perid);
                    $pere->pernombre          = $re_nombre;
                    $pere->perapellidopaterno = $re_apellidoPaterno;
                    $pere->perapellidomaterno = $re_apellidoMaterno;
                    $pere->pernombrecompleto  = $re_nombre." ".$re_apellidoPaterno." ".$re_apellidoMaterno;
                    $pere->perdireccion       = $re_direccion;
                    $pere->percelular         = $re_telefono;
                    if ($pere->update()) {
                        $respuesta      = true;
                        $mensaje        = 'Los datos de la persona se actualizaron correctamente';

                        $usu->usuusuario = $re_correo;
                        $usu->usucontrasena = Hash::make($re_nuevaContrasenia);

                        if (strstr($re_imagen, env('APP_URL')) == false) {
                            list(, $base64) = explode(',', $re_imagen);
                            $fichero = '/Sistema/Administrador/Imagenes/Usuarios/'.Str::random(5).".png";
                            $archivo = base64_decode($base64);
                            file_put_contents(base_path().'/public'.$fichero, $archivo);

                            $usu->usuimagen = env('APP_URL').$fichero;
                        }
                        
                        if ($usu->update()) {
                            $datos = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                                    ->where('usutoken', $usutoken)
                                                    ->first();
                            $respuesta = true;
                            $mensaje   = 'Los datos del usuario se actualizaron correctamente';
                        }else{
                            $respuesta = false;
                            $mensaje   = 'Lo siento, hubo un error al actualizar los datos del usuario';
                        }
                    } else {
                        $respuesta = false;
                        $mensaje   = 'Lo siento, hubo un error al momento de actualizar los datos de la persona';
                    }
                }else{
                    $respuesta = false;
                    $mensaje   = 'Su contraseña es incorrecta, vuelva a ingresar su contraseña';
                }
            }else{
                $perid = $usu->perid;
                $pere = perpersonas::find($perid);
                $pere->pernombre          = $re_nombre;
                $pere->perapellidopaterno = $re_apellidoPaterno;
                $pere->perapellidomaterno = $re_apellidoMaterno;
                $pere->pernombrecompleto  = $re_nombre." ".$re_apellidoPaterno." ".$re_apellidoMaterno;
                $pere->perdireccion       = $re_direccion;
                $pere->percelular         = $re_telefono;
                if ($pere->update()) {
                    $respuesta      = true;
                    $mensaje        = 'Los datos de la persona se actualizaron correctamente';

                    $usu->usuusuario = $re_correo;

                    if (strstr($re_imagen, env('APP_URL')) == false) {
                        list(, $base64) = explode(',', $re_imagen);
                        $fichero = '/Sistema/Administrador/Imagenes/Usuarios/'.Str::random(5).".png";
                        $archivo = base64_decode($base64);
                        file_put_contents(base_path().'/public'.$fichero, $archivo);

                        $usu->usuimagen = env('APP_URL').$fichero;
                    }

                    $usu->usuimagen = env('APP_URL').$fichero;

                    if ($usu->update()) {
                        $datos = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                                ->where('usutoken', $usutoken)
                                                ->first();
                        $respuesta = true;
                        $mensaje   = 'Los datos del usuario se actualizaron correctamente';
                    }else{
                        $respuesta = false;
                        $mensaje   = 'Lo siento, hubo un error al actualizar los datos del usuario';
                    }
                } else {
                    $respuesta = false;
                    $mensaje   = 'Lo siento, hubo un error al momento de actualizar los datos de la persona';
                }
            }
        } else {
            $respuesta = false;
            $mensaje   = 'No existen datos del usuario';
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $datos
        ]);

        return $requestsalida;
    }
}
