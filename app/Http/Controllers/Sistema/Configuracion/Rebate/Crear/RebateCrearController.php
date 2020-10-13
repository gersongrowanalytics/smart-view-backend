<?php

namespace App\Http\Controllers\Sistema\Configuracion\Rebate\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\rtprebatetipospromociones;
use App\fecfechas;
use App\trrtiposrebatesrebates;
use App\tsutipospromocionessucursales;
use App\sucsucursales;

class RebateCrearController extends Controller
{
    public function CrearRebate(Request $request )
    {
        $usutoken   = $request->header('api_token');

        $fecha            = $request['fecha'];
        $tipoPromocion    = $request['tipoPromocion'];
        $catsid           = $request['catsid'];  //nuevo
        $treid            = $request['treid'];  //nuevo
        $porcentajeDesde  = $request['porcentajeDesde'];
        $porcentajeHasta  = $request['porcentajeHasta'];
        $porcentajeRebate = $request['porcentajeRebate'];

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log            = [];
        $pkid           = 0;

        try{
            $fecha = new \DateTime(date("Y-m-d", strtotime($fecha)));

            $fecfecha = fecfechas::where('fecfecha', $fecha)->first(['fecid']);
            $fecid = 0;
            if($fecfecha){
                $fecid = $fecfecha->fecid;
                $log[] = "Existe la fecha";
            }else{
                $log[] = "No existe la fecha";
                $nuevafecha = new fecfechas;
                $nuevafecha->fecfecha = $fecha;
                $nuevafecha->fecdia   = '';
                $nuevafecha->fecmes   = '';
                $nuevafecha->fecano   = '';
                if($nuevafecha->save()){
                    $fecid = $nuevafecha->fecid;

                    $pkid = "FEC-".$fecid." ";
                    $log[] = "Se agrego la fecha";
                }else{
                    $pkid = "";
                    $log[] = "No se pudo agregar la fecha";
                }
            }

            $rtp = rtprebatetipospromociones::where('fecid', $fecid)
                                            ->where('tprid', $tipoPromocion)
                                            ->where('rtpporcentajedesde', $porcentajeDesde)
                                            ->where('rtpporcentajehasta', $porcentajeHasta)
                                            ->where('rtpporcentajerebate', $porcentajeRebate)
                                            ->first(['rtpid']);

            if($rtp){
                
                for($contadorCats = 0; $contadorCats < sizeof($catsid); $contadorCats++  ){

                    $trr = trrtiposrebatesrebates::where('treid', $treid)
                                            ->where('rtpid', $rtp->rtpid)
                                            ->where('catid', $catsid[$contadorCats])
                                            ->first(['trrid']);

                    if($trr){
                        $respuesta      = true;
                        $mensaje        = 'El rebate ya existe';
                        $datos          = $trrn;
                        $linea          = __LINE__;
                        $mensajeDetalle = 'El rebate junto con el grupo asignado y tipo de promoción ya existen';
                        $log[]          = "No se creo nada nuevo, porque ya existe todo lo enviado";
                    }else{
                        $trrn = new trrtiposrebatesrebates;
                        $trrn->treid = $treid;
                        $trrn->rtpid = $rtp->rtpid;
                        $trrn->catid = $catsid[$contadorCats];
                        if($trrn->save()){
                            $respuesta      = true;
                            $mensaje        = 'El rebate se registro correctamente';
                            $datos          = $trrn;
                            $linea          = __LINE__;
                            $mensajeDetalle = 'Nuevo rebate agregado';
                            $log[]          = "Se agrego el trr";
                            $pkid           = $pkid."TRR-".$trrn->trrid;
                        }else{
                            $respuesta      = false;
                            $mensaje        = 'Ocurrio un error al momento de asignar la categoria y el grupo al rebate';
                            $datos          = [];
                            $linea          = __LINE__;
                            $mensajeDetalle = 'El trr no se agrego';
                            $log[]          = "No se pudo agregar el registro trr";
                        }
                    }
                }

            }else{
                $rtpn = new rtprebatetipospromociones;
                $rtpn->fecid               = $fecid;
                $rtpn->tprid               = $tipoPromocion;
                $rtpn->rtpporcentajedesde  = $porcentajeDesde;
                $rtpn->rtpporcentajehasta  = $porcentajeHasta;
                $rtpn->rtpporcentajerebate = $porcentajeRebate;
                if($rtpn->save()){

                    for($contadorCats = 0; $contadorCats < sizeof($catsid); $contadorCats++  ){
                        $trrn = new trrtiposrebatesrebates;
                        $trrn->treid = $treid;
                        $trrn->rtpid = $rtpn->rtpid;
                        $trrn->catid = $catsid[$contadorCats];
                        if($trrn->save()){
                            
                            $respuesta      = true;
                            $mensaje        = 'El rebate se registro correctamente';
                            $datos          = $rtpn;
                            $linea          = __LINE__;
                            $mensajeDetalle = 'Nuevo rebate agregado';
                            $pkid           = $pkid."RTP-".$rtpn->rtpid;
                            $log[]          = "Se agrego el rebate";

                        }else{
                            $respuesta      = false;
                            $mensaje        = 'Ocurrio un error al momento de asignar la categoria y el grupo al rebate';
                            $datos          = [];
                            $linea          = __LINE__;
                            $mensajeDetalle = 'El trr no se agrego';
                            $log[]          = "No se pudo agregar el registro trr";
                        }
                    }

                }else{
                    $respuesta      = false;
                    $mensaje        = 'Ocurrio un error al momento de agregar el rebate';
                    $datos          = [];
                    $linea          = __LINE__;
                    $mensajeDetalle = 'El rebate no se agrego';
                    $log[]          = "No se pudo agregar el rebate";
                }
            }


            // ACTUALIZAR EL TOTAL DE REBATE EN LAS SUCURSALES

            $sucs = sucsucursales::get(['sucid']);

            foreach($sucs as $suc){
                $tsu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                ->where('sucid', $suc->$suc)
                                                ->where('tprid', $tipoPromocion)
                                                ->first(['tsuid', 'tsuvalorizadoreal', 'tsuvalorizadoobjetivo', 'treid']);

                if($tsu){
                    
                }else{
                    
                }
            }   
            

            

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[]      = "ERROR DE SERVIDOR: ".$mensajedev;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
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
            'Agregar un nuevo registro de rebate',
            'AGREGAR',
            '/configuracion/rebate/crearRebate', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
