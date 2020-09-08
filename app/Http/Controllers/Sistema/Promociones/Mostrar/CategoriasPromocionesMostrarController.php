<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\csccanalessucursalescategorias;
use App\scasucursalescategorias;
use App\prppromocionesproductos;
use App\prbpromocionesbonificaciones;
use App\cspcanalessucursalespromociones;

class CategoriasPromocionesMostrarController extends Controller
{
    public function mostrarCategoriasPromociones(Request $request)
    {

        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
        $dia        = $request['dia'];
        $mes        = $request['mes'];
        $anio       = $request['ano'];
        
        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;


        try{

            $nuevoArray = array(
                array(
                    "ANIO"                  => "",
                    "MES"                   => "",
                    "CATEGORIA"             => "",
                    "CANAL"                 => "",
                    "MECANICA"              => "",
                    "SKU"                   => "",
                    "PRODUCTO"              => "",
                    "SKU BONIFICADO"        => "",
                    "PRODUCTO BONIFICADO"   => "",
                    "PLANCHAS ROTAR"        => "",
                    "#COMBOS"               => "",
                    "RECONOCER X COMBO"     => "",
                    "RECONOCER X PLANCHA"   => "",
                    "TOTAL SOLES"           => "",
                    "ACCION"                => ""
                )
            );

            $scasucursalescategorias = scasucursalescategorias::join('fecfechas as fec', 'scasucursalescategorias.fecid', 'fec.fecid')
                                                                ->join('catcategorias as cat', 'cat.catid', 'scasucursalescategorias.catid')
                                                                ->where('scasucursalescategorias.sucid', $sucid)
                                                                ->where('fec.fecano', $ano)
                                                                ->where('fec.fecmes', $mes)
                                                                ->where('fec.fecdia', $dia)
                                                                ->where('scasucursalescategorias.tsuid', null)
                                                                ->get([
                                                                    'scasucursalescategorias.scaid',
                                                                    'cat.catid',
                                                                    'cat.catnombre',
                                                                    'cat.catimagenfondo',
                                                                    'cat.catimagenfondoseleccionado',
                                                                    'cat.catimagenfondoopaco',
                                                                    'cat.caticono',
                                                                    'cat.caticonohover',
                                                                    'cat.catcolorhover',
                                                                    'cat.catcolor',
                                                                    'cat.caticonoseleccionado',
                                                                    'fec.fecfecha'
                                                                ]);

            if(sizeof($scasucursalescategorias) > 0){
                $contador = 0;
                foreach($scasucursalescategorias as $posicionSca => $sca){
                    $nuevoArray[$contador]['ANIO']      = $anio;
                    $nuevoArray[$contador]['MES']       = $mes;
                    $nuevoArray[$contador]['CATEGORIA'] = $sca->catnombre;


                    $csccanalessucursalescategorias = csccanalessucursalescategorias::join('cancanales as can', 'can.canid', 'csccanalessucursalescategorias.canid')
                                                                        ->where('csccanalessucursalescategorias.scaid', $sca->scaid)
                                                                        ->get([
                                                                            'csccanalessucursalescategorias.cscid',
                                                                            'can.canid',
                                                                            'can.cannombre'
                                                                        ]);
                    //**************************************** */
                    if(sizeof($csccanalessucursalescategorias) > 0){
                        foreach($csccanalessucursalescategorias as $posicion => $csccanalesucursalcategoria){
                            if($posicion == 0){
                                $nuevoArray[$contador]['CANAL'] = $csccanalesucursalcategoria->cannombre;
                            }else{
                                $contador = $contador+1;
                                $nuevoArray[$contador]['ANIO']      = $anio;
                                $nuevoArray[$contador]['MES']       = $mes;
                                $nuevoArray[$contador]['CATEGORIA'] = $sca->catnombre;
                                $nuevoArray[$contador]['CANAL']     = $csccanalesucursalcategoria->cannombre;
                            }

                            $cspcanalessucursalespromociones = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
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
                                                                                                    'prm.prmaccion'
                                                                                                ]);

                            if(sizeof($cspcanalessucursalespromociones) > 0){
                                foreach($cspcanalessucursalespromociones as $posicionPromociones => $cspcanalesucursalpromocion){
                                    if($posicionPromociones == 0){
                                        $nuevoArray[$contador]['MECANICA']            = $cspcanalesucursalpromocion->prmmecanica;
                                        $nuevoArray[$contador]['PLANCHAS ROTAR']      = $cspcanalesucursalpromocion->cspcantidadplancha;
                                        $nuevoArray[$contador]['#COMBOS']             = $cspcanalesucursalpromocion->cspcantidadcombo;
                                        $nuevoArray[$contador]['RECONOCER X COMBO']   = $cspcanalesucursalpromocion->csptotalcombo;
                                        $nuevoArray[$contador]['RECONOCER X PLANCHA'] = $cspcanalesucursalpromocion->csptotalplancha;
                                        $nuevoArray[$contador]['TOTAL SOLES']         = $cspcanalesucursalpromocion->csptotal;
                                        $nuevoArray[$contador]['ACCION']              = $cspcanalesucursalpromocion->prmaccion;
                                    }else{
                                        $contador = $contador+1;
                                        $nuevoArray[$contador]['ANIO']                = $anio;
                                        $nuevoArray[$contador]['MES']                 = $mes;
                                        $nuevoArray[$contador]['CATEGORIA']           = $sca->catnombre;
                                        $nuevoArray[$contador]['CANAL']               = $csccanalesucursalcategoria->cannombre;
                                        $nuevoArray[$contador]['MECANICA']            = $cspcanalesucursalpromocion->prmmecanica;
                                        $nuevoArray[$contador]['PLANCHAS ROTAR']      = $cspcanalesucursalpromocion->cspcantidadplancha;
                                        $nuevoArray[$contador]['#COMBOS']             = $cspcanalesucursalpromocion->cspcantidadcombo;
                                        $nuevoArray[$contador]['RECONOCER X COMBO']   = $cspcanalesucursalpromocion->csptotalcombo;
                                        $nuevoArray[$contador]['RECONOCER X PLANCHA'] = $cspcanalesucursalpromocion->csptotalplancha;
                                        $nuevoArray[$contador]['TOTAL SOLES']         = $cspcanalesucursalpromocion->csptotal;
                                        $nuevoArray[$contador]['ACCION']              = $cspcanalesucursalpromocion->prmaccion;
                                    }

                                    $prppromocionesproductos = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
                                                                                        ->where('prppromocionesproductos.prmid', $cspcanalesucursalpromocion->prmid )
                                                                                        ->get([
                                                                                            'pro.proid',
                                                                                            'pro.prosku',
                                                                                            'pro.pronombre',
                                                                                            'pro.proimagen',
                                                                                            'prpproductoppt',
                                                                                            'prpcomprappt',
                                                                                            'prpcodigoprincipal'
                                                                                        ]);

                                    if(sizeof($prppromocionesproductos) > 0){
                                        foreach($prppromocionesproductos as $posicionProductos => $prp ){
                                            if($posicionProductos == 0){
                                                $nuevoArray[$contador]['SKU']      = $prp->prosku;
                                                $nuevoArray[$contador]['PRODUCTO'] = $prp->prpproductoppt;
                                            }else{
                                                $contador = $contador+1;
                                                $nuevoArray[$contador]['ANIO']                = $anio;
                                                $nuevoArray[$contador]['MES']                 = $mes;
                                                $nuevoArray[$contador]['CATEGORIA']           = $sca->catnombre;
                                                $nuevoArray[$contador]['CANAL']               = $csccanalesucursalcategoria->cannombre;
                                                $nuevoArray[$contador]['MECANICA']            = $cspcanalesucursalpromocion->prmmecanica;
                                                $nuevoArray[$contador]['PLANCHAS ROTAR']      = $cspcanalesucursalpromocion->cspcantidadplancha;
                                                $nuevoArray[$contador]['#COMBOS']             = $cspcanalesucursalpromocion->cspcantidadcombo;
                                                $nuevoArray[$contador]['RECONOCER X COMBO']   = $cspcanalesucursalpromocion->csptotalcombo;
                                                $nuevoArray[$contador]['RECONOCER X PLANCHA'] = $cspcanalesucursalpromocion->csptotalplancha;
                                                $nuevoArray[$contador]['TOTAL SOLES']         = $cspcanalesucursalpromocion->csptotal;
                                                $nuevoArray[$contador]['ACCION']              = $cspcanalesucursalpromocion->prmaccion;
                                                $nuevoArray[$contador]['SKU']                 = $prp->prosku;
                                                $nuevoArray[$contador]['PRODUCTO']            = $prp->prpproductoppt;
                                            }



                                            $prbpromocionesbonificaciones = prbpromocionesbonificaciones::join('proproductos as pro', 'pro.proid', 'prbpromocionesbonificaciones.proid')
                                                                                                ->where('prbpromocionesbonificaciones.prmid', $cspcanalesucursalpromocion->prmid )
                                                                                                ->where('prbcodigoprincipal', $prp->prpcodigoprincipal )
                                                                                                ->first([
                                                                                                    'pro.proid',
                                                                                                    'pro.prosku',
                                                                                                    'pro.pronombre',
                                                                                                    'pro.proimagen',
                                                                                                    'prbproductoppt',
                                                                                                    'prbcomprappt',
                                                                                                    'prbcodigoprincipal'
                                                                                                ]);
                                    
                                            if($prbpromocionesbonificaciones){
                                                $nuevoArray[$contador]['SKU BONIFICADO']      = $prbpromocionesbonificaciones->prosku;
                                                $nuevoArray[$contador]['PRODUCTO BONIFICADO'] = $prbpromocionesbonificaciones->prbproductoppt;
                                            }else{
                                                $nuevoArray[$contador]['SKU BONIFICADO']      = '';
                                                $nuevoArray[$contador]['PRODUCTO BONIFICADO'] = '';
                                            }
                                        }
                                    }else{
                                        $nuevoArray[$contador]['SKU']      = '';
                                        $nuevoArray[$contador]['PRODUCTO'] = '';
                                    }
                                }
                            }else{
                                $nuevoArray[$contador]['MECANICA']            = '';
                                $nuevoArray[$contador]['PLANCHAS ROTAR']      = '';
                                $nuevoArray[$contador]['#COMBOS']             = '';
                                $nuevoArray[$contador]['RECONOCER X COMBO']   = '';
                                $nuevoArray[$contador]['RECONOCER X PLANCHA'] = '';
                                $nuevoArray[$contador]['TOTAL SOLES']         = '';
                                $nuevoArray[$contador]['ACCION']              = '';
                            }
                        }
                    }else{
                        $nuevoArray[$contador]['CANAL'] = '';
                    }

                    $contador = $contador+1;

                    //**************************************** */
                }
                $respuesta = true;
                $datos     = $nuevoArray;
            }else{
                $respuesta      = false;
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos, no se contramos categorias registradas a este filtro.';
                $mensajeDetalle = sizeof($scasucursalescategorias).' registros encontrados.';
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
            'MOSTRAR LAS PROMOCIONES DE UN USUARIO PARA DESCARGAR UN EXCEL',
            'DESCARGAR',
            '',
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
