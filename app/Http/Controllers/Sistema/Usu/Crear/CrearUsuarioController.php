<?php

namespace App\Http\Controllers\Sistema\Usu\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuditoriaController;
use App\perpersonas;
use App\usuusuarios;
use App\ussusuariossucursales;
use App\Mail\MailCrearUsuario;
use App\Mail\MailCrearUsuarioOutlook;
use Illuminate\Support\Facades\Mail;

class CrearUsuarioController extends Controller
{
    public function CrearUsuario(Request $request)
    {

        $respuesta = true;
        $mensaje = "Usuario creado satisfactoriamente";
        $linea   = __LINE__;

        $log = array(
            "perpersonas" => [],
            "usuusuarios" => []
        );

        $mensajeDetalle = '';
        $mensajedev     = null;

        $usutoken   = $request->header('api_token');

        $nombre     = $request['pernom'];
        $apellPat   = $request['perapellpat'];
        $apellMat   = $request['perapellmat'];
        $soldto     = $request['soldto'];
        $correo     = $request['correo'];
        $usuario    = $request['usuario'];
        $pass       = $request['contrasena'];
        $tpuid      = $request['tpuid'];
        $zonid      = $request['zonid'];
        $sucursales = $request['sucursales'];

        $sucs     = $request['sucs'];

        DB::beginTransaction();

        $pkid = 0;

        try{
            $per = perpersonas::where('pernombre', $nombre)
                            ->where('perapellidopaterno', $apellPat)
                            ->where('perapellidomaterno', $apellMat)
                            ->first(['perid']);

            $perid = 0;
            if($per){
                $perid = $per->perid;
            }else{
                $pern = new perpersonas;
                $pern->tdiid = 1;
                $pern->pernombrecompleto = $nombre." ".$apellPat." ".$apellMat;
                $pern->pernombre = $nombre;
                $pern->perapellidopaterno = $apellPat;
                $pern->perapellidomaterno = $apellMat;
                if($pern->save()){
                    $perid = $pern->perid;
                }else{
                    $log['perpersonas'][] = "No se pudo agregar la persona: ".$nombre;
                }
            }


            $pkid = $perid;

            $usuusuario = usuusuarios::where('usuusuario', $usuario)->first(['usuid']);

            if(!$usuusuario){

                $usun = new usuusuarios;
                $usun->tpuid         = $tpuid;
                $usun->perid         = $perid;
                $usun->estid         = 1;
                $usun->zonid         = $zonid;
                $usun->ususoldto     = $soldto;
                $usun->usuusuario    = $usuario;
                $usun->usucorreo     = $correo;
                $usun->usucontrasena = Hash::make($pass);
                $usun->usutoken      = Str::random(60);
                if($usun->save()){
                    $pkid = $pkid." ".$usun->usuid;

                    foreach($sucs as $suc){
                        foreach($suc['sucs'] as $sucSeleccionada){
                            if(isset($sucSeleccionada['seleccionado'])){
                                if($sucSeleccionada['seleccionado'] == true){
                                    $ussn = new ussusuariossucursales;
                                    $ussn->usuid = $usun->usuid;
                                    $ussn->sucid = $sucSeleccionada['sucid'];
                                    $ussn->save();
                                }
                            }
                        }
                    }

                    $data = [
                        "correo"     => $correo,
                        'nombre'     => $nombre." ".$apellPat." ".$apellMat,
                        "usuario"    => $usuario,
                        "contrasena" => $pass
                    ];

                    $pos = strpos($correo, "@gmail.com");
                    
                    if($pos){
                        // Mail::to($correo)->send(new MailCrearUsuario($data));
                    }else{
                        // Mail::to($correo)->send(new MailCrearUsuarioOutlook($data));
                    }

                }else{
                    $log['usuusuarios'][] = "No se pudo agregar el usuario: ".$usuario;
                }

            }else{
                $respuesta = false;
                $mensaje = "El usuario: ".$usuario." ya existe";
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
