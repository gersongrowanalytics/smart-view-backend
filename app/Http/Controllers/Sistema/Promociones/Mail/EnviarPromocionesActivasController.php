<?php

namespace App\Http\Controllers\Sistema\Promociones\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Promociones\MailPromocionesActivas;
use App\uceusuarioscorreosenviados;
use App\dcedestinatarioscorreosenviados;
use App\dmpdetallemecanicaspromocional;
use App\gsugrupossucursales;
use App\sucsucursales;
use App\ussusuariossucursales;
use App\usuusuarios;

class EnviarPromocionesActivasController extends Controller
{
    // ENVIA TODOS LOS CORREOS
    public function EnviarPromocionesActivas2(Request $request)
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

        $activar_envio_correos = false;

        if($activar_envio_correos == true){

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
                            // Mail::to($correo)->cc(['gerson.vilca.growanalytics@gmail.com'])
                            //              ->send(new MailPromocionesActivas($data, $asunto));
                        }else{
                            $gsu = gsugrupossucursales::where('gsuid',$usuario_sucursal['gsuid'])
                                                        ->first();
                            if ($gsu) {
                                $atributo = $gsu->gsunombre;
                                $data = ['txtSucursales' => $atributo, 're_fecha' => $re_fecha, "anio" => $anio, "usuario" => $usuario_sucursal['usuusuario']];
                                $asunto = "Kimberly Clark (PE): PROMOCIONES ".$re_fecha." ".$anio." (".$atributo.")";
                                // Mail::to($correo)->cc(['gerson.vilca.growanalytics@gmail.com'])
                                //              ->send(new MailPromocionesActivas($data, $asunto));
                            }
                        }
                        
                    }
                }else{
                    $respuesta = false;
                    $mensaje = "Lo siento, no se encontraron registros";
                }
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

    public function EnviarPromocionesActivas(Request $request)
    {
        $respuesta = true;
        $mensaje = 'El correo se envio exitosamente';

        $usutoken   = $request->header('api_token');
        $re_sucursales = $request['sucursales'];
        $re_fecha = $request['fecha'];
        $re_reenviado = $request['reenviado'];

        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        $correo = "jeanmarcoe@gmail.com";

        $dataEnviada = [];
        $data_correo = [];
        if (true == true) {
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

            $usuariosCorreo = [];
            
            foreach ($re_sucursales as $sucursal) {

                // Pablo Alocen
                // Rodrigo Balleta
                // Romina Hernandez x
                // Jesús Medina
                // Carlos Galagarza
                // Jose Barbosa
                // Melissa García
                // Jorge Testino
                // Jose Del Busto
                // Axel Rooth
                // Gonzalo Cornejo
                // Javier Ley Almanza x


                $uss = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                            ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                            ->join('gsugrupossucursales as gsu', 'gsu.gsuid', 'suc.gsuid')
                                            ->where('usu.estid', 1)
                                            ->where('suc.sucnombre','like', '%'.$sucursal.'%')
                                            ->where('usu.usuusuario', 'not like', '%grow-analytics%')
                                            ->where('usu.usuusuario', 'not like', '%mzorrilla%')
                                            ->where('usu.usuusuario', 'not like', '%pablo.alocen%')
                                            ->where('usu.usuusuario', 'not like', '%rodrigo.balletta%')
                                            ->where('usu.usuusuario', 'not like', '%jesus.medina%')
                                            ->where('usu.usuusuario', 'not like', '%jorge.j.testino%')
                                            ->where('usu.usuusuario', 'not like', '%Axel.Rooth%')
                                            ->where('usu.usuusuario', 'not like', '%gonzalo.p.cornejo%')
                                            ->where('usu.usuusuario', 'not like', '%carlos.galagarza%')
                                            ->where('usu.usuusuario', 'not like', '%Jose.m.barbosa%')
                                            ->where('usu.usuusuario', 'not like', '%jose.delbusto%')
                                            ->where('usu.usuusuario', 'not like', '%melissa.garcia%')
                                            ->where('usu.usuusuario', 'not like', '%pruebagrow%')
                                            ->where('usu.usuusuario', 'not like', '%joaquin.J.Cuervas%')
                                            ->where('usu.usuusuario', 'not like', '%carmenvanessa.malaga%')
                                            ->where('usu.usuusuario', 'not like', '%luis.e.acevedo%')
                                            ->whereNotNull('usu.usuusuario')
                                            ->orderBy('ussusuariossucursales.usuid', 'DESC')
                                            ->get([
                                                'usu.usuid',
                                                'suc.gsuid',
                                                'suc.sucnombre', 
                                                'ussusuariossucursales.usuid',
                                                'usu.usuusuario',
                                                'gsu.gsunombre'
                                            ]);

                //OBTENER LOS REGISTROS DE USUARIOS PARA ENVIAR CORREO SEGUN SUCURSAL
                foreach ($uss as $key => $usuario) {
                    
                    $ucen = new uceusuarioscorreosenviados;
                    $ucen->usuid          = $usuario['usuid'];
                    $ucen->ucetipo        = "Promociones Activas";
                    $ucen->uceasunto      = "Promociones Activas";
                    $ucen->ucesucursales  = $usuario['sucnombre'];
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
                            if ($usuario['gsuid'] == 1) {
                                $suc = $usuario['sucnombre'];
                            }else{
                                $suc = $usuario['gsunombre'];
                            }
                            // $anio = date("Y");
                            $anio = "2023";

                            $data = [
                                "txtSucursales" => $suc, 
                                "re_fecha" => $re_fecha, 
                                "anio" => $anio, 
                                "usuario" => $usuario['usuusuario']
                            ];
                            $asunto = "Kimberly Clark (PE): PROMOCIONES ".$re_fecha." ".$anio." (".$suc.")";

                            $anadirCorreo = true;

                            foreach ($usuariosCorreo as $usuarioCorreo) {
                                if ($usuario['usuusuario'] == $usuarioCorreo['usuario']) {

                                    if($usuario['gsunombre'] == 'Clientes'){
                                        $anadirCorreo = false;
                                    }else if($usuarioCorreo['gsunombre'] == $usuario['gsunombre']){
                                        $anadirCorreo = false;
                                    }
                                }
                            }
                            
                            if($anadirCorreo == true){
                                $usuariosCorreo[] = array(
                                    "usuario"   => $usuario['usuusuario'],
                                    "gsunombre" => $usuario['gsunombre'],
                                    "sucnombre" => $usuario['sucnombre'],
                                    "data"      => $data,
                                    "asunto"    => $asunto
                                );
                            }                            
                        }
                    }
                    
                }
                
            }
            if (sizeof($usuariosCorreo) > 0) {

                foreach ($usuariosCorreo as $key => $usuarioCorreo) {
                    $suc_correos[] = $usuarioCorreo['data']['txtSucursales'];
                }

                $suc_unicos = array_values(array_unique($suc_correos));

                foreach ($suc_unicos as $key => $suc_unico) {
                    $usuarios_correo = [];
                    $asunto_correo = "";
                    $data_correo = [];
                    foreach ($usuariosCorreo as $key => $usuarioCorreo) {
                        if ($suc_unico ==  $usuarioCorreo['data']['txtSucursales']) {
                            $usuarios_correo[] = $usuarioCorreo['usuario'];
                            $asunto_correo = $usuarioCorreo['asunto'];
                            // $asunto_correo = $asunto_correo."/".$usuarioCorreo['usuario'];
                            $data_correo = $usuarioCorreo['data'];
                        }
                    }

                    $dataEnviada[] = $usuarios_correo;
                    
                    // REAL A UTILIZAR
                    // Mail::to($usuarios_correo)->cc(['gerson.vilca@grow-analytics.com.pe'])
                    //                             ->send(new MailPromocionesActivas($data_correo, $asunto_correo));
                    // Mail::to($usuarios_correo)->cc(['0540Peru.salescontrolling@kcc.com', 'Cuidatunegocio.KC@kcc.com', 'gerson.vilca@grow-analytics.com.pe', 'miguel.caballero@grow-analytics.com.pe', 'eunice.calle@grow-analytics.com.pe'])
                    //                             ->send(new MailPromocionesActivas($data_correo, $asunto_correo));

                    // ENVIAR RECORDATORIO
                    // Mail::to($usuarios_correo)->cc(['eunice.calle@grow-analytics.com.pe'])
                    //                             ->send(new MailPromocionesActivas($data_correo, $asunto_correo));

                    // RECORDATORIO SIN EUNICE
                    // Mail::to($usuarios_correo)->send(new MailPromocionesActivas($data_correo, $asunto_correo));

                    

                    // Mail::to($correo)->send(new MailPromocionesActivas($data_correo, $asunto_correo));
                }

                // // foreach ($usuariosCorreo as $key => $usuarioCorreo) {
                // //     // Mail::to($usuarioCorreo['usuario'])->cc(['0540Peru.salescontrolling@kcc.com', 'Cuidatunegocio.KC@kcc.com', 'gerson.vilca@grow-analytics.com.pe', 'miguel.caballero@grow-analytics.com.pe', 'director.creativo@grow-analytics.com.pe'])
                // //     //                  ->send(new MailPromocionesActivas($usuarioCorreo['data'], $usuarioCorreo['asunto']));
                // // }
            }


        }else{
            $respuesta = false;
            $mensaje = "Lo siento, no existe datos del usuario";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            // "datos"     => $usuariosCorreo,
            // "datos" => $dataEnviada,
            "data_correo" => $data_correo,
            // "asunto" => $asunto_correo

        ]);

        return $requestsalida;
    }
}


