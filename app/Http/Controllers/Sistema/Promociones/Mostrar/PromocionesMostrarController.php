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

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

        $usutoken   = $request['usutoken'];
        $scaid      = $request['scaid']; //id de la cateogira de una sucursal 
        
        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $fechaInicio = date("m", strtotime($fechaActual));
        $fechaInicio = "01/".$fechaInicio;
        $fechaFinal = date("m", strtotime($fechaActual));
        $fechafinal = "30/".$fechaFinal;

        $promocionVacia = array(
            'cspid'              => 0,
            'prmid'              => "",
            'prmcodigo'          => "",
            'cspvalorizado'      => "",
            'cspplanchas'        => "",
            'cspcompletado'      => "",
            'cspcantidadcombo'   => "",
            'prmmecanica'        => "",
            'cspcantidadplancha' => "",
            'csptotalcombo'      => "",
            'csptotalplancha'    => "",
            'csptotal'           => "",
            'cspgratis'          => "",
            'prmaccion'          => "",
            'tprnombre'          => "",
            'cspnuevo'           => "",
            'productos'          => [],
            'productoPrincipal'  => "0",
            'productosbonificados' => [],
            'fechainicio' => $fechaInicio,
            'fechafinal'  => $fechafinal,

        );

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
        //  13716 group by csc.cscid order by cont desc;
                                                    
            if(sizeof($csccanalessucursalescategorias) > 0){
                
                $productosCsc = [];

                

                foreach($csccanalessucursalescategorias as $posicion => $csccanalesucursalcategoria){

                    $nuevoArrayCsp = array();
                    
                    $cspcanalessucursalespromociones = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                                                        ->join('tprtipospromociones as tpr', 'tpr.tprid', 'prm.tprid')
                                                                                        ->join('fecfechas as fec', 'fec.fecid', 'cspcanalessucursalespromociones.fecid')
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
                                                                                            'cspnuevo',
                                                                                            'fec.fecid',
                                                                                            'fec.fecfecha',
                                                                                            'cspcanalessucursalespromociones.cspiniciopromo',
                                                                                            'cspcanalessucursalespromociones.cspfinpromo',
                                                                                        ]);
                    $numeroPromocionesTerminadas = 0;

                    if(sizeof($cspcanalessucursalespromociones) > 0){

                        $productosCscMomento = [];

                        foreach($cspcanalessucursalespromociones as $posicionPromociones => $cspcanalesucursalpromocion){

                            if($cspcanalesucursalpromocion->cspiniciopromo == null){
                                $fechaInicio = date("d/m", strtotime($cspcanalesucursalpromocion->fecfecha));

                                $fechaFinal = date("m", strtotime($cspcanalesucursalpromocion->fecfecha));
                                $fechafinal = "30/".$fechaFinal;
                            }else{
                                $fechaInicio = date("d/m", strtotime($cspcanalesucursalpromocion->cspiniciopromo));

                                $fechaFinal = date("d/m", strtotime($cspcanalesucursalpromocion->cspfinpromo));
                                $fechafinal = $fechaFinal;
                            }

                            $cspcanalessucursalespromociones[$posicionPromociones]['fechainicio'] = $fechaInicio;
                            $cspcanalessucursalespromociones[$posicionPromociones]['fechafinal']  = $fechafinal;

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

                            if($prppromocionesproductos){
                                $cspcanalessucursalespromociones[$posicionPromociones]['productos'] = $prppromocionesproductos;

                                if($posicion == 0){
                                    $productosCsc[] = $prppromocionesproductos[0]['prosku'];
                                }else{
                                    $productosCscMomento[] = $prppromocionesproductos[0]['prosku'];
                                }

                                $cspcanalessucursalespromociones[$posicionPromociones]['productoPrincipal'] = $prppromocionesproductos[0]['prosku'];

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


                        if($posicion != 0){

                            $productosDuplicados = [];

                            foreach ($productosCsc as $posicionProductoCsc => $productoCsc) {
                                
                                $encontroProducto = false;

                                foreach ($cspcanalessucursalespromociones as $posicionCspDos => $csp) {
                                    
                                    if($csp->productoPrincipal == $productoCsc){
                                        $encontroProducto = true;
                                        $nuevoArrayCsp[] = array(
                                            'cspid'              => $csp->cspid,
                                            'prmid'              => $csp->prmid,
                                            'prmcodigo'          => $csp->prmcodigo,
                                            'cspvalorizado'      => $csp->cspvalorizado,
                                            'cspplanchas'        => $csp->cspplanchas,
                                            'cspcompletado'      => $csp->cspcompletado,
                                            'cspcantidadcombo'   => $csp->cspcantidadcombo,
                                            'prmmecanica'        => $csp->prmmecanica,
                                            'cspcantidadplancha' => $csp->cspcantidadplancha,
                                            'csptotalcombo'      => $csp->csptotalcombo,
                                            'csptotalplancha'    => $csp->csptotalplancha,
                                            'csptotal'           => $csp->csptotal,
                                            'cspgratis'          => $csp->cspgratis,
                                            'prmaccion'          => $csp->prmaccion,
                                            'tprnombre'          => $csp->tprnombre,
                                            'cspnuevo'           => $csp->cspnuevo,
                                            'productos'          => $csp->productos,
                                            'productoPrincipal'  => $csp->productoPrincipal,
                                            'productosbonificados' => $csp->productosbonificados,
                                            'fechainicio' => $csp->fechainicio,
                                            'fechafinal' => $csp->fechafinal,
                                        );


                                        $encontroProductoDuplicado = false;

                                        foreach($productosDuplicados as $productoDuplicado){
                                            if($productoDuplicado == $productoCsc){
                                                $encontroProductoDuplicado = true;
                                                
                                                $nuevoArrayCspDuplicado = array();
                                                $nuevoArrayCspn = $csccanalessucursalescategorias[0]['promocionesOrdenadas'];
                                                $productosMasDuplicados = "";

                                                foreach($nuevoArrayCspn as $posicionNuevInv => $nuevoArrayCspInv){
                                                    if($nuevoArrayCspn[$posicionNuevInv]['productoPrincipal'] == $productoDuplicado){

                                                        $nuevoArrayCspDuplicado[] = array(
                                                            'cspid'              => $nuevoArrayCspn[$posicionNuevInv]['cspid'],
                                                            'prmid'              => $nuevoArrayCspn[$posicionNuevInv]['prmid'],
                                                            'prmcodigo'          => $nuevoArrayCspn[$posicionNuevInv]['prmcodigo'],
                                                            'cspvalorizado'      => $nuevoArrayCspn[$posicionNuevInv]['cspvalorizado'],
                                                            'cspplanchas'        => $nuevoArrayCspn[$posicionNuevInv]['cspplanchas'],
                                                            'cspcompletado'      => $nuevoArrayCspn[$posicionNuevInv]['cspcompletado'],
                                                            'cspcantidadcombo'   => $nuevoArrayCspn[$posicionNuevInv]['cspcantidadcombo'],
                                                            'prmmecanica'        => $nuevoArrayCspn[$posicionNuevInv]['prmmecanica'],
                                                            'cspcantidadplancha' => $nuevoArrayCspn[$posicionNuevInv]['cspcantidadplancha'],
                                                            'csptotalcombo'      => $nuevoArrayCspn[$posicionNuevInv]['csptotalcombo'],
                                                            'csptotalplancha'    => $nuevoArrayCspn[$posicionNuevInv]['csptotalplancha'],
                                                            'csptotal'           => $nuevoArrayCspn[$posicionNuevInv]['csptotal'],
                                                            'cspgratis'          => $nuevoArrayCspn[$posicionNuevInv]['cspgratis'],
                                                            'prmaccion'          => $nuevoArrayCspn[$posicionNuevInv]['prmaccion'],
                                                            'tprnombre'          => $nuevoArrayCspn[$posicionNuevInv]['tprnombre'],
                                                            'cspnuevo'           => $nuevoArrayCspn[$posicionNuevInv]['cspnuevo'],
                                                            'productos'          => $nuevoArrayCspn[$posicionNuevInv]['productos'],
                                                            'productoPrincipal'  => $nuevoArrayCspn[$posicionNuevInv]['productoPrincipal'],
                                                            'productosbonificados' => $nuevoArrayCspn[$posicionNuevInv]['productosbonificados'],
                                                            'fechainicio' => $nuevoArrayCspn[$posicionNuevInv]['fechainicio'],
                                                            'fechafinal' => $nuevoArrayCspn[$posicionNuevInv]['fechafinal'],
                                                        );

                                                        if(sizeof($nuevoArrayCspn)-1 >=  $posicionNuevInv+1 ){
                                                            if($nuevoArrayCspn[$posicionNuevInv+1]['productoPrincipal'] == $productoDuplicado){
            
                                                                $productosMasDuplicados = $productoDuplicado;
                
                                                            }else{
                                                                if($productosMasDuplicados != $productoDuplicado){

                                                                    $fechaInicio = date("m", strtotime($fechaActual));
                                                                    $fechaInicio = "01/".$fechaInicio;
                                                                    $fechaFinal = date("m", strtotime($fechaActual));
                                                                    $fechafinal = "30/".$fechaFinal;

                                                                    $nuevoArrayCspDuplicado[] = array(
                                                                        'cspid'              => 0,
                                                                        'prmid'              => "",
                                                                        'prmcodigo'          => "",
                                                                        'cspvalorizado'      => "",
                                                                        'cspplanchas'        => "",
                                                                        'cspcompletado'      => "",
                                                                        'cspcantidadcombo'   => "",
                                                                        'prmmecanica'        => "",
                                                                        'cspcantidadplancha' => "",
                                                                        'csptotalcombo'      => "",
                                                                        'csptotalplancha'    => "",
                                                                        'csptotal'           => "",
                                                                        'cspgratis'          => "",
                                                                        'prmaccion'          => "",
                                                                        'tprnombre'          => "",
                                                                        'cspnuevo'           => "",
                                                                        'productos'          => [],
                                                                        'productoPrincipal'  => $productoCsc,
                                                                        'productosbonificados' => [],
                                                                        'fechainicio' => $fechaInicio,
                                                                        'fechafinal'  => $fechafinal,
                                
                                                                    );
                                                                }
                                                            }
                                                        }

                                                    }else{
                                                        $nuevoArrayCspDuplicado[] = array(
                                                            'cspid'              => $nuevoArrayCspn[$posicionNuevInv]['cspid'],
                                                            'prmid'              => $nuevoArrayCspn[$posicionNuevInv]['prmid'],
                                                            'prmcodigo'          => $nuevoArrayCspn[$posicionNuevInv]['prmcodigo'],
                                                            'cspvalorizado'      => $nuevoArrayCspn[$posicionNuevInv]['cspvalorizado'],
                                                            'cspplanchas'        => $nuevoArrayCspn[$posicionNuevInv]['cspplanchas'],
                                                            'cspcompletado'      => $nuevoArrayCspn[$posicionNuevInv]['cspcompletado'],
                                                            'cspcantidadcombo'   => $nuevoArrayCspn[$posicionNuevInv]['cspcantidadcombo'],
                                                            'prmmecanica'        => $nuevoArrayCspn[$posicionNuevInv]['prmmecanica'],
                                                            'cspcantidadplancha' => $nuevoArrayCspn[$posicionNuevInv]['cspcantidadplancha'],
                                                            'csptotalcombo'      => $nuevoArrayCspn[$posicionNuevInv]['csptotalcombo'],
                                                            'csptotalplancha'    => $nuevoArrayCspn[$posicionNuevInv]['csptotalplancha'],
                                                            'csptotal'           => $nuevoArrayCspn[$posicionNuevInv]['csptotal'],
                                                            'cspgratis'          => $nuevoArrayCspn[$posicionNuevInv]['cspgratis'],
                                                            'prmaccion'          => $nuevoArrayCspn[$posicionNuevInv]['prmaccion'],
                                                            'tprnombre'          => $nuevoArrayCspn[$posicionNuevInv]['tprnombre'],
                                                            'cspnuevo'           => $nuevoArrayCspn[$posicionNuevInv]['cspnuevo'],
                                                            'productos'          => $nuevoArrayCspn[$posicionNuevInv]['productos'],
                                                            'productoPrincipal'  => $nuevoArrayCspn[$posicionNuevInv]['productoPrincipal'],
                                                            'productosbonificados' => $nuevoArrayCspn[$posicionNuevInv]['productosbonificados'],
                                                            'fechainicio'       => $nuevoArrayCspn[$posicionNuevInv]['fechainicio'],
                                                            'fechafinal'        => $nuevoArrayCspn[$posicionNuevInv]['fechafinal'],
                                                        );
                                                    }
                                                }

                                                $csccanalessucursalescategorias[0]['promocionesOrdenadas'] = $nuevoArrayCspDuplicado;
                                            }
                                        }

                                        if($encontroProductoDuplicado == false){
                                            $productosDuplicados[] = $productoCsc;
                                        }

                                    }

                                }

                                if($encontroProducto == false){
                                    
                                    $fechaInicio = date("m", strtotime($fechaActual));
                                    $fechaInicio = "01/".$fechaInicio;
                                    $fechaFinal = date("m", strtotime($fechaActual));
                                    $fechafinal = "30/".$fechaFinal;

                                    $nuevoArrayCsp[] = array(
                                        'cspid'              => 0,
                                        'prmid'              => "",
                                        'prmcodigo'          => "",
                                        'cspvalorizado'      => "",
                                        'cspplanchas'        => "",
                                        'cspcompletado'      => "",
                                        'cspcantidadcombo'   => "",
                                        'prmmecanica'        => "",
                                        'cspcantidadplancha' => "",
                                        'csptotalcombo'      => "",
                                        'csptotalplancha'    => "",
                                        'csptotal'           => "",
                                        'cspgratis'          => "",
                                        'prmaccion'          => "",
                                        'tprnombre'          => "",
                                        'cspnuevo'           => "",
                                        'productos'          => [],
                                        'productoPrincipal'  => $productoCsc,
                                        'productosbonificados' => [],
                                        'fechainicio' => $fechaInicio,
                                        'fechafinal'  => $fechafinal,

                                    );
                                }
                            }

                            foreach ($cspcanalessucursalespromociones as $posicionCspDos => $csp) {
                                $encontroProductoMomento = false;
                                foreach ($productosCsc as $key => $productoCsc) {
                                    if($csp->productoPrincipal == $productoCsc){
                                        $encontroProductoMomento = true;
                                    }
                                }

                                if($encontroProductoMomento == false){
                                    $nuevoArrayCsp[] = array(
                                        'cspid'              => $csp->cspid,
                                        'prmid'              => $csp->prmid,
                                        'prmcodigo'          => $csp->prmcodigo,
                                        'cspvalorizado'      => $csp->cspvalorizado,
                                        'cspplanchas'        => $csp->cspplanchas,
                                        'cspcompletado'      => $csp->cspcompletado,
                                        'cspcantidadcombo'   => $csp->cspcantidadcombo,
                                        'prmmecanica'        => $csp->prmmecanica,
                                        'cspcantidadplancha' => $csp->cspcantidadplancha,
                                        'csptotalcombo'      => $csp->csptotalcombo,
                                        'csptotalplancha'    => $csp->csptotalplancha,
                                        'csptotal'           => $csp->csptotal,
                                        'cspgratis'          => $csp->cspgratis,
                                        'prmaccion'          => $csp->prmaccion,
                                        'tprnombre'          => $csp->tprnombre,
                                        'cspnuevo'           => $csp->cspnuevo,
                                        'productos'          => $csp->productos,
                                        'productoPrincipal'  => $csp->productoPrincipal,
                                        'productosbonificados' => $csp->productosbonificados,
                                        'fechainicio' => $csp->fechainicio,
                                        'fechafinal' => $csp->fechafinal,

                                    );
                                }

                            }

                        }else{
                            // $nuevoArrayCsp = $cspcanalessucursalespromociones;

                            foreach ($cspcanalessucursalespromociones as $posicionCsp => $csp) {
                                $nuevoArrayCsp[] = array(
                                    'cspid'              => $csp->cspid,
                                    'prmid'              => $csp->prmid,
                                    'prmcodigo'          => $csp->prmcodigo,
                                    'cspvalorizado'      => $csp->cspvalorizado,
                                    'cspplanchas'        => $csp->cspplanchas,
                                    'cspcompletado'      => $csp->cspcompletado,
                                    'cspcantidadcombo'   => $csp->cspcantidadcombo,
                                    'prmmecanica'        => $csp->prmmecanica,
                                    'cspcantidadplancha' => $csp->cspcantidadplancha,
                                    'csptotalcombo'      => $csp->csptotalcombo,
                                    'csptotalplancha'    => $csp->csptotalplancha,
                                    'csptotal'           => $csp->csptotal,
                                    'cspgratis'          => $csp->cspgratis,
                                    'prmaccion'          => $csp->prmaccion,
                                    'tprnombre'          => $csp->tprnombre,
                                    'cspnuevo'           => $csp->cspnuevo,
                                    'productos'          => $csp->productos,
                                    'productoPrincipal'  => $csp->productoPrincipal,
                                    'productosbonificados' => $csp->productosbonificados,
                                    'fechainicio' => $csp->fechainicio,
                                    'fechafinal' => $csp->fechafinal,
                                );
                            }
                        }





                    }else{
                        $cspcanalessucursalespromociones = [];
                        $nuevoArrayCsp = [];
                    }
                    
                    $csccanalessucursalescategorias[$posicion]['porcentaje'] = (sizeof($cspcanalessucursalespromociones)*$numeroPromocionesTerminadas)/100;
                    $csccanalessucursalescategorias[$posicion]['promocionesOrdenadas'] = $nuevoArrayCsp;
                    $csccanalessucursalescategorias[$posicion]['promociones'] = $cspcanalessucursalespromociones;
                    $csccanalessucursalescategorias[$posicion]['productos'] = $productosCsc;
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


        $contador = 0;

        foreach($datos as $posicionDat => $dat){
            $contadorDat = sizeof($dat['promocionesOrdenadas']);
            $datos[$posicionDat]['cont'] = $contadorDat;

            if($contadorDat > $contador){
                $contador =  $contadorDat;
            }




            // LOGICA LIMPIAR LAS MECANICAS IGUALES Y REEMPLAZAR POR PROMOCIONES EN 0
            $promocionesOrdenadas = $dat['promocionesOrdenadas'];

            $mecanicasUtilizadas = array();

            foreach($promocionesOrdenadas as $posicionPromocionesOrdenadas => $promocionOrdenada){

                if($promocionOrdenada['prmmecanica'] != ""){
                    
                    if(sizeof($mecanicasUtilizadas) > 0){

                        $productos = $promocionOrdenada['productos'];
                        $productoBuscar = "";
                        foreach($productos as $posicionProducto => $producto){
                            if($posicionProducto == 0){
                                $productoBuscar = $producto['prosku'];
                                
                            }
                        }

                        $encontroMecanica = false;

                        foreach($mecanicasUtilizadas as $mecanicaUtilizada){


                            if($mecanicaUtilizada['mecanica'] == $promocionOrdenada['prmmecanica'] && $productoBuscar == $mecanicaUtilizada['sku'] ){
                                $encontroMecanica = true;
                                // $datos[$posicionDat]['promocionesOrdenadas'][$posicionPromocionesOrdenadas] = $promocionVacia;
                                $promocionesOrdenadas[$posicionPromocionesOrdenadas] = $promocionVacia;
                            }


                        }

                        if($encontroMecanica == false){
                            // $mecanicasUtilizadas[] = $promocionOrdenada['prmmecanica'];
                            $mecanicasUtilizadas[] = array(
                                "mecanica" => $promocionOrdenada['prmmecanica'],
                                "sku" => $productoBuscar
                            );
                        }

                    }else{
                        // $mecanicasUtilizadas[] = $promocionOrdenada['prmmecanica'];
                        $productos = $promocionOrdenada['productos'];

                        foreach($productos as $posicionProducto => $producto){
                            if($posicionProducto == 0){
                                $mecanicasUtilizadas[] = array(
                                    "mecanica" => $promocionOrdenada['prmmecanica'],
                                    "sku" => $producto['prosku']
                                );
                            }
                        }
                    }   
                }
            }

            $datos[$posicionDat]['promocionesOrdenadas'] = $promocionesOrdenadas;

        }

        foreach($datos as $contadorDat => $dat){
            
            if($contador > $dat['cont'] ){

                $nuevasPromos = $dat['promocionesOrdenadas'];

                $cuadrarPromos = $contador - $dat['cont'];

                for($i = 0; $i <= $cuadrarPromos; $i++){
                    $fechaInicio = date("m", strtotime($fechaActual));
                    $fechaInicio = "01/".$fechaInicio;
                    $fechaFinal = date("m", strtotime($fechaActual));
                    $fechafinal = "30/".$fechaFinal;

                    $nuevasPromos[] = array(
                        'cspid'              => 0,
                        'prmid'              => "",
                        'prmcodigo'          => "",
                        'cspvalorizado'      => "",
                        'cspplanchas'        => "",
                        'cspcompletado'      => "",
                        'cspcantidadcombo'   => "",
                        'prmmecanica'        => "",
                        'cspcantidadplancha' => "",
                        'csptotalcombo'      => "",
                        'csptotalplancha'    => "",
                        'csptotal'           => "",
                        'cspgratis'          => "",
                        'prmaccion'          => "",
                        'tprnombre'          => "",
                        'cspnuevo'           => "",
                        'productos'          => [],
                        'productoPrincipal'  => "0",
                        'productosbonificados' => [],
                        'fechainicio' => $fechaInicio,
                        'fechafinal'  => $fechafinal,
                    );

                }

                $datos[$contadorDat]['promocionesOrdenadas'] = $nuevasPromos;

            }


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

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

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
                                                'can.canid',
                                                'can.cannombre'
                                            ]);

        $cscsDoble = array();
        
        foreach($cscs as $posicionCsc => $csc){

            $csps = array();
            $cont = 0;

            foreach($sucs as $suc){
                $cspscs = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                    ->join('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
                                                    ->join('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                    ->join('tprtipospromociones as tpr', 'tpr.tprid', 'prm.tprid')
                                                    ->join('fecfechas as fec', 'fec.fecid', 'cspcanalessucursalespromociones.fecid')
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
                                                        'cspgratis',
                                                        'cspiniciopromo',
                                                        'cspfinpromo',
                                                        'fecfecha'
                                                    ]);

                foreach($cspscs as $posicionCspsc => $cspsc){

                    if($cspsc->cspiniciopromo == null){
                        $fechaInicio = date("d/m", strtotime($cspsc->fecfecha));

                        $fechaFinal = date("m", strtotime($cspsc->fecfecha));
                        $fechafinal = "30/".$fechaFinal;
                    }else{
                        $fechaInicio = date("d/m", strtotime($cspsc->cspiniciopromo));

                        $fechaFinal = date("d/m", strtotime($cspsc->cspfinpromo));
                        $fechafinal = $fechaFinal;
                    }

                    $contadorEspecificoCsps = 0;

                    $productoInicialCspscs = "0";
                    $prppromocionesproducto = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
                                                                        ->where('prppromocionesproductos.prmid', $cspsc->prmid )
                                                                        ->first([
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

                    if($prppromocionesproducto){
                        $cspscs[$posicionCspsc]['productoInicial'] = $prppromocionesproducto->prosku;
                        $productoInicialCspscs = $prppromocionesproducto->prosku;
                    }else{
                        $cspscs[$posicionCspsc]['productoInicial'] = "0";
                        $productoInicialCspscs = "0";
                    }

                    if(sizeof($csps) > 0){
                        foreach($csps as $posicionCsp => $csp){


                            $productoInicialCsp = "0";
                            $prppromocionesproducto = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
                                                                                ->where('prppromocionesproductos.prmid', $csp['prmid'] )
                                                                                ->first([
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

                            if($prppromocionesproducto){
                                $csps[$posicionCsp]['productoInicial'] = $prppromocionesproducto->prosku;
                                $productoInicialCsp = $prppromocionesproducto->prosku;
                            }else{
                                $csps[$posicionCsp]['productoInicial'] = "0";
                                $productoInicialCsp = "0";
                            }


                            // if($csp['prmcodigo'] == $cspsc->prmcodigo){
                            if($csp['prmmecanica'] == $cspsc->prmmecanica && $productoInicialCspscs == $productoInicialCsp ){
                            // if($csp['prmmecanica'] == $cspsc->prmmecanica ){

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
                                
                                $csps[$cont]['fechainicio'] = $fechaInicio;
                                $csps[$cont]['fechafinal'] = $fechafinal;

                                $csps[$cont]['prmcodigo'] = $cspsc->prmcodigo;
                                $csps[$cont]['cspcantidadcombo']   = $cantidadComboNuevo;
                                $csps[$cont]['cspcantidadplancha'] = $cantidadPlanchaNuevo;
                                $csps[$cont]['cspcompletado']      = 0;
                                $csps[$cont]['cspgratis']          = $cspsc->cspgratis;
                                $csps[$cont]['cspid']              = 1;
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

                        $csps[$cont]['fechainicio'] = $fechaInicio;
                        $csps[$cont]['fechafinal'] = $fechafinal;

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
                        $csps[$cont]['cspid']              = 1;
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
            $cscs[$posicionCsc]["cantidadPromociones"] = sizeof($csps);
        }

        // usort(
        //     $cscs,
        //     function ($a, $b)  {
        //         if ($a['cantidadPromociones'] > $b['cantidadPromociones']) {
        //             return -1;
        //         } else if ($a['cantidadPromociones'] < $b['cantidadPromociones']) {
        //             return 1;
        //         } else {
        //             return 0;
        //         }
        //     }
        // );

        // $cantidadPromociones = 0;

        // foreach($cscs as $posicionCsc => $csc){

        //     $promociones = $cscs[$posicionCsc]["promociones"];
        //     $cantidadPromociones = sizeof($cscs[$posicionCsc]["promociones"]);

        //     if($cantidadPromociones < $csc['cantidadPromociones']){

        //     }

        //     foreach($cspcanalessucursalespromociones as $posicionCsp => $cspcanalessucursalespromocion){

        //     }
        // }

        $productosCsc = [];
        foreach($cscs as $posicionCsc => $csc){

            $csccanalessucursalescategorias = $cscs;
            $cspcanalessucursalespromociones = $cscs[$posicionCsc]["promociones"];

            $rptaArmarPromociones = $this->ArmarPromociones($cspcanalessucursalespromociones, $productosCsc, $posicionCsc, $csccanalessucursalescategorias);
            $cscs[$posicionCsc]["promocionesOrdenadas"] = $rptaArmarPromociones['nuevoArrayCsp'];
            $productosCsc = $rptaArmarPromociones['productosCsc'];
            $cscs = $rptaArmarPromociones['csccanalessucursalescategorias'];
            // $cscs[$posicionCsc]["promocionesOrdenadas"] = $csps;

            $cscsDoble[] = array(
                "canid" => $csc->canid,
                "cannombre" => $csc->cannombre,
                "cscid" => 0,
                "porcentaje" => 0,
                "promociones" => $csc->promociones,
                "cantidadPromociones" => sizeof($rptaArmarPromociones['nuevoArrayCsp']),
                "promocionesOrdenadas" => $rptaArmarPromociones['nuevoArrayCsp']
            );

        }


        usort(
            $cscsDoble,
            function ($a, $b)  {
                if ($a['cantidadPromociones'] > $b['cantidadPromociones']) {
                    return -1;
                } else if ($a['cantidadPromociones'] < $b['cantidadPromociones']) {
                    return 1;
                } else {
                    return 0;
                }
            }
        );

        $cscs = $cscsDoble;

        $linea          = __LINE__;
        $respuesta      = true;
        $datos          = $cscs;
        $mensaje        = 'Las promociones se cargaron satisfactoriamente';

        $contador = 0;

        foreach($datos as $contDat => $dat){
            $contadorDat = sizeof($dat['promocionesOrdenadas']);
            $datos[$contDat]['cont'] = $contadorDat;

            if($contadorDat > $contador){
                $contador =  $contadorDat;
            }
        }

        foreach($datos as $contadorDat => $dat){
            
            if($contador > $dat['cont'] ){

                $nuevasPromos = $dat['promocionesOrdenadas'];

                $cuadrarPromos = $contador - $dat['cont'];

                for($i = 0; $i <= $cuadrarPromos; $i++){
                    $fechaInicio = date("m", strtotime($fechaActual));
                    $fechaInicio = "01/".$fechaInicio;
                    $fechaFinal = date("m", strtotime($fechaActual));
                    $fechafinal = "30/".$fechaFinal;

                    $nuevasPromos[] = array(
                        'cspid'              => 0,
                        'prmid'              => "",
                        'prmcodigo'          => "",
                        'cspvalorizado'      => "",
                        'cspplanchas'        => "",
                        'cspcompletado'      => "",
                        'cspcantidadcombo'   => "",
                        'prmmecanica'        => "",
                        'cspcantidadplancha' => "",
                        'csptotalcombo'      => "",
                        'csptotalplancha'    => "",
                        'csptotal'           => "",
                        'cspgratis'          => "",
                        'prmaccion'          => "",
                        'tprnombre'          => "",
                        'cspnuevo'           => "",
                        'productos'          => [],
                        'productoPrincipal'  => "0",
                        'productosbonificados' => [],
                        'fechainicio' => $fechaInicio,
                        'fechafinal'  => $fechafinal,
                    );

                }

                $datos[$contadorDat]['promocionesOrdenadas'] = $nuevasPromos;

            }
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

    public function ArmarPromociones($cspcanalessucursalespromociones, $productosCsc, $posicion, $csccanalessucursalescategorias)
    {

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

        $act_fechaInicio = date("m", strtotime($fechaActual));
        $act_fechaInicio = "01/".$act_fechaInicio;
        $act_fechaFinal = date("m", strtotime($fechaActual));
        $act_fechafinal = "30/".$act_fechaFinal;

        $nuevoArrayCsp = array();
                    
        $numeroPromocionesTerminadas = 0;

        if(sizeof($cspcanalessucursalespromociones) > 0){

            $productosCscMomento = [];

            foreach($cspcanalessucursalespromociones as $posicionPromociones => $cspcanalesucursalpromocion){

                $prppromocionesproductos = $cspcanalessucursalespromociones[$posicionPromociones]['productos'];

                if(sizeof($prppromocionesproductos) > 0){
                    $cspcanalessucursalespromociones[$posicionPromociones]['productos'] = $prppromocionesproductos;

                    if($posicion == 0){
                        $productosCsc[] = $prppromocionesproductos[0]['prosku'];
                    }else{
                        $productosCscMomento[] = $prppromocionesproductos[0]['prosku'];
                    }

                    $cspcanalessucursalespromociones[$posicionPromociones]['productoPrincipal'] = $prppromocionesproductos[0]['prosku'];

                }else{
                    $cspcanalessucursalespromociones[$posicionPromociones]['productos'] = [];
                }


                $prbpromocionesbonificaciones = $cspcanalessucursalespromociones[$posicionPromociones]['productosbonificados'];
                
                if(sizeof($prbpromocionesbonificaciones) > 0){
                    $cspcanalessucursalespromociones[$posicionPromociones]['productosbonificados'] = $prbpromocionesbonificaciones;
                }else{
                    $cspcanalessucursalespromociones[$posicionPromociones]['productosbonificados'] = [];
                }
            }


            if($posicion != 0){

                $productosDuplicados = [];

                foreach ($productosCsc as $posicionProductoCsc => $productoCsc) {
                    
                    $encontroProducto = false;

                    foreach ($cspcanalessucursalespromociones as $posicionCspDos => $csp) {
                        
                        if($cspcanalessucursalespromociones[$posicionCspDos]['productoPrincipal'] == $productoCsc){
                            $encontroProducto = true;
                            $nuevoArrayCsp[] = array(
                                'cspid'              => 1,
                                'prmid'              => $cspcanalessucursalespromociones[$posicionCspDos]['prmid'],
                                'prmcodigo'          => $cspcanalessucursalespromociones[$posicionCspDos]['prmcodigo'],
                                'cspvalorizado'      => $cspcanalessucursalespromociones[$posicionCspDos]['cspvalorizado'],
                                'cspplanchas'        => $cspcanalessucursalespromociones[$posicionCspDos]['cspplanchas'],
                                'cspcompletado'      => $cspcanalessucursalespromociones[$posicionCspDos]['cspcompletado'],
                                'cspcantidadcombo'   => $cspcanalessucursalespromociones[$posicionCspDos]['cspcantidadcombo'],
                                'prmmecanica'        => $cspcanalessucursalespromociones[$posicionCspDos]['prmmecanica'],
                                'cspcantidadplancha' => $cspcanalessucursalespromociones[$posicionCspDos]['cspcantidadplancha'],
                                'csptotalcombo'      => $cspcanalessucursalespromociones[$posicionCspDos]['csptotalcombo'],
                                'csptotalplancha'    => $cspcanalessucursalespromociones[$posicionCspDos]['csptotalplancha'],
                                'csptotal'           => $cspcanalessucursalespromociones[$posicionCspDos]['csptotal'],
                                'cspgratis'          => $cspcanalessucursalespromociones[$posicionCspDos]['cspgratis'],
                                'prmaccion'          => $cspcanalessucursalespromociones[$posicionCspDos]['prmaccion'],
                                'tprnombre'          => $cspcanalessucursalespromociones[$posicionCspDos]['tprnombre'],
                                'cspnuevo'           => $cspcanalessucursalespromociones[$posicionCspDos]['cspnuevo'],
                                'productos'          => $cspcanalessucursalespromociones[$posicionCspDos]['productos'],
                                'productoPrincipal'  => $cspcanalessucursalespromociones[$posicionCspDos]['productoPrincipal'],
                                'productosbonificados' => $cspcanalessucursalespromociones[$posicionCspDos]['productosbonificados'],
                                'fechainicio'          => $cspcanalessucursalespromociones[$posicionCspDos]['fechainicio'],
                                'fechafinal'           => $cspcanalessucursalespromociones[$posicionCspDos]['fechafinal'],
                            );


                            $encontroProductoDuplicado = false;

                            foreach($productosDuplicados as $productoDuplicado){
                                if($productoDuplicado == $productoCsc){
                                    $encontroProductoDuplicado = true;
                                    
                                    $nuevoArrayCspDuplicado = array();
                                    $nuevoArrayCspn = $csccanalessucursalescategorias[0]['promocionesOrdenadas'];
                                    $productosMasDuplicados = "";

                                    foreach($nuevoArrayCspn as $posicionNuevInv => $nuevoArrayCspInv){
                                        if($nuevoArrayCspn[$posicionNuevInv]['productoPrincipal'] == $productoDuplicado){

                                            $nuevoArrayCspDuplicado[] = array(
                                                'cspid'              => 1,
                                                'prmid'              => $nuevoArrayCspn[$posicionNuevInv]['prmid'],
                                                'prmcodigo'          => $nuevoArrayCspn[$posicionNuevInv]['prmcodigo'],
                                                'cspvalorizado'      => $nuevoArrayCspn[$posicionNuevInv]['cspvalorizado'],
                                                'cspplanchas'        => $nuevoArrayCspn[$posicionNuevInv]['cspplanchas'],
                                                'cspcompletado'      => $nuevoArrayCspn[$posicionNuevInv]['cspcompletado'],
                                                'cspcantidadcombo'   => $nuevoArrayCspn[$posicionNuevInv]['cspcantidadcombo'],
                                                'prmmecanica'        => $nuevoArrayCspn[$posicionNuevInv]['prmmecanica'],
                                                'cspcantidadplancha' => $nuevoArrayCspn[$posicionNuevInv]['cspcantidadplancha'],
                                                'csptotalcombo'      => $nuevoArrayCspn[$posicionNuevInv]['csptotalcombo'],
                                                'csptotalplancha'    => $nuevoArrayCspn[$posicionNuevInv]['csptotalplancha'],
                                                'csptotal'           => $nuevoArrayCspn[$posicionNuevInv]['csptotal'],
                                                'cspgratis'          => $nuevoArrayCspn[$posicionNuevInv]['cspgratis'],
                                                'prmaccion'          => $nuevoArrayCspn[$posicionNuevInv]['prmaccion'],
                                                'tprnombre'          => $nuevoArrayCspn[$posicionNuevInv]['tprnombre'],
                                                'cspnuevo'           => $nuevoArrayCspn[$posicionNuevInv]['cspnuevo'],
                                                'productos'          => $nuevoArrayCspn[$posicionNuevInv]['productos'],
                                                'productoPrincipal'  => $nuevoArrayCspn[$posicionNuevInv]['productoPrincipal'],
                                                'productosbonificados' => $nuevoArrayCspn[$posicionNuevInv]['productosbonificados'],
                                                'fechainicio'          => $nuevoArrayCspn[$posicionNuevInv]['fechainicio'],
                                                'fechafinal'           => $nuevoArrayCspn[$posicionNuevInv]['fechafinal'],
                                            );

                                            if(sizeof($nuevoArrayCspn)-1 >=  $posicionNuevInv+1 ){
                                                if($nuevoArrayCspn[$posicionNuevInv+1]['productoPrincipal'] == $productoDuplicado){

                                                    $productosMasDuplicados = $productoDuplicado;
    
                                                }else{
                                                    if($productosMasDuplicados != $productoDuplicado){
                                                        $nuevoArrayCspDuplicado[] = array(
                                                            'cspid'              => 0,
                                                            'prmid'              => "",
                                                            'prmcodigo'          => "",
                                                            'cspvalorizado'      => "",
                                                            'cspplanchas'        => "",
                                                            'cspcompletado'      => "",
                                                            'cspcantidadcombo'   => "",
                                                            'prmmecanica'        => "",
                                                            'cspcantidadplancha' => "",
                                                            'csptotalcombo'      => "",
                                                            'csptotalplancha'    => "",
                                                            'csptotal'           => "",
                                                            'cspgratis'          => "",
                                                            'prmaccion'          => "",
                                                            'tprnombre'          => "",
                                                            'cspnuevo'           => "",
                                                            'productos'          => [],
                                                            'productoPrincipal'  => $productoCsc,
                                                            'productosbonificados' => [],
                                                            'fechainicio'          => $act_fechaInicio,
                                                            'fechafinal'           => $act_fechafinal,
                    
                                                        );
                                                    }
                                                }
                                            }

                                        }else{
                                            $nuevoArrayCspDuplicado[] = array(
                                                'cspid'              => 1,
                                                'prmid'              => $nuevoArrayCspn[$posicionNuevInv]['prmid'],
                                                'prmcodigo'          => $nuevoArrayCspn[$posicionNuevInv]['prmcodigo'],
                                                'cspvalorizado'      => $nuevoArrayCspn[$posicionNuevInv]['cspvalorizado'],
                                                'cspplanchas'        => $nuevoArrayCspn[$posicionNuevInv]['cspplanchas'],
                                                'cspcompletado'      => $nuevoArrayCspn[$posicionNuevInv]['cspcompletado'],
                                                'cspcantidadcombo'   => $nuevoArrayCspn[$posicionNuevInv]['cspcantidadcombo'],
                                                'prmmecanica'        => $nuevoArrayCspn[$posicionNuevInv]['prmmecanica'],
                                                'cspcantidadplancha' => $nuevoArrayCspn[$posicionNuevInv]['cspcantidadplancha'],
                                                'csptotalcombo'      => $nuevoArrayCspn[$posicionNuevInv]['csptotalcombo'],
                                                'csptotalplancha'    => $nuevoArrayCspn[$posicionNuevInv]['csptotalplancha'],
                                                'csptotal'           => $nuevoArrayCspn[$posicionNuevInv]['csptotal'],
                                                'cspgratis'          => $nuevoArrayCspn[$posicionNuevInv]['cspgratis'],
                                                'prmaccion'          => $nuevoArrayCspn[$posicionNuevInv]['prmaccion'],
                                                'tprnombre'          => $nuevoArrayCspn[$posicionNuevInv]['tprnombre'],
                                                'cspnuevo'           => $nuevoArrayCspn[$posicionNuevInv]['cspnuevo'],
                                                'productos'          => $nuevoArrayCspn[$posicionNuevInv]['productos'],
                                                'productoPrincipal'  => $nuevoArrayCspn[$posicionNuevInv]['productoPrincipal'],
                                                'productosbonificados' => $nuevoArrayCspn[$posicionNuevInv]['productosbonificados'],
                                                'fechainicio'          => $nuevoArrayCspn[$posicionNuevInv]['fechainicio'],
                                                'fechafinal'           => $nuevoArrayCspn[$posicionNuevInv]['fechafinal'],
                                            );
                                        }
                                    }

                                    $csccanalessucursalescategorias[0]['promocionesOrdenadas'] = $nuevoArrayCspDuplicado;
                                }
                            }

                            if($encontroProductoDuplicado == false){
                                $productosDuplicados[] = $productoCsc;
                            }

                        }

                    }

                    if($encontroProducto == false){
                        $nuevoArrayCsp[] = array(
                            'cspid'              => 0,
                            'prmid'              => "",
                            'prmcodigo'          => "",
                            'cspvalorizado'      => "",
                            'cspplanchas'        => "",
                            'cspcompletado'      => "",
                            'cspcantidadcombo'   => "",
                            'prmmecanica'        => "",
                            'cspcantidadplancha' => "",
                            'csptotalcombo'      => "",
                            'csptotalplancha'    => "",
                            'csptotal'           => "",
                            'cspgratis'          => "",
                            'prmaccion'          => "",
                            'tprnombre'          => "",
                            'cspnuevo'           => "",
                            'productos'          => [],
                            'productoPrincipal'  => $productoCsc,
                            'productosbonificados' => [],
                            'fechainicio'          => $act_fechaInicio,
                            'fechafinal'           => $act_fechafinal,

                        );
                    }
                }

                foreach ($cspcanalessucursalespromociones as $posicionCspDos => $csp) {
                    $encontroProductoMomento = false;
                    foreach ($productosCsc as $key => $productoCsc) {
                        if($cspcanalessucursalespromociones[$posicionCspDos]['productoPrincipal'] == $productoCsc){
                            $encontroProductoMomento = true;
                        }
                    }

                    $productoPrincipal = "0";

                    if(isset($cspcanalessucursalespromociones[$posicionCspDos]['productoPrincipal'])){
                        $productoPrincipal = $cspcanalessucursalespromociones[$posicionCspDos]['productoPrincipal'];
                    }

                    if($encontroProductoMomento == false){
                        $nuevoArrayCsp[] = array(
                            'cspid'              => 1,
                            'prmid'              => $cspcanalessucursalespromociones[$posicionCspDos]['prmid'],
                            'prmcodigo'          => $cspcanalessucursalespromociones[$posicionCspDos]['prmcodigo'],
                            'cspvalorizado'      => $cspcanalessucursalespromociones[$posicionCspDos]['cspvalorizado'],
                            'cspplanchas'        => $cspcanalessucursalespromociones[$posicionCspDos]['cspplanchas'],
                            'cspcompletado'      => $cspcanalessucursalespromociones[$posicionCspDos]['cspcompletado'],
                            'cspcantidadcombo'   => $cspcanalessucursalespromociones[$posicionCspDos]['cspcantidadcombo'],
                            'prmmecanica'        => $cspcanalessucursalespromociones[$posicionCspDos]['prmmecanica'],
                            'cspcantidadplancha' => $cspcanalessucursalespromociones[$posicionCspDos]['cspcantidadplancha'],
                            'csptotalcombo'      => $cspcanalessucursalespromociones[$posicionCspDos]['csptotalcombo'],
                            'csptotalplancha'    => $cspcanalessucursalespromociones[$posicionCspDos]['csptotalplancha'],
                            'csptotal'           => $cspcanalessucursalespromociones[$posicionCspDos]['csptotal'],
                            'cspgratis'          => $cspcanalessucursalespromociones[$posicionCspDos]['cspgratis'],
                            'prmaccion'          => $cspcanalessucursalespromociones[$posicionCspDos]['prmaccion'],
                            'tprnombre'          => $cspcanalessucursalespromociones[$posicionCspDos]['tprnombre'],
                            'cspnuevo'           => $cspcanalessucursalespromociones[$posicionCspDos]['cspnuevo'],
                            'productos'          => $cspcanalessucursalespromociones[$posicionCspDos]['productos'],
                            'productoPrincipal'  => $productoPrincipal,
                            'productosbonificados' => $cspcanalessucursalespromociones[$posicionCspDos]['productosbonificados'],
                            'fechainicio'          => $cspcanalessucursalespromociones[$posicionCspDos]['fechainicio'],
                            'fechafinal'           => $cspcanalessucursalespromociones[$posicionCspDos]['fechafinal'],

                        );
                    }

                }

            }else{
                // $nuevoArrayCsp = $cspcanalessucursalespromociones;

                foreach ($cspcanalessucursalespromociones as $posicionCsp => $csp) {

                    $productoPrincipal = "0";

                    if(isset($cspcanalessucursalespromociones[$posicionCsp]['productoPrincipal'])){
                        $productoPrincipal = $cspcanalessucursalespromociones[$posicionCsp]['productoPrincipal'];
                    }


                    $nuevoArrayCsp[] = array(
                        'cspid'              => 1,
                        'prmid'              => $cspcanalessucursalespromociones[$posicionCsp]['prmid'],
                        'prmcodigo'          => $cspcanalessucursalespromociones[$posicionCsp]['prmcodigo'],
                        'cspvalorizado'      => $cspcanalessucursalespromociones[$posicionCsp]['cspvalorizado'],
                        'cspplanchas'        => $cspcanalessucursalespromociones[$posicionCsp]['cspplanchas'],
                        'cspcompletado'      => $cspcanalessucursalespromociones[$posicionCsp]['cspcompletado'],
                        'cspcantidadcombo'   => $cspcanalessucursalespromociones[$posicionCsp]['cspcantidadcombo'],
                        'prmmecanica'        => $cspcanalessucursalespromociones[$posicionCsp]['prmmecanica'],
                        'cspcantidadplancha' => $cspcanalessucursalespromociones[$posicionCsp]['cspcantidadplancha'],
                        'csptotalcombo'      => $cspcanalessucursalespromociones[$posicionCsp]['csptotalcombo'],
                        'csptotalplancha'    => $cspcanalessucursalespromociones[$posicionCsp]['csptotalplancha'],
                        'csptotal'           => $cspcanalessucursalespromociones[$posicionCsp]['csptotal'],
                        'cspgratis'          => $cspcanalessucursalespromociones[$posicionCsp]['cspgratis'],
                        'prmaccion'          => $cspcanalessucursalespromociones[$posicionCsp]['prmaccion'],
                        'tprnombre'          => $cspcanalessucursalespromociones[$posicionCsp]['tprnombre'],
                        'cspnuevo'           => $cspcanalessucursalespromociones[$posicionCsp]['cspnuevo'],
                        'productos'          => $cspcanalessucursalespromociones[$posicionCsp]['productos'],
                        'productoPrincipal'  => $productoPrincipal,
                        'productosbonificados' => $cspcanalessucursalespromociones[$posicionCsp]['productosbonificados'],
                        'fechainicio'          => $cspcanalessucursalespromociones[$posicionCsp]['fechainicio'],
                        'fechafinal'           => $cspcanalessucursalespromociones[$posicionCsp]['fechafinal'],
                    );
                }
            }





        }else{
            $cspcanalessucursalespromociones = [];
            $nuevoArrayCsp = [];
        }


        return array(
            "productosCsc" => $productosCsc,
            "nuevoArrayCsp" => $nuevoArrayCsp,
            "csccanalessucursalescategorias" => $csccanalessucursalescategorias
        ); 
    }
}
