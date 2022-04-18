<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\usuusuarios;
use App\tuptiposusuariospermisos;
use App\ussusuariossucursales;
use App\cejclientesejecutivos;
use DateTime;

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

        $re_logintoken = $request['logintoken'];
        $re_token      = $request['token'];

        $aparecerTerminosCondiciones = false;

        try{
            
            if($re_logintoken == true){
                $usuusaurio = usuusuarios::join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                        ->join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                        ->where('usuusuarios.usutoken', $re_token)
                                        ->first([
                                            'usuusuarios.usuid',
                                            'usuusuarios.usuusuario',
                                            'usuusuarios.usucorreo',
                                            'usuusuarios.usutoken',
                                            'usuusuarios.usucontrasena',
                                            'usuusuarios.tpuid',
                                            'tpu.tpunombre',
                                            'tpu.tpuprivilegio',
                                            'per.pernombre',
                                            'per.pernombrecompleto',
                                            'per.perdireccion',
                                            'per.perfechanacimiento',
                                            'per.percelular',
                                            'usuusuarios.usuorganizacion',
                                            'usuaceptoterminos',
                                            'usucerrosesion'
                                        ]);
            }else{
                $usuusaurio = usuusuarios::join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                        ->join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                                        ->where('usuusuarios.usuusuario', $usuario)
                                        ->first([
                                            'usuusuarios.usuid',
                                            'usuusuarios.usuusuario',
                                            'usuusuarios.usucorreo',
                                            'usuusuarios.usutoken',
                                            'usuusuarios.usucontrasena',
                                            'usuusuarios.tpuid',
                                            'tpu.tpunombre',
                                            'tpu.tpuprivilegio',
                                            'per.pernombre',
                                            'per.pernombrecompleto',
                                            'per.perdireccion',
                                            'per.perfechanacimiento',
                                            'per.percelular',
                                            'usuusuarios.usuorganizacion',
                                            'usuaceptoterminos',
                                            'usucerrosesion'
                                        ]);
            }

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

                    $cej = cejclientesejecutivos::join('usuusuarios as usu', 'usu.usuid', 'cejclientesejecutivos.cejejecutivo')
                                                ->join('perpersonas as per', 'per.perid', 'usu.perid')
                                                ->where('cejclientesejecutivos.cejcliente', $usuusaurio->usuid)
                                                ->first([
                                                    'cejclientesejecutivos.cejid',
                                                    'per.pernombre',
                                                    'per.pernombrecompleto'
                                                ]);

                    if($cej){
                        $usuusaurio->idcej     = $cej->cejid;
                        if($cej->pernombre == null){
                            $usuusaurio->ejecutivo = $cej->pernombrecompleto;
                        }else{
                            $usuusaurio->ejecutivo = $cej->pernombre;
                        }
                    }else{
                        $usuusaurio->idcej     = 0;
                        $usuusaurio->ejecutivo = 'No tiene';
                    }


                    if(isset($usuusaurio->usuaceptoterminos)){
                        date_default_timezone_set("America/Lima");
                        $fechaActual = new DateTime();
                        $fechaAceptacionTerminos = new DateTime($usuusaurio->usuaceptoterminos);

                        $diff = $fechaActual->diff($fechaAceptacionTerminos);

                        if($usuusaurio->usucerrosesion == true){
                            $aparecerTerminosCondiciones = true;
                        }else{
                            if($diff->days >= 7){
                                $aparecerTerminosCondiciones = true;

                                date_default_timezone_set("America/Lima");
                                $fechaActual = date('Y-m-d H:i:s');

                                $usue = usuusuarios::where('usuid', $usuusaurio->usuid)->first();
                                $usue->usuaceptoterminos = $fechaActual;
                                $usue->update();

                                // $AuditoriaController = new AuditoriaController;
                                // $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
                                //     $usutoken,
                                //     $usuidAud,
                                //     null,
                                //     $request,
                                //     [],
                                //     "VOLVER A ACEPTAR LOS TERMINOS  Y CONDICIONES DESPUES DE 7 DIAS",
                                //     'ACEPTAR TERMINOS Y CONDICONES',
                                //     '/aceptar-terminos-condiciones', //ruta
                                //     [],
                                //     [],
                                //     5 // Aceptar terminos y condiciones
                                // );

                            }else{
                                $aparecerTerminosCondiciones = false;
                            }
                        }
                        
                    }else{
                        $aparecerTerminosCondiciones = true;
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

        if($respuesta == true){
            $cadena_de_texto = $usuario;
            $cadena_buscada  = "kcc.com";

            $posicion_coincidencia = strpos($cadena_de_texto, $cadena_buscada);

            if ($posicion_coincidencia === false) {
            
            } else {
                $aparecerTerminosCondiciones = false;
            }
        }

        
        return response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            'mostrarterminos' => $aparecerTerminosCondiciones,
        ]);
    }

    public function MetCerrarSession(Request $request)
    {

        $usutoken = $request->header('api_token');

        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        if($usu){
            $usu->usucerrosesion = true;
            $usu->update();
        }

    }

}