// MARZO
// CENTRO

// "LOGISTIC ONE",
// "SERV. VIRGEN",
// "ROMA AYACUCHO",
// "ROMA",
// "CONSORCIO ARBECO",
// "DISTR. ARBECO",
// "GLOSER",
// "SANTA ROSA",
// "BRENMA",
// "GABRIELA INDI",
// "GRUPO COMERCIAL ARELLANO",
// "5M DISTRIBUCIONES SRL",
// "DISTR. GIANCARLO",
// "DISTR. SEÑOR DE POTOSI",
// "DISTR. GABRIEL ARCANGEL",
// "DISALTI",
// "PAPA DE AMERICA SA",
// "DISTR. E INVERSIONES ANDERSO",
// "DIMEXA",
// "JN SUR"

// // PERAMAS

// "DESPENSA CHICLAYO",
// "DESPENSA TRUJILLO",
// "ALMACENES DE LA SELVA JAEN",
// "ALMACENES DE LA SELVA PUCALLPA",
// "ALMACENES DE LA SELVA TARAPOTO",
// "RACSER",
// "DESPENSA DE LA SELVA",
// "DESPENSA CAJAMARCA",
// "DESPENSA CHIMBOTE",
// "CHALI",
// "DISTR. SILVIA",
// "D Y R",
// "DISTR. PATITA",
// "GRUPO RACCE",
// "PUNTO BLANCO"

// // LIMA

// "GUMI",
// "VIJISA",
// "REDIJISA",
// "JIRUSA",
// "JINORSA",
// "ABAFLOR",
// "RODAMEOS",
// "INV. ZISCO",
// "REP. JHOSEP",
// "ZV INVERSIONES GENERALES",
// "ROXAL",
// "MA DI",
// "M&S INV. TRADING",
// "SUPERPLAZA",
// "CODIJISA",
// "CORP. CODIFER",
// "ECONOMYSA",
// "TUIN",
// "DIST. JOMER",
// "AUREN",
// "DEHOCA",
// "ORIUNDA SAC",
// "CORP. VEGA",
// "SAN RAFAELITO",
// "SAGRA DISTRIBUCION",
// "TERRANORTE",
// "URIAFER",
// "GUMI CHIMBOTE",
// "JIMENEZ & AVENDAÑO",
// "MOLI",
// "TOTAL CALIDAD AMERICA",
// "CORP. REHB",
// "UNIÓN DE CERVECERÍAS PERUANAS BACKU"