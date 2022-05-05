<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios;

use App\estestados;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\paupaisesusuarios;
use App\perpersonas;
use App\usuusuarios;
use App\ussusuariossucursales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CrearUsuariosController extends Controller
{
    public function CrearUsuarios(Request $request)
    {
        $respuesta      = false;
        $mensaje        = '';
        $log            = [];
        $pkid           = [];
        $usun = [];
        $usutoken   = $request->header('api_token');
        
        $re_imagen       = $request['re_imagen'];
        $re_nombre       = $request['re_nombre'];
        $re_apellidos    = $request['re_apellidos'];
        $re_usuario      = $request['re_usuario'];
        $re_correo       = $request['re_correo'];
        $re_contrasenia  = $request['re_contrasenia'];
        $re_celular      = $request['re_celular'];
        $re_tipo_usuario = $request['re_tipo_usuario'];
        $re_fecha_inicio = $request['re_fecha_inicio'];
        $re_fecha_fin    = $request['re_fecha_fin'];
        $re_paises       = $request['re_paises'];
        // $zonas        = $request['zonas'];
        $re_estado       = $request['re_estado'];
        $re_sucursales   = $request['re_sucursales'];

        $per = perpersonas::where('pernombre', $re_nombre)
                                ->where('perapellidopaterno',$re_apellidos)
                                ->first(['perid']);
        
        $perid = 0;
        if ($per) {
            $perid = $per->perid;
        }else{
            $pern = new perpersonas;
            $pern->tdiid              = 1;
            $pern->pernombrecompleto  = $re_nombre." ".$re_apellidos;
            $pern->pernombre          = $re_nombre;
            $pern->perapellidopaterno = $re_apellidos;
            $pern->percelular         = $re_celular;
            if($pern->save()){
                $perid = $pern->perid;
                $log[] = "La persona se registro correctamente perid: ".$perid;
            }else{
                $log[] = "No se pudo registrar a la persona";
                $respuesta = false;
                $mensaje   = "Lo sentimos no se pudo crear el usuario, error al crear la persona";
            }
        }

        $usuid = 0;

        $usu = usuusuarios::where('usuusuario', $re_usuario)->first();

        if($usu){
            
            $usuid = $usu->usuid;
            $usu->usuimagen         = $re_imagen;
            $usu->tpuid             = $re_tipo_usuario;
            $usu->perid             = $perid;
            $usu->estid             = $re_estado;
            $usu->usuusuario        = $re_usuario;
            $usu->usucorreo         = $re_correo;
            $usu->usufechainicio    = $re_fecha_inicio;
            $usu->usufechafinal     = $re_fecha_fin;
            $usu->usucontrasena     = Hash::make($re_contrasenia);
            if($usu->update()){
                $log[] = "El usuario se edito correctamente usuid: ".$usu->usuid;

                paupaisesusuarios::where('usuid', $usu->usuid)->delete();

                foreach ($re_paises as $pais) {
                    $paun = new paupaisesusuarios();
                    $paun->paiid = $pais['paiid'];
                    $paun->usuid = $usu->usuid;
                    if ($paun->save()) {
                        $log[] = "Se registro correctamente el pais de id:".$pais['paiid'];
                        $respuesta = true;
                        $mensaje = "El usuario se edito correctamente";
                    }else{
                        $log[] = "No se registro el usuario, surgio un error al registrar el pais del usuario";
                        $respuesta = false;
                        $mensaje = "Lo sentimos, ocurrio un error al momento de registrar los paises del usuario";
                    }
                }
            }

        }else{

            $usun = new usuusuarios();
            $usun->usuimagen         = $re_imagen;
            $usun->tpuid             = $re_tipo_usuario;
            $usun->perid             = $perid;
            $usun->estid             = $re_estado;
            $usun->usuusuario        = $re_usuario;
            $usun->usucorreo         = $re_correo;
            $usun->usufechainicio    = $re_fecha_inicio;
            $usun->usufechafinal     = $re_fecha_fin;
            $usun->usucontrasena     = Hash::make($re_contrasenia);
            $usun->usutoken          = Str::random(60);
            if($usun->save()){
                $usuid = $usun->usuid;
                $log[] = "El usuario se registro correctamente usuid: ".$usun->usuid;
                
                foreach ($re_paises as $pais) {
                    $paun = new paupaisesusuarios();
                    $paun->paiid = $pais['paiid'];
                    $paun->usuid = $usun->usuid;
                    if ($paun->save()) {
                        $log[] = "Se registro correctamente el pais de id:".$pais['paiid'];
                        $respuesta = true;
                        $mensaje = "El usuario se registro correctamente";
                    }else{
                        $log[] = "No se registro el usuario, surgio un error al registrar el pais del usuario";
                        $respuesta = false;
                        $mensaje = "Lo sentimos, ocurrio un error al momento de registrar los paises del usuario";
                    }
                }
            }else{
                $log[] = "No se registro el usuario";
                $respuesta = false;
                $mensaje = "Lo sentimos, ocurrio un error al momento de crear el usuario";
            }

        }


        if($usuid != 0){

            ussusuariossucursales::where('usuid', $usuid)->delete();

            foreach($re_sucursales as $sucursal){

                if(isset($sucursal['sucpromocioncrear'])){
                    if($sucursal['sucpromocioncrear'] == true){
                        $ussn = new ussusuariossucursales;
                        $ussn->usuid = $usuid;
                        $ussn->sucid = $sucursal['sucid'];
                        $ussn->save();
                    }
                }

            }
        }



        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $usun
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            null,
            $request,
            $requestsalida,
            'Crear nuevo usuario',
            'CREAR',
            '/administrativo/usuarios/crear/usuario', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }

        return $requestsalida;
    }
}
