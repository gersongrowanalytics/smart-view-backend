<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\csccanalessucursalescategorias;
use App\Http\Controllers\AuditoriaController;
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
                                                                        ->where('csccanalessucursalescategorias.scaid', $scaid)
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
                                                                                        ->get([
                                                                                            'cspcanalessucursalespromociones.cspid',
                                                                                            'prm.prmid',
                                                                                            'cspcanalessucursalespromociones.cspvalorizado',
                                                                                            'cspcanalessucursalespromociones.cspplanchas',
                                                                                            'cspcanalessucursalespromociones.cspcompletado',
                                                                                            'cspcanalessucursalespromociones.cspcantidadcombo',
                                                                                            'prm.prmmecanica',
                                                                                            'cspcanalessucursalespromociones.cspcantidadplancha',
                                                                                            'cspcanalessucursalespromociones.csptotalcombo',
                                                                                            'cspcanalessucursalespromociones.csptotalplancha',
                                                                                            'cspcanalessucursalespromociones.csptotal',
                                                                                            'prm.prmaccion',
                                                                                            'tpr.tprnombre'
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
                                                                                    'pro.proid',
                                                                                    'pro.prosku',
                                                                                    'pro.pronombre',
                                                                                    'pro.proimagen',
                                                                                    'prpproductoppt',
                                                                                    'prpcomprappt'
                                                                                ]);

                            if(sizeof($prppromocionesproductos) > 0){
                                $cspcanalessucursalespromociones[$posicionPromociones]['productos'] = $prppromocionesproductos;
                            }else{
                                $cspcanalessucursalespromociones[$posicionPromociones]['productos'] = [];
                            }

    
                            $prbpromocionesbonificaciones = prbpromocionesbonificaciones::join('proproductos as pro', 'pro.proid', 'prbpromocionesbonificaciones.proid')
                                                                                        ->where('prbpromocionesbonificaciones.prmid', $cspcanalesucursalpromocion->prmid )
                                                                                        ->get([
                                                                                            'pro.proid',
                                                                                            'pro.prosku',
                                                                                            'pro.pronombre',
                                                                                            'pro.proimagen',
                                                                                            'prbproductoppt',
                                                                                            'prbcomprappt'
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

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            $request['ip'],
            $request,
            $requestsalida,
            'Mostrar las promociones de una categoria seleccionada, con su canal correspondiente segun el filtro de sucursal, fecha (dia, mes, año)',
            'MOSTRAR',
            '',
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
