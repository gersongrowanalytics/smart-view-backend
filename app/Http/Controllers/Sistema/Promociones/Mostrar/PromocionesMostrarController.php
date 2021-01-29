<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\csccanalessucursalescategorias;
use App\cspcanalessucursalespromociones;
use App\prppromocionesproductos;
use App\prbpromocionesbonificaciones;

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
            $csccanalessucursalescategorias = csccanalessucursalescategorias::join('cancanales as can', 'can.canid', 'csccanalessucursalescategorias.canid')
                                                                        ->join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                                                        ->where('csccanalessucursalescategorias.scaid', $scaid)
                                                                        // ->where('csp.cspcantidadcombo', '!=', "0")
                                                                        ->where('csp.cspcantidadplancha', '!=', "0")
                                                                        ->where('csp.cspestado', 1)
                                                                        ->distinct('can.canid')
                                                                        ->get([
                                                                            'csccanalessucursalescategorias.cscid',
                                                                            'csccanalessucursalescategorias.scaid',
                                                                            'can.canid',
                                                                            'can.cannombre'
                                                                        ]);
                                                    
            if(sizeof($csccanalessucursalescategorias) > 0){
                
                foreach($csccanalessucursalescategorias as $posicion => $csccanalesucursalcategoria){
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
                                                                                    'prpimagen'
                                                                                ]);

                            if(sizeof($prppromocionesproductos) > 0){
                                $cspcanalessucursalespromociones[$posicionPromociones]['productos'] = $prppromocionesproductos;
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
                                                                                            'prbimagen'
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
            'mensajedev'     => $mensajedev
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

        $cscs = csccanalessucursalescategorias::join('cancanales as can', 'can.canid', 'csccanalessucursalescategorias.canid')
                                            ->join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                            ->join('scasucursalescategorias as sca', 'sca.scaid', 'csccanalessucursalescategorias.scaid')
                                            ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                            ->where('sca.catid', $catid)
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
                                                'csccanalessucursalescategorias.cscid',
                                                'csccanalessucursalescategorias.scaid',
                                                'can.canid',
                                                'can.cannombre'
                                            ]);

        foreach($cscs as $posicionCsc => $csc){
            $csps = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                ->join('tprtipospromociones as tpr', 'tpr.tprid', 'prm.tprid')
                                                ->join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
                                                ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                                ->where('cspcantidadplancha', '!=', "0")
                                                ->where('cspestado', 1)
                                                ->where('csc.canid', $csc->canid)
                                                ->distinct('cspcanalessucursalespromociones.prmid')
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

            foreach($csps as $posicionCsp => $csp){

                $cspsumacombo = cspcanalessucursalespromociones::join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
                                                            ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                                            ->where('cspcantidadplancha', '!=', "0")
                                                            ->where('cspestado', 1)
                                                            ->where('csc.canid', $csc->canid)
                                                            ->where('cspcanalessucursalespromociones.prmid', $csp->prmid)
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
                                                            ->sum('cspcantidadcombo');

                $cspsumaplancha = cspcanalessucursalespromociones::join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
                                                            ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'sca.sucid')
                                                            ->where('cspcantidadplancha', '!=', "0")
                                                            ->where('cspestado', 1)
                                                            ->where('csc.canid', $csc->canid)
                                                            ->where('cspcanalessucursalespromociones.prmid', $csp->prmid)
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
                                                            ->sum('cspcantidadplancha');
                
                $prppromocionesproductos = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
                                                                    ->where('prppromocionesproductos.prmid', $csp->prmid )
                                                                    ->get([
                                                                        'prppromocionesproductos.prpid',
                                                                        'pro.proid',
                                                                        'pro.prosku',
                                                                        'pro.pronombre',
                                                                        'pro.proimagen',
                                                                        'prpproductoppt',
                                                                        'prpcomprappt',
                                                                        'prpimagen'
                                                                    ]);

                if(sizeof($prppromocionesproductos) > 0){
                    $csps[$posicionCsp]['productos'] = $prppromocionesproductos;
                }else{
                    $csps[$posicionCsp]['productos'] = [];
                }


                $prbpromocionesbonificaciones = prbpromocionesbonificaciones::join('proproductos as pro', 'pro.proid', 'prbpromocionesbonificaciones.proid')
                                                                            ->where('prbpromocionesbonificaciones.prmid', $csp->prmid )
                                                                            ->get([
                                                                                'prbpromocionesbonificaciones.prbid',
                                                                                'pro.proid',
                                                                                'pro.prosku',
                                                                                'pro.pronombre',
                                                                                'pro.proimagen',
                                                                                'prbproductoppt',
                                                                                'prbcomprappt',
                                                                                'prbimagen'
                                                                            ]);
                
                if(sizeof($prbpromocionesbonificaciones) > 0){
                    $csps[$posicionCsp]['productosbonificados'] = $prbpromocionesbonificaciones;
                }else{
                    $csps[$posicionCsp]['productosbonificados'] = [];
                }

                $csps[$posicionCsp]["cspcantidadcombo"]   = $cspsumacombo;
                $csps[$posicionCsp]["cspcantidadplancha"] = $cspsumaplancha;
                $csps[$posicionCsp]["cspcompletado"] = 0;
                $csps[$posicionCsp]["cspid"] = 0;

            }

            $cscs[$posicionCsc]["cscid"] = 0;
            $cscs[$posicionCsc]["porcentaje"] = 0;
            $cscs[$posicionCsc]["promociones"] = $csps;
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
