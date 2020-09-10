<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\usuusuarios;
use App\tuptiposusuariospermisos;
use App\ussusuariossucursales;
use App\cejclientesejecutivos;

class loginController extends Controller
{
    public function login(Request $request)
    {

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $usuario    = $request['usuario'];
        $contrasena = $request['contrasena'];

        try{
            
            $usuusaurio = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                        ->where('usuusuarios.usuusuario', $usuario)
                                        ->first([
                                            'usuusuarios.usuid',
                                            'usuusuarios.usuusuario',
                                            'usuusuarios.usutoken',
                                            'usuusuarios.usucontrasena',
                                            'usuusuarios.tpuid',
                                            'per.pernombre',
                                            'per.pernombrecompleto'
                                        ]);

            if($usuusaurio){

                if (Hash::check($contrasena, $usuusaurio->usucontrasena)) {

                    $tuptiposusuariospermisos = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                                                        ->where('tuptiposusuariospermisos.tpuid', $usuusaurio->tpuid )
                                                                        ->get([
                                                                            'tuptiposusuariospermisos.tupid',
                                                                            'pem.pemnombre',
                                                                            'pem.pemslug'
                                                                        ]);

                    if(sizeof($tuptiposusuariospermisos) > 0){
                        $usuusaurio->permisos = $tuptiposusuariospermisos;
                    }else{
                        $usuusaurio->permisos = [];
                    }

                    $ussusuariossucursales = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                                    ->where('ussusuariossucursales.usuid', $usuusaurio->usuid )
                                                                    ->get([
                                                                        'ussusuariossucursales.ussid',
                                                                        'suc.sucid',
                                                                        'suc.sucnombre'
                                                                    ]);
                                                                    
                    if(sizeof($ussusuariossucursales) > 0){
                        $usuusaurio->sucursales = $ussusuariossucursales;
                    }else{
                        $usuusaurio->sucursales = [];
                    }

                    $cej = cejclientesejecutivos::join('usuusaurio as usu', 'usu.usuid', 'cejclientesejecutivos.cejejecutivo')
                                                ->join('perpersonas as per', 'per.perid', 'usu.perid')
                                                ->where('cejclientesejecutivos.cejcliente', $usuusaurio->usuid)
                                                ->first([
                                                    'cejclientesejecutivos.cejid',
                                                    'per.pernombre',
                                                ]);

                    if($cej){
                        $usuusaurio->idcej     = $cej->cejid;
                        $usuusaurio->ejecutivo = $cej->pernombre;
                    }else{
                        $usuusaurio->idcej     = 0;
                        $usuusaurio->ejecutivo = 'No tiene';
                    }


                    $respuesta      = true;
                    $mensaje        = 'Login Correcto';
                    $mensajeDetalle = 'Bienvenido.';
                    $linea          = __LINE__;
                    $datos          = $usuusaurio;
                }else{
                    $respuesta      = false;
                    $mensaje        = 'Login Incorrecto';
                    $mensajeDetalle = 'La contraseña es incorrecta.';
                    $linea          = __LINE__;
                    $datos          = [];
                }

            }else{
                $respuesta      = false;
                $mensaje        = 'Login Incorrecto';
                $mensajeDetalle = 'El usuario no esta registrado.';
                $linea          = __LINE__;
                $datos          = [];
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }
        
        return response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev
        ]);
    }
}
