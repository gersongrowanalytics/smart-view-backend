<?php

namespace App\Http\Controllers\Sistema\Promociones\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Promociones\MailPromocionesActivas;
use App\uceusuarioscorreosenviados;
use App\dcedestinatarioscorreosenviados;
use App\gsugrupossucursales;
use App\sucsucursales;
use App\ussusuariossucursales;
use App\usuusuarios;

class EnviarPromocionesActivasController extends Controller
{
    // ENVIA TODOS LOS CORREOS
    public function EnviarPromocionesActivas(Request $request)
    {
        $respuesta = true;
        $mensaje = 'El correo se envio exitosamente';

        $usutoken   = $request->header('api_token');
        $re_sucursales = $request['sucursales'];
        $re_fecha = $request['fecha'];
        $re_reenviado = $request['reenviado'];

        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        // $correo = "euni_tkm@hotmail.com";
        // $correo = "eunicecallecahuana@gmail.com";
        // $correo = "gerson.vilca.growanalytics@gmail.com";

        // $correo = "gerson.vilca@grow-analytics.com.pe";
        $correo = "director.creativo@grow-analytics.com.pe";
        // $correo = "miguel.caballero@grow-analytics.com.pe";
        // $correo = "mzorrilla@kcc.com";

        if($usu){

            // OBTENER FECHAS

            date_default_timezone_set("America/Lima");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $fecha = date('Y-m-d');
            $hora  = date('H:i:s');

            $anioActualizacion = date("Y", strtotime($fecha));
            $mesActualizacion = $meses[date('n', strtotime($fecha))-1];
            $diaActualizacion = date("j", strtotime($fecha));

            $horaActualizacion = date("H", strtotime($hora));
            $minutoActualizacion = date("i", strtotime($hora));

            $uss = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                        ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                        ->where('usu.estid', 1)
                                        ->where('usu.usuusuario', 'not like', '%grow-analytics%')
                                        ->whereNotNull('usu.usucorreo')
                                        ->orderBy('ussusuariossucursales.usuid', 'DESC')
                                        ->distinct('ussusuariossucursales.usuid')
                                        // ->limit(1)
                                        ->get([
                                            'suc.gsuid',
                                            'suc.sucnombre', 
                                            'ussusuariossucursales.usuid',
                                            'usu.usucorreo', 
                                            'usu.usuusuario'
                                        ]);

            $uss_sinDuplicados = $uss->unique('usuid');
            
            if ($uss_sinDuplicados) {
                foreach ($uss_sinDuplicados as $posicion => $usuario_sucursal) {
                    $atributo = "";

                    $sucursales = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                        ->where('ussusuariossucursales.usuid',$usuario_sucursal['usuid'])
                                                        ->get(['suc.sucnombre']);
                                                    
                    $ucen = new uceusuarioscorreosenviados;
                    $ucen->usuid = $usuario_sucursal['usuid'];
                    $ucen->ucetipo        = "Promociones Activas";
                    // $ucen->ucenombreexcel = ;
                    $ucen->uceasunto      = "Promociones Activas";
                    // $ucen->ucecontenido   = ;
                    // $ucen->ucecolumnas    = ;
                    $ucen->ucesucursales  = json_encode($re_sucursales);
                    $ucen->uceanio        = $anioActualizacion;
                    $ucen->ucemes         = $mesActualizacion;
                    $ucen->ucedia         = $diaActualizacion;
                    $ucen->ucehora        = $horaActualizacion.":".$minutoActualizacion;
                    $ucen->ucefecha       = $fecha;
                    if($ucen->save()){
                        $dcen = new dcedestinatarioscorreosenviados;
                        $dcen->uceid = $ucen->uceid;
                        $dcen->dcedestinatario = $correo;

                        if(isset($re_reenviado)){
                            if($re_reenviado == true){
                                $dcen->dceestado = 'R';
                            }else{
                                $dcen->dceestado = 'E';
                            }
                        }else{
                            $dcen->dceestado = 'E';
                        }
                        $dcen->save();
                    }

                    $txtSucursales = "";
                    $nombre = "";
            
                    $arr_nombreGrupos = [];
                    $arr_listaNombreGrupos = array();
                    $nombreGrupos = "";
                    
                    foreach($sucursales as $posicionSucursal => $sucursal){

                        // if($posicionSucursal == 0){
                        //     $txtSucursales = $sucursal;
                        //     $gsu = sucsucursales::where('sucnombre', 'LIKE', "%".$sucursal."%")
                        //                     ->join('gsugrupossucursales as gsu', 'gsu.gsuid', 'sucsucursales.gsuid')
                        //                     ->first(['gsu.gsunombre']);
            
                        //     if ($gsu) {
                        //         $nombre = $gsu->nombre;
                        //         if ($gsu->nombre == 'Clientes') {
                        //             $nombre = $sucursal;
                        //         }
                        //     }else{
                        //         $nombre = $sucursal;
                        //     }
            
                        // }else{
                        //     $txtSucursales = $txtSucursales.", ".$sucursal;
                        // }
            
            
                        
                        $gsu = sucsucursales::where('sucnombre', 'LIKE', "%".$sucursal['sucnombre']."%")
                                        ->join('gsugrupossucursales as gsu', 'gsu.gsuid', 'sucsucursales.gsuid')
                                        ->first(['gsu.gsunombre']);
                                        
                        $grupoSeleccionado = "";
                        $ordenSeleccionado = 9;
            
                        if ($gsu) {
                            
                            $grupoSeleccionado = $gsu->gsunombre;
                            
                            if ($gsu->gsunombre == 'Clientes') {
                                $grupoSeleccionado = $sucursal['sucnombre'];
                            }else{
                                $ordenSeleccionado = 1;
                            }
            
                        }else{
                            
                            $grupoSeleccionado = $sucursal['sucnombre'];
                        }
            
                        $encontroGrupo = false;
            
                        foreach($arr_nombreGrupos as $arr_nombreGrupo){
                            if($arr_nombreGrupo == $grupoSeleccionado){
                                $encontroGrupo = true;
                            }
                        }
            
                        if($encontroGrupo == false){
                            $arr_nombreGrupos[] = $grupoSeleccionado;
                            $arr_listaNombreGrupos[] = array(
                                "grupo" => $grupoSeleccionado,
                                "orden" => $ordenSeleccionado
                            );
                        }
                        
                    }
            
                    usort(
                        $arr_listaNombreGrupos,
                        function ($a, $b)  {
                            if ($a['orden'] < $b['orden']) {
                                return -1;
                            } else if ($a['orden'] > $b['orden']) {
                                return 1;
                            } else {
                                return 0;
                            }
                        }
                    );
            
                    foreach($arr_listaNombreGrupos as $posArr_NombreGrupos => $arr_nombreGrupo){
                        if($posArr_NombreGrupos == 0){
                            $nombreGrupos = $arr_nombreGrupo['grupo'];
                        }else{
                            $nombreGrupos = $nombreGrupos.", ".$arr_nombreGrupo['grupo'];
                        }
                    }
            
                    // foreach($arr_nombreGrupos as $posArr_NombreGrupos => $arr_nombreGrupo){
                    //     if($posArr_NombreGrupos == 0){
                    //         $nombreGrupos = $arr_nombreGrupo;
                    //     }else{
                    //         $nombreGrupos = $nombreGrupos.", ".$arr_nombreGrupo;
                    //     }
                    // }
                    
            
                    $anio = date("Y");
            
                    // $data = ['txtSucursales' => $txtSucursales, 're_fecha' => $re_fecha];
                    // $data = ['txtSucursales' => $nombre, 're_fecha' => $re_fecha];
                    // $data = ['txtSucursales' => $nombreGrupos, 're_fecha' => $re_fecha, "anio" => $anio, "correo" => $usu->usucorreo];
            
                    // $primerGrupo = explode(',', $nombreGrupos);

                    // $asunto = "Kimberly Clark (PE): PROMOCIONES ".$re_fecha." ".$anio." (".$primerGrupo[0].")";


                    if ($usuario_sucursal['gsuid'] == 1) {
                        $atributo = $usuario_sucursal['sucnombre'];
                        $data = ['txtSucursales' => $atributo, 're_fecha' => $re_fecha, "anio" => $anio, "usuario" => $usuario_sucursal['usuusuario']];
                        $asunto = "Kimberly Clark (PE): PROMOCIONES ".$re_fecha." ".$anio." (".$atributo.")";
                        Mail::to($correo)->cc(['gerson.vilca.growanalytics@gmail.com'])
                                     ->send(new MailPromocionesActivas($data, $asunto));
                    }else{
                        $gsu = gsugrupossucursales::where('gsuid',$usuario_sucursal['gsuid'])
                                                    ->first();
                        if ($gsu) {
                            $atributo = $gsu->gsunombre;
                            $data = ['txtSucursales' => $atributo, 're_fecha' => $re_fecha, "anio" => $anio, "usuario" => $usuario_sucursal['usuusuario']];
                            $asunto = "Kimberly Clark (PE): PROMOCIONES ".$re_fecha." ".$anio." (".$atributo.")";
                            Mail::to($correo)->cc(['gerson.vilca.growanalytics@gmail.com'])
                                         ->send(new MailPromocionesActivas($data, $asunto));
                        }
                    }
                    
                }
            }else{
                $respuesta = false;
                $mensaje = "Lo siento, no se encontraron registros";
            }
        }

       
        
        // $atributo = "";
        // $uss = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
        //                                 ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
        //                                 // ->where('ussusuariossucursales.usuid', $usu->usuid)
        //                                 ->where('usu.estid', 2)
        //                                 ->distinct('ussusuariossucursales.usuid')
        //                                 ->get(['suc.gsuid','suc.sucnombre', 'ussusuariossucursales.usuid']);
        // if ($uss) {
        //     foreach ($uss as $posicion => $usuario_sucursal) {
        //         if ($usuario_sucursal['gsuid'] == 'Clientes') {
        //             $atributo = $usuario_sucursal['sucnombre'];
        //             $data = ['txtSucursales' => $nombreGrupos, 're_fecha' => $re_fecha, "anio" => $anio, "correo" => $usu->usucorreo, "atributo" => $atributo];
        //         }else{
        //             $gsu = gsugrupossucursales::where('gsuid',$usuario_sucursal['gsuid'])
        //                                         ->first();
        //             if ($gsu) {
        //                 $atributo = $gsu->gsunombre;
        //                 $data = ['txtSucursales' => $nombreGrupos, 're_fecha' => $re_fecha, "anio" => $anio, "correo" => $usu->usucorreo, "atributo" => $atributo];
        //             }
        //         }
                
        //         // Mail::to($correo)->cc(['gerson.vilca@grow-analytics.com.pe'])
        //         Mail::to($correo)->send(new MailPromocionesActivas($data, $asunto));
        //     }
        // }else{
        //     $respuesta = false;
        //     $mensaje = "NO SE PUDO ENVIAR EL CORREO";
        // }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
        ]);

        return $requestsalida;
    }

    public function EnviarPromocionesActivas2(Request $request)
    {
        $respuesta = true;
        $mensaje = 'El correo se envio exitosamente';

        $usutoken   = $request->header('api_token');
        $re_sucursales = $request['re_sucursales'];
        $re_fecha = $request['re_fecha'];
        $re_reenviado = $request['re_reenviado'];

        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        $correo = "jeanmarcoe@gmail.com";

        if ($usu) {
            //OBTENER LA FECHA
            date_default_timezone_set("America/Lima");
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $fecha = date('Y-m-d');
            $hora  = date('H:i:s');

            $anioActualizacion = date("Y", strtotime($fecha));
            $mesActualizacion = $meses[date('n', strtotime($fecha))-1];
            $diaActualizacion = date("j", strtotime($fecha));

            $horaActualizacion = date("H", strtotime($hora));
            $minutoActualizacion = date("i", strtotime($hora));

            $usuarioCorreo = [];
            foreach ($re_sucursales as $sucursal) {
                $uss = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                            ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                            ->where('usu.estid', 1)
                                            ->where('suc.sucnombre','like', '%'.$sucursal.'%')
                                            ->where('usu.usuusuario', 'not like', '%grow-analytics%')
                                            ->whereNotNull('usu.usuusuario')
                                            ->orderBy('ussusuariossucursales.usuid', 'DESC')
                                            ->first([
                                                'suc.gsuid',
                                                'suc.sucnombre', 
                                                'ussusuariossucursales.usuid',
                                                'usu.usuusuario'
                                            ]);

                //OBTENER LOS REGISTROS DE USUARIOS PARA ENVIAR CORREO SEGUN SUCURSAL
            
                if ($uss) {
                    // $usuarioCorreo[] = $uss->usuusuario;

                    $ucen = new uceusuarioscorreosenviados;
                    $ucen->usuid          = $uss->usuid;
                    $ucen->ucetipo        = "Promociones Activas";
                    $ucen->uceasunto      = "Promociones Activas";
                    $ucen->ucesucursales  = $uss->sucnombre;
                    $ucen->uceanio        = $anioActualizacion;
                    $ucen->ucemes         = $mesActualizacion;
                    $ucen->ucedia         = $diaActualizacion;
                    $ucen->ucehora        = $horaActualizacion.":".$minutoActualizacion;
                    $ucen->ucefecha       = $fecha;
                    if($ucen->save()){

                        $dcen = new dcedestinatarioscorreosenviados;
                        $dcen->uceid = $ucen->uceid;
                        $dcen->dcedestinatario = $correo;

                        if(isset($re_reenviado)){
                            if($re_reenviado == true){
                                $dcen->dceestado = 'R';
                            }else{
                                $dcen->dceestado = 'E';
                            }
                        }else{
                            $dcen->dceestado = 'E';
                        }
                        
                        if ($dcen->save()) {
                            if ($uss->gsuid == 1) {
                                $suc = $uss->sucnombre;
                            }else{
                                $gsu = gsugrupossucursales::where('gsuid',$uss->gsuid)
                                                            ->first();
                                if ($gsu) {
                                    $suc = $gsu->gsunombre;
                                }
                            }
                            $anio = date("Y");

                            $data = ['txtSucursales' => $suc, 're_fecha' => $re_fecha, "anio" => $anio, "usuario" => $uss->usuusuario];
                            $asunto = "Kimberly Clark (PE): PROMOCIONES ".$re_fecha." ".$anio." (".$suc.")";
                            if (in_array($uss->usuusuario,$usuarioCorreo)) {
                               
                            }else{
                                Mail::to($correo)->cc(['gerson.vilca.growanalytics@gmail.com'])
                                                 ->send(new MailPromocionesActivas($data, $asunto));
                                $usuarioCorreo[] = $uss->usuusuario;
                            }
                            
                        }
                    }
                    
                }
            }
        }else{
            $respuesta = false;
            $mensaje = "Lo siento, no existe datos del usuario";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
        ]);

        return $requestsalida;
    }
}


