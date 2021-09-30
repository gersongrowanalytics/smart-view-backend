<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\csccanalessucursalescategorias;
use App\cspcanalessucursalespromociones;
use App\prppromocionesproductos;
use App\prbpromocionesbonificaciones;
use App\fecfechas;
use App\sucsucursales;
use Illuminate\Support\Facades\DB;

class PromocionesMostrarController extends Controller
{
    /**
     * [{"canal" : 1, "promociones": [{"prmid": 1, "productos" : [], "productosbonificados": []}]}]
     */
    public function mostrarPromociones(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $scaid      = $request['scaid']; //id de la cateogira de una sucursal 
        
        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{
            $csccanalessucursalescategoriasa = array();
            $csccanalessucursalescategorias = csccanalessucursalescategorias::join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                                                                ->join('cancanales as can', 'can.canid', 'csccanalessucursalescategorias.canid')
                                                                                ->where('csccanalessucursalescategorias.scaid', $scaid)
                                                                                ->groupBy('csccanalessucursalescategorias.cscid')
                                                                                ->orderBy('cont', 'DESC')
                                                                                ->select(
                                                                                    'csccanalessucursalescategorias.cscid',
                                                                                    'csccanalessucursalescategorias.scaid',
                                                                                    'can.canid',
                                                                                    'can.cannombre',
                                                                                    DB::raw('count(cspid) as cont')
                                                                                )
                                                                                ->get();
            
//             SELECT csc.cscid, count(cspid) as cont from csccanalessucursalescategorias as csc INNER JOIN cspcanalessucursalespromociones as csp ON csp.cscid = csc.cscid where csc.scaid =
// 13716 group by csc.cscid order by cont desc;
                                                    
            if(sizeof($csccanalessucursalescategorias) > 0){
                
                foreach($csccanalessucursalescategorias as $posicion => $csccanalesucursalcategoria){

                    $csccanalessucursalescategorias[$posicion]['productos'] = [];
                    
                    $cspcanalessucursalespromociones = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                                                        ->join('tprtipospromociones as tpr', 'tpr.tprid', 'prm.tprid')
                                                                                        ->where('cscid', $csccanalesucursalcategoria->cscid)
                                                                                        ->where('cspcanalessucursalespromociones.cspestado', 1)
                                                                                        ->get([
                                                                                            'cspcanalessucursalespromociones.cspid',
                                                                                            'prm.prmid',
                                                                                            'prm.prmcodigo',
                                                                                            'cspcanalessucursalespromociones.cspvalorizado',
                                                                                            'cspcanalessucursalespromociones.cspplanchas',
                                                                                            'cspcanalessucursalespromociones.cspcompletado',
                                                                                            'cspcanalessucursalespromociones.cspcantidadcombo',
                                                                                            'prm.prmmecanica',
                                                                                            'cspcanalessucursalespromociones.cspcantidadplancha',
                                                                                            'cspcanalessucursalespromociones.csptotalcombo',
                                                                                            'cspcanalessucursalespromociones.csptotalplancha',
                                                                                            'cspcanalessucursalespromociones.csptotal',
                                                                                            'cspcanalessucursalespromociones.cspgratis',
                                                                                            'prm.prmaccion',
                                                                                            'tpr.tprnombre',
                                                                                            'cspnuevo'
                                                                                        ]);
                    $numeroPromocionesTerminadas = 0;

                    if(sizeof($cspcanalessucursalespromociones) > 0){
                        foreach($cspcanalessucursalespromociones as $posicionPromociones => $cspcanalesucursalpromocion){
                            if($cspcanalesucursalpromocion->cspcompletado == true){
                                $numeroPromocionesTerminadas = $numeroPromocionesTerminadas+1;
                            }
                            $prppromocionesproductos = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
                                                                                ->where('prppromocionesproductos.prmid', $cspcanalesucursalpromocion->prmid )
                                                                                ->get([
                                                                                    'prppromocionesproductos.prpid',
                                                                                    'pro.proid',
                                                                                    'pro.prosku',
                                                                                    'pro.pronombre',
                                                                                    'pro.proimagen',
                                                                                    'prpproductoppt',
                                                                                    'prpcomprappt',
                                                                                    'prpimagen',
                                                                                    'prpoimagen',
                                                                                ]);

                            if(sizeof($prppromocionesproductos) > 0){
                                $cspcanalessucursalespromociones[$posicionPromociones]['productos'] = $prppromocionesproductos;
                                
                                $csccanalessucursalescategorias[$posicion]['productos'][] = $prppromocionesproductos[0]['prosku'];

                            }else{
                                $cspcanalessucursalespromociones[$posicionPromociones]['productos'] = [];
                            }

    
                            $prbpromocionesbonificaciones = prbpromocionesbonificaciones::join('proproductos as pro', 'pro.proid', 'prbpromocionesbonificaciones.proid')
                                                                                        ->where('prbpromocionesbonificaciones.prmid', $cspcanalesucursalpromocion->prmid )
                                                                                        ->get([
                                                                                            'prbpromocionesbonificaciones.prbid',
                                                                                            'pro.proid',
                                                                                            'pro.prosku',
                                                                                            'pro.pronombre',
                                                                                            'pro.proimagen',
                                                                                            'prbproductoppt',
                                                                                            'prbcomprappt',
                                                                                            'prbimagen',
                                                                                            'prboimagen',
                                                                                        ]);
                            
                            if(sizeof($prbpromocionesbonificaciones) > 0){
                                $cspcanalessucursalespromociones[$posicionPromociones]['productosbonificados'] = $prbpromocionesbonificaciones;
                            }else{
                                $cspcanalessucursalespromociones[$posicionPromociones]['productosbonificados'] = [];
                            }
                        }
                    }else{
                        $cspcanalessucursalespromociones = [];
                    }
                    
                    $csccanalessucursalescategorias[$posicion]['porcentaje'] = (sizeof($cspcanalessucursalespromociones)*$numeroPromocionesTerminadas)/100;
                    $csccanalessucursalescategorias[$posicion]['promociones'] = $cspcanalessucursalespromociones;
                }

                $linea          = __LINE__;
                $respuesta      = true;
                $datos          = $csccanalessucursalescategorias;
                $mensaje        = 'Las promociones se cargaron satisfactoriamente';

            }else{
                $respuesta      = false;
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos, no se contramos canales registradas a este filtro.';
                $mensajeDetalle = sizeof($csccanalessucursalescategorias).' registros encontrados.';
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            'csccanalessucursalescategoriasa'     => $csccanalessucursalescategoriasa,
        ]);
        
        return $requestsalida;
    }

