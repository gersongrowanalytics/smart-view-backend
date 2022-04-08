<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios;

use App\estestados;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\paupaisesusuarios;
use App\perpersonas;
use App\usuusuarios;
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
        
        $nombre       = $request['nombre'];
        $apellidos    = $request['apellidos'];
        $correo       = $request['correo'];
        $correo_inst  = $request['correo_inst'];
        $contrasenia  = $request['contrasenia'];
        $celular      = $request['celular'];
        $tipo_usuario = $request['tipo_usuario'];
        $fecha_inicio = $request['fecha_inicio'];
        $fecha_fin    = $request['fecha_fin'];
        $paises       = $request['paises'];
        // $zonas        = $request['zonas'];
        $estado       = $request['estado'];
        
        $per = perpersonas::where('pernombre', $nombre)
                                ->where('perapellidopaterno',$apellidos)
                                ->first(['perid']);
        
        $perid = 0;
        if ($per) {
            $perid = $per->perid;
            $log[] = "Esta persona ya se encuentra registrada perid: ".$perid;
            $respuesta      = false;
            $mensaje        = 'Esta persona ya se encuentra registrada';
        }else{
            $pern = new perpersonas;
            $pern->tdiid              = 1;
            $pern->pernombrecompleto  = $nombre." ".$apellidos;
            $pern->pernombre          = $nombre;
            $pern->perapellidopaterno = $apellidos;
            $pern->percelular         = $celular;
            if($pern->save()){
                $perid = $pern->perid;
                $log[] = "La persona se registro correctamente perid: ".$perid;

                $usun = new usuusuarios();
                $usun->tpuid             = $tipo_usuario;
                $usun->perid             = $perid;
                $usun->estid             = $estado;
                $usun->usucorreo         = $correo_inst;
                $usun->usucorreopersonal = $correo;
                $usun->usufechainicio    = $fecha_inicio;
                $usun->usufechafinal     = $fecha_fin;
                $usun->usucontrasena     = Hash::make($contrasenia);
                $usun->usutoken          = Str::random(60);
                if($usun->save()){
                    $log[] = "El usuario se registro correctamente usuid: ".$usun->usuid;
                   
                    foreach ($paises as $pais) {
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
            }else{
                $log[] = "No se pudo registrar a la persona";
                $respuesta = false;
                $mensaje   = "Lo sentimos no se pudo crear el usuario, error al crear la persona";
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
