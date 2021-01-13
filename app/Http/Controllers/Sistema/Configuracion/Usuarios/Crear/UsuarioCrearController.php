<?php

namespace App\Http\Controllers\Sistema\Configuracion\Usuarios\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\usuusuarios;
use App\perpersonas;
use App\ussusuariossucursales;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Mail\MailCrearUsuario;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuditoriaController;

class UsuarioCrearController extends Controller
{
    public function CrearUsuario(Request $request)
    {
        $usutoken   = $request->header('api_token');

        $tdiid       = $request['tdiid'];
        $pernum      = $request['pernum'];
        $pernomcomp  = $request['pernomcomp'];
        $pernom      = $request['pernom'];
        $perapellpat = $request['perapellpat'];
        $perapellmat = $request['perapellmat'];

        $tpuid      = $request['tpuid'];
        $zonid      = $request['zonid'];
        $soldto     = $request['soldto'];
        $usuario    = $request['usuario'];
        $contrasena = $request['contrasena'];
        $correo     = $request['correo'];

        $sucs     = $request['sucs'];
        
        $respuesta      = true;
        $mensaje        = '';
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log            = [];

        DB::beginTransaction();

        $pkid = 0;

        try{
            
            $per = perpersonas::where('pernumerodocumentoidentidad', $pernum)->first(['perid']);
            $perid = 0;
            if($per){
                $perid = $per->perid;
                $log[] = "Esta persona ya se encuentra registrada perid: ".$perid;
            }else{
                $pern = new perpersonas;
                $pern->tdiid                        = $tdiid;
                $pern->pernumerodocumentoidentidad  = $pernum;
                $pern->pernombrecompleto            = $pernomcomp;
                $pern->pernombre                    = $pernom;
                $pern->perapellidopaterno           = $perapellpat;
                $pern->perapellidomaterno           = $perapellmat;
                if($pern->save()){
                    $perid = $pern->perid;
                    $log[] = "La persona se registro correctamente perid: ".$perid;
                }else{
                    $log[] = "No se pudo registrar a la persona";
                    $respuesta = false;
                    $mensaje = "Lo sentimos, no se pudo registrar a la persona, porfavor verifique sus nombres o numero de identidad";
                }
            }

            if($perid != 0){
                
                $pkid = 'PER-'.$perid;
                
                $usu = usuusuarios::where('ususoldto', $soldto)->first();
                if($usu){
                    $respuesta = false;
                    $mensaje = 'Lo sentimos, el soldto: '.$soldto.' ya se encuentra registrado';
                    $linea = __LINE__;
                    $mensajeDetalle = 'Porfavor verifique el codigo soldto, tambien le recordamos que puede editar el usuario, Gracias';
                    $log[] = "El soldto ya se encuentra registrado";
                }else{
                    $usun = new usuusuarios;
                    $usun->tpuid         = $tpuid;
                    $usun->perid         = $perid;
                    $usun->zonid         = $zonid;
                    $usun->ususoldto     = $soldto;
                    $usun->usuusuario    = $usuario;
                    $usun->usucorreo     = $correo;
                    $usun->usucontrasena = Hash::make($contrasena);
                    $usun->usutoken      = Str::random(60);
                    if($usun->save()){

                        $data = [
                            'nombre' => $pernom,
                            'usuario' => $usuario,
                            'contrasena' => $contrasena,
                            'correo' => $correo
                        ];

                        // Mail::to($correo)->send(new MailCrearUsuario($data));
                        
                        $respuesta = true;
                        $mensaje = "El usuario se creo satisfactoriamente";
                        $log[] = "El usuario se registro correctamente usuid: ".$usun->usuid;
                        $pkid = $pkid.' || USU-'.$usun->usuid;


                        foreach($sucs as $suc){
                            foreach($suc['sucs'] as $sucSeleccionada){
                                if($sucSeleccionada['seleccionado'] == true){
                                    $ussn = new ussusuariossucursales;
                                    $ussn->usuid = $usun->usuid;
                                    $ussn->sucid = $sucSeleccionada['sucid'];
                                    $ussn->save();
                                }
                            }
                        }

                    }else{
                        $mensaje = "Lo sentimos, ocurrio un error al momento de crear el usuario";
                        $mensajeDetalle = "Porfavor verifique los campos como correo, usuario";
                        $respuesta = false;
                        $log[] = "No se registro el usuario";
                    }

                }
            }

            
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
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
            'Crear nuevo usuario',
            'CREAR',
            '/configuracion/usuarios/crear/usuario', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }

        return $requestsalida;
    }
}