    public function mostrarPromocionesXZona(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $catid      = $request['catid'];
        $zonid      = $request['zonid'];
        $gsuid      = $request['gsuid'];
        $casid      = $request['casid'];

        $dia        = "01";
        $mes        = $request['mes'];
        $ano        = $request['ano'];

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;


        $sucs = sucsucursales::where(function ($query) use($zonid, $gsuid, $casid) {
                                    if( $zonid != 0 ){
                                        $query->where('zonid', $zonid);
                                    }

                                    if($gsuid != 0){
                                        $query->where('gsuid', $gsuid);
                                    }
                                    
                                    if($casid != 0){
                                        $query->where('casid', $casid);
                                    }
                                })
                                ->get();


        $fec = fecfechas::where('fecdia', $dia)
                        ->where('fecmes', $mes)
                        ->where('fecano', $ano)
                        ->first(['fecid']);

        $cscs = csccanalessucursalescategorias::join('cancanales as can', 'can.canid', 'csccanalessucursalescategorias.canid')
                                            ->join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                            ->join('scasucursalescategorias as sca', 'sca.scaid', 'csccanalessucursalescategorias.scaid')
                                            ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                            ->where('sca.catid', $catid)
                                            ->where('csccanalessucursalescategorias.fecid', $fec->fecid)
                                            ->where(function ($query) use($zonid, $gsuid, $casid) {
                                                if( $zonid != 0 ){
                                                    $query->where('suc.zonid', $zonid);
                                                }
            
                                                if($gsuid != 0){
                                                    $query->where('suc.gsuid', $gsuid);
                                                }
                                                
                                                if($casid != 0){
                                                    $query->where('suc.casid', $casid);
                                                }
                                            })
                                            ->where('csp.cspcantidadplancha', '!=', "0")
                                            ->where('csp.cspestado', 1)
                                            ->distinct('can.canid')
                                            ->get([
                                                // 'csccanalessucursalescategorias.cscid',
                                                // 'csccanalessucursalescategorias.scaid',
                                                'can.canid',
                                                'can.cannombre'
                                            ]);

        foreach($cscs as $posicionCsc => $csc){

            $csps = array();
            $cont = 0;

            foreach($sucs as $suc){
                $cspscs = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                    ->join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
                                                    ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                    ->join('tprtipospromociones as tpr', 'tpr.tprid', 'prm.tprid')
                                                    ->where('cspcanalessucursalespromociones.fecid', $fec->fecid)
                                                    ->where('sca.catid', $catid)
                                                    ->where('sca.sucid', $suc->sucid)
                                                    ->where('csc.canid', $csc->canid)
                                                    ->where('cspcantidadplancha', '!=', "0")
                                                    ->where('cspestado', 1)
                                                    ->get([
                                                        'prmcodigo',
                                                        'prmaccion',
                                                        'prm.prmid',
                                                        'prmmecanica',
                                                        'cspcantidadcombo',
                                                        'cspcantidadplancha',
                                                        'tprnombre',
                                                        'cspgratis'
                                                    ]);

                foreach($cspscs as $cspsc){


                    $contadorEspecificoCsps = 0;

                    if(sizeof($csps) > 0){
                        foreach($csps as $posicionCsp => $csp){
                            // if($csp['prmcodigo'] == $cspsc->prmcodigo){
                            if($csp['prmmecanica'] == $cspsc->prmmecanica){

                                if(is_numeric ( $cspsc->cspcantidadcombo )){
                                    $cantidadComboNuevo = $cspsc->cspcantidadcombo;
                                }else{
                                    $cantidadComboNuevo = 0;
                                }
        
                                if(is_numeric ( $cspsc->cspcantidadplancha )){
                                    $cantidadPlanchaNuevo = $cspsc->cspcantidadplancha;
                                }else{
                                    $cantidadPlanchaNuevo = 0;
                                }

                                $csps[$posicionCsp]['cspcantidadcombo']   = $csps[$posicionCsp]['cspcantidadcombo'] + $cantidadComboNuevo;
                                $csps[$posicionCsp]['cspcantidadplancha'] = $csps[$posicionCsp]['cspcantidadplancha'] + $cantidadPlanchaNuevo;
                                $contadorEspecificoCsps = $posicionCsp;
                                break;
                            }
                            
                            if($posicionCsp+1 == sizeof($csps)){

                                if(is_numeric ( $cspsc->cspcantidadcombo )){
                                    $cantidadComboNuevo = $cspsc->cspcantidadcombo;
                                }else{
                                    $cantidadComboNuevo = 0;
                                }
        
                                if(is_numeric ( $cspsc->cspcantidadplancha )){
                                    $cantidadPlanchaNuevo = $cspsc->cspcantidadplancha;
                                }else{
                                    $cantidadPlanchaNuevo = 0;
                                }
                                
                                $csps[$cont]['prmcodigo'] = $cspsc->prmcodigo;
                                $csps[$cont]['cspcantidadcombo']   = $cantidadComboNuevo;
                                $csps[$cont]['cspcantidadplancha'] = $cantidadPlanchaNuevo;
                                $csps[$cont]['cspcompletado']      = 0;
                                $csps[$cont]['cspgratis']          = $cspsc->cspgratis;
                                $csps[$cont]['cspid']              = 0;
                                $csps[$cont]['cspnuevo']           = 0;
                                $csps[$cont]['cspplanchas']        = 0;
                                $csps[$cont]['csptotal']           = 0;
                                $csps[$cont]['csptotalcombo']      = 0;
                                $csps[$cont]['csptotalplancha']    = 0;
                                $csps[$cont]['cspvalorizado']      = 0;

                                $csps[$cont]['prmaccion']       = $cspsc->prmaccion;
                                $csps[$cont]['prmid']           = $cspsc->prmid;
                                $csps[$cont]['prmmecanica']     = $cspsc->prmmecanica;
                                $csps[$cont]['tprnombre']       = $cspsc->tprnombre;

                                $prppromocionesproductos = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
                                                                                    ->where('prppromocionesproductos.prmid', $cspsc->prmid )
                                                                                    ->get([
                                                                                        'prppromocionesproductos.prpid',
                                                                                        'pro.proid',
                                                                                        'pro.prosku',
                                                                                        'pro.pronombre',
                                                                                        'pro.proimagen',
                                                                                        'prpproductoppt',
                                                                                        'prpcomprappt',
                                                                                        'prpimagen',
                                                                                        'prpoimagen',
                                                                                    ]);

                                if(sizeof($prppromocionesproductos) > 0){
                                    $csps[$cont]['productos'] = $prppromocionesproductos;
                                }else{
                                    $csps[$cont]['productos'] = [];
                                }


                                $prbpromocionesbonificaciones = prbpromocionesbonificaciones::join('proproductos as pro', 'pro.proid', 'prbpromocionesbonificaciones.proid')
                                                                                            ->where('prbpromocionesbonificaciones.prmid', $cspsc->prmid )
                                                                                            ->get([
                                                                                                'prbpromocionesbonificaciones.prbid',
                                                                                                'pro.proid',
                                                                                                'pro.prosku',
                                                                                                'pro.pronombre',
                                                                                                'pro.proimagen',
                                                                                                'prbproductoppt',
                                                                                                'prbcomprappt',
                                                                                                'prbimagen',
                                                                                                'prboimagen',
                                                                                            ]);
                                
                                if(sizeof($prbpromocionesbonificaciones) > 0){
                                    $csps[$cont]['productosbonificados'] = $prbpromocionesbonificaciones;
                                }else{
                                    $csps[$cont]['productosbonificados'] = [];
                                }
                                
                                $cont = $cont + 1;
                            }
                        }

                    }else{
                        $csps[$cont]['prmcodigo'] = $cspsc->prmcodigo;

                        if(is_numeric ( $cspsc->cspcantidadcombo )){
                            $cantidadComboNuevo = $cspsc->cspcantidadcombo;
                        }else{
                            $cantidadComboNuevo = 0;
                        }

                        if(is_numeric ( $cspsc->cspcantidadplancha )){
                            $cantidadPlanchaNuevo = $cspsc->cspcantidadplancha;
                        }else{
                            $cantidadPlanchaNuevo = 0;
                        }

                        $csps[$cont]['cspcantidadcombo']   = $cantidadComboNuevo;
                        $csps[$cont]['cspcantidadplancha'] = $cantidadPlanchaNuevo;
                        $csps[$cont]['cspcompletado']      = 0;
                        $csps[$cont]['cspgratis']          = $cspsc->cspgratis;
                        $csps[$cont]['cspid']              = 0;
                        $csps[$cont]['cspnuevo']           = 0;
                        $csps[$cont]['cspplanchas']        = 0;
                        $csps[$cont]['csptotal']           = 0;
                        $csps[$cont]['csptotalcombo']      = 0;
                        $csps[$cont]['csptotalplancha']    = 0;
                        $csps[$cont]['cspvalorizado']      = 0;

                        $csps[$cont]['prmaccion']       = $cspsc->prmaccion;
                        $csps[$cont]['prmid']           = $cspsc->prmid;
                        $csps[$cont]['prmmecanica']     = $cspsc->prmmecanica;
                        $csps[$cont]['tprnombre']       = $cspsc->tprnombre;

                        $prppromocionesproductos = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
                                                                            ->where('prppromocionesproductos.prmid', $cspsc->prmid )
                                                                            ->get([
                                                                                'prppromocionesproductos.prpid',
                                                                                'pro.proid',
                                                                                'pro.prosku',
                                                                                'pro.pronombre',
                                                                                'pro.proimagen',
                                                                                'prpproductoppt',
                                                                                'prpcomprappt',
                                                                                'prpimagen',
                                                                                'prpoimagen',
                                                                            ]);

                        if(sizeof($prppromocionesproductos) > 0){
                            $csps[$cont]['productos'] = $prppromocionesproductos;
                        }else{
                            $csps[$cont]['productos'] = [];
                        }


                        $prbpromocionesbonificaciones = prbpromocionesbonificaciones::join('proproductos as pro', 'pro.proid', 'prbpromocionesbonificaciones.proid')
                                                                                    ->where('prbpromocionesbonificaciones.prmid', $cspsc->prmid )
                                                                                    ->get([
                                                                                        'prbpromocionesbonificaciones.prbid',
                                                                                        'pro.proid',
                                                                                        'pro.prosku',
                                                                                        'pro.pronombre',
                                                                                        'pro.proimagen',
                                                                                        'prbproductoppt',
                                                                                        'prbcomprappt',
                                                                                        'prbimagen',
                                                                                        'prboimagen',
                                                                                    ]);
                        
                        if(sizeof($prbpromocionesbonificaciones) > 0){
                            $csps[$cont]['productosbonificados'] = $prbpromocionesbonificaciones;
                        }else{
                            $csps[$cont]['productosbonificados'] = [];
                        }

                        $cont = $cont + 1;

                        
                    }
                }
            }

            $cscs[$posicionCsc]["cscid"] = 0;
            $cscs[$posicionCsc]["porcentaje"] = 0;
            $cscs[$posicionCsc]["promociones"] = $csps;






            // $csps = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
            //                                     ->join('tprtipospromociones as tpr', 'tpr.tprid', 'prm.tprid')
            //                                     ->join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
            //                                     ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
            //                                     ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
            //                                     ->where('cspcantidadplancha', '!=', "0")
            //                                     ->where('cspestado', 1)
            //                                     ->where('csc.canid', $csc->canid)
            //                                     ->where('prm.fecid', $fec->fecid)
            //                                     ->distinct('prm.prmcodigo')
            //                                     // ->distinct('cspcanalessucursalespromociones.prmid')
            //                                     ->where(function ($query) use($zonid, $gsuid, $casid) {
            //                                         if( $zonid != 0 ){
            //                                             $query->where('suc.zonid', $zonid);
            //                                         }
                
            //                                         if($gsuid != 0){
            //                                             $query->where('suc.gsuid', $gsuid);
            //                                         }
                                                    
            //                                         if($casid != 0){
            //                                             $query->where('suc.casid', $casid);
            //                                         }
            //                                     })
            //                                     ->get([
            //                                         // 'cspcanalessucursalespromociones.cspid',
            //                                         'prm.prmid',
            //                                         'prm.prmcodigo',
            //                                         // 'cspcanalessucursalespromociones.cspvalorizado',
            //                                         // 'cspcanalessucursalespromociones.cspplanchas',
            //                                         // 'cspcanalessucursalespromociones.cspcompletado',
            //                                         // 'cspcanalessucursalespromociones.cspcantidadcombo',
            //                                         'prm.prmmecanica',
            //                                         // 'cspcanalessucursalespromociones.cspcantidadplancha',
            //                                         // 'cspcanalessucursalespromociones.csptotalcombo',
            //                                         // 'cspcanalessucursalespromociones.csptotalplancha',
            //                                         // 'cspcanalessucursalespromociones.csptotal',
            //                                         // 'cspcanalessucursalespromociones.cspgratis',
            //                                         'prm.prmaccion',
            //                                         'tpr.tprnombre',
            //                                         // 'cspnuevo'
            //                                     ]);

            // foreach($csps as $posicionCsp => $csp){

            //     $cspsumacombo = cspcanalessucursalespromociones::join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
            //                                                 ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
            //                                                 ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
            //                                                 ->where('cspcantidadplancha', '!=', "0")
            //                                                 ->where('cspestado', 1)
            //                                                 ->where('csc.canid', $csc->canid)
            //                                                 ->where('cspcanalessucursalespromociones.prmid', $csp->prmid)
            //                                                 ->where('csc.fecid', $fec->fecid)
            //                                                 ->where(function ($query) use($zonid, $gsuid, $casid) {
            //                                                     if( $zonid != 0 ){
            //                                                         $query->where('suc.zonid', $zonid);
            //                                                     }

            //                                                     if($gsuid != 0){
            //                                                         $query->where('suc.gsuid', $gsuid);
            //                                                     }
                                                                
            //                                                     if($casid != 0){
            //                                                         $query->where('suc.casid', $casid);
            //                                                     }
            //                                                 })
            //                                                 ->sum('cspcantidadcombo');

            //     $cspsumaplancha = cspcanalessucursalespromociones::join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
            //                                                 ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
            //                                                 ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
            //                                                 ->where('cspcantidadplancha', '!=', "0")
            //                                                 ->where('cspestado', 1)
            //                                                 ->where('csc.canid', $csc->canid)
            //                                                 ->where('cspcanalessucursalespromociones.prmid', $csp->prmid)
            //                                                 ->where('csc.fecid', $fec->fecid)
            //                                                 ->where(function ($query) use($zonid, $gsuid, $casid) {
            //                                                     if( $zonid != 0 ){
            //                                                         $query->where('suc.zonid', $zonid);
            //                                                     }
                            
            //                                                     if($gsuid != 0){
            //                                                         $query->where('suc.gsuid', $gsuid);
            //                                                     }
                                                                
            //                                                     if($casid != 0){
            //                                                         $query->where('suc.casid', $casid);
            //                                                     }
            //                                                 })
            //                                                 ->sum('cspcantidadplancha');
                
            //     $prppromocionesproductos = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
            //                                                         ->where('prppromocionesproductos.prmid', $csp->prmid )
            //                                                         ->get([
            //                                                             'prppromocionesproductos.prpid',
            //                                                             'pro.proid',
            //                                                             'pro.prosku',
            //                                                             'pro.pronombre',
            //                                                             'pro.proimagen',
            //                                                             'prpproductoppt',
            //                                                             'prpcomprappt',
            //                                                             'prpimagen'
            //                                                         ]);

            //     if(sizeof($prppromocionesproductos) > 0){
            //         $csps[$posicionCsp]['productos'] = $prppromocionesproductos;
            //     }else{
            //         $csps[$posicionCsp]['productos'] = [];
            //     }


            //     $prbpromocionesbonificaciones = prbpromocionesbonificaciones::join('proproductos as pro', 'pro.proid', 'prbpromocionesbonificaciones.proid')
            //                                                                 ->where('prbpromocionesbonificaciones.prmid', $csp->prmid )
            //                                                                 ->get([
            //                                                                     'prbpromocionesbonificaciones.prbid',
            //                                                                     'pro.proid',
            //                                                                     'pro.prosku',
            //                                                                     'pro.pronombre',
            //                                                                     'pro.proimagen',
            //                                                                     'prbproductoppt',
            //                                                                     'prbcomprappt',
            //                                                                     'prbimagen'
            //                                                                 ]);
                
            //     if(sizeof($prbpromocionesbonificaciones) > 0){
            //         $csps[$posicionCsp]['productosbonificados'] = $prbpromocionesbonificaciones;
            //     }else{
            //         $csps[$posicionCsp]['productosbonificados'] = [];
            //     }

            //     $csps[$posicionCsp]["cspcantidadcombo"]   = $cspsumacombo;
            //     $csps[$posicionCsp]["cspcantidadplancha"] = $cspsumaplancha;
            //     $csps[$posicionCsp]["cspcompletado"] = 0;
            //     $csps[$posicionCsp]["cspvalorizado"] = 0;
            //     $csps[$posicionCsp]["cspplanchas"] = 0;
            //     $csps[$posicionCsp]["csptotalcombo"] = 0;
            //     $csps[$posicionCsp]["csptotalplancha"] = 0;
            //     $csps[$posicionCsp]["csptotal"] = 0;
            //     $csps[$posicionCsp]["cspgratis"] = 0;
            //     $csps[$posicionCsp]["cspnuevo"] = 0;

            // }

            // $cscs[$posicionCsc]["cscid"] = 0;
            // $cscs[$posicionCsc]["porcentaje"] = 0;
            // $cscs[$posicionCsc]["promociones"] = $csps;
        }

        $linea          = __LINE__;
        $respuesta      = true;
        $datos          = $cscs;
        $mensaje        = 'Las promociones se cargaron satisfactoriamente';
        
        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev
        ]);
        
        return $requestsalida;
    }
}
