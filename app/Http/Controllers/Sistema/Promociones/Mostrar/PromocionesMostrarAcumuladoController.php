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

class PromocionesMostrarAcumuladoController extends Controller
{
    public function PromocionesMostrarAcumulado(Request $request)
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

        $re_aplicandoFiltroCanal = $request['aplicandoFiltroCanal'];
        $re_aplicandoFiltroDt    = $request['aplicandoFiltroDt'];
        $re_aplicandoFiltroGrupo = $request['aplicandoFiltroGrupo'];
        $re_aplicandoFiltroZona  = $request['aplicandoFiltroZona'];
        $re_cass                 = $request['cass'];
        $re_gsus                 = $request['gsus'];
        $re_sucursalesUsuario    = $request['sucursalesUsuario'];
        $re_zonas                = $request['zonas'];

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;


        $sucs = sucsucursales::where(function ($query) use($zonid, $gsuid, $casid, $re_aplicandoFiltroCanal, $re_aplicandoFiltroDt, $re_aplicandoFiltroGrupo, $re_aplicandoFiltroZona, $re_cass, $re_gsus, $re_sucursalesUsuario, $re_zonas) {

                                    if($re_aplicandoFiltroZona == true){
                                        foreach($re_zonas as $re_zona){
                                            if($re_zona['check'] == true){
                                                $query->orwhere('zonid', $re_zona['zonid']);
                                            }
                                        }
                                    }else if($re_aplicandoFiltroCanal == true){
                                        foreach($re_cass as $re_cas){
                                            if($re_cas['check'] == true){
                                                $query->orwhere('casid', $re_cas['casid']);
                                            }
                                        }
                                    }else if($re_aplicandoFiltroGrupo == true){
                                        foreach($re_gsus as $re_gsu){
                                            if($re_gsu['check'] == true){
                                                $query->orwhere('gsuid', $re_gsu['gsuid']);
                                            }
                                        }
                                    }else if($re_aplicandoFiltroDt == true){
                                        foreach($re_sucursalesUsuario as $re_sucursalUsuario){
                                            if($re_sucursalUsuario['check'] == true){
                                                $query->orwhere('sucid', $re_sucursalUsuario['sucid']);
                                            }
                                        }
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
                                            ->where(function ($query) use($zonid, $gsuid, $casid, $re_aplicandoFiltroCanal, $re_aplicandoFiltroDt, $re_aplicandoFiltroGrupo, $re_aplicandoFiltroZona, $re_cass, $re_gsus, $re_sucursalesUsuario, $re_zonas) {

                                                if($re_aplicandoFiltroZona == true){
                                                    foreach($re_zonas as $re_zona){
                                                        if($re_zona['check'] == true){
                                                            $query->orwhere('suc.zonid', $re_zona['zonid']);
                                                        }
                                                    }
                                                }else if($re_aplicandoFiltroCanal == true){
                                                    foreach($re_cass as $re_cas){
                                                        if($re_cas['check'] == true){
                                                            $query->orwhere('suc.casid', $re_cas['casid']);
                                                        }
                                                    }
                                                }else if($re_aplicandoFiltroGrupo == true){
                                                    foreach($re_gsus as $re_gsu){
                                                        if($re_gsu['check'] == true){
                                                            $query->orwhere('suc.gsuid', $re_gsu['gsuid']);
                                                        }
                                                    }
                                                }else if($re_aplicandoFiltroDt == true){
                                                    foreach($re_sucursalesUsuario as $re_sucursalUsuario){
                                                        if($re_sucursalUsuario['check'] == true){
                                                            $query->orwhere('suc.sucid', $re_sucursalUsuario['sucid']);
                                                        }
                                                    }
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
                                                        'fecfecha',
                                                        'cspnuevapromocion'
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
                                $csps[$cont]['cspnuevapromocion']  = $cspsc->cspnuevapromocion;

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
                        $csps[$cont]['cspnuevapromocion'] = $cspsc->cspnuevapromocion;

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







        // ORDENAR LAS PROMOS POR PRODUCTO 23/12

        
        $dataPrueba = array();

        foreach($cscs as $csc){
            $dataPrueba[] = $csc;
        }

        if(sizeof($dataPrueba) > 0){
            usort(
                $dataPrueba,
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
        }

        $arrProductosTotal = array();
        
        foreach($dataPrueba as $posicionDatPrueba => $datPrueba){
            
            $promociones = $datPrueba['promociones'];

            $arrProductos = array();

            foreach($promociones as $posicionPromocion => $promocion){

                $productos = $promocion['productos'];
                $productoSeleccionado = 0;

                foreach($productos as $posicionProducto => $producto){

                    if($posicionProducto == 0){
                        $productoSeleccionado = $producto['prosku'];
                    }

                }

                if(sizeof($arrProductos) > 0){
                    $encontroProducto = false;
                    foreach($arrProductos as $posicionArr => $arrProducto){
                        if($arrProducto['sku'] == $productoSeleccionado){

                            $arrProductos[$posicionArr]['cantidad'] = $arrProductos[$posicionArr]['cantidad'] + 1;

                            $encontroProducto = true;
                        }
                    }
                    
                    if($encontroProducto == false){
                        $arrProductos[] = array(
                            "sku" => $productoSeleccionado,
                            "cantidad" => 1
                        );
                    }

                }else{
                    $arrProductos[] = array(
                        "sku" => $productoSeleccionado,
                        "cantidad" => 1
                    );
                }


            }

            $dataPrueba[$posicionDatPrueba]['arrProductos'] = $arrProductos;

        }

        usort(
            $arrProductosTotal,
            function ($a, $b)  {
                if ($a['cantidad'] < $b['cantidad']) {
                    return -1;
                } else if ($a['cantidad'] > $b['cantidad']) {
                    return 1;
                } else {
                    return 0;
                }
            }
        );

        foreach($dataPrueba as $posicionDatPrueba => $datPrueba){

            $arrProductos = $datPrueba['arrProductos'];
            
            foreach($arrProductos as $arrProducto){
                if(sizeof($arrProductosTotal) > 0){
                    
                    $encontroProducto = false;

                    foreach($arrProductosTotal as $posicionArrProductoTotal => $arrProductoTotal){
                        if($arrProductoTotal['sku'] == $arrProducto['sku']){
                            if($arrProducto['cantidad'] > $arrProductoTotal['cantidad']){
                                $arrProductosTotal[$posicionArrProductoTotal]['cantidad'] = $arrProducto['cantidad'];
                            }
                            $encontroProducto = true;
                        }
                    }

                    if($encontroProducto == false){
                        $arrProductosTotal[] = array(
                            "sku"      => $arrProducto['sku'],
                            "cantidad" => $arrProducto['cantidad']
                        );
                    }

                }else{
                    $arrProductosTotal[] = array(
                        "sku" => $arrProducto['sku'],
                        "cantidad" => $arrProducto['cantidad']
                    );
                }
            }

        }

        $fechaInicio = date("m", strtotime($fechaActual));
        $fechaInicio = "01/".$fechaInicio;
        $fechaFinal = date("m", strtotime($fechaActual));
        $fechafinal = "30/".$fechaFinal;

        $promoVacia = array(
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
            'cspnuevapromocion'  => false,
        );

        foreach($dataPrueba as $posicionDatPrueba => $datPrueba){

            $promociones = $datPrueba['promociones'];
            $nuevasPromos = array();

            foreach($arrProductosTotal as $arrProductoTotal){
                
                $promocionesEncontradas = 0;

                foreach($promociones as $posicionPromocion => $promocion){

                    $productoSeleccionado = 0;
                    $productos = $promocion['productos'];

                    foreach($productos as $posicionProducto => $producto){

                        if($posicionProducto == 0){
                            $productoSeleccionado = $producto['prosku'];
                        }

                    }


                    if($productoSeleccionado == $arrProductoTotal['sku']){
                        $promocionesEncontradas = $promocionesEncontradas + 1;
                        $nuevasPromos[] = $promocion;
                    }

                }

                $diferenciaPromocionesEncontradas = $arrProductoTotal['cantidad'] - $promocionesEncontradas;

                if( $diferenciaPromocionesEncontradas != 0){
                    for($i = 0; $i < $diferenciaPromocionesEncontradas; $i++){
                        $nuevasPromos[] = $promoVacia;
                    }
                }

            }
            
            $dataPrueba[$posicionDatPrueba]['promociones'] = $nuevasPromos;
            $dataPrueba[$posicionDatPrueba]['promocionesOrdenadas'] = $nuevasPromos;

        }



        //FIN ORDENAR LAS PROMOS POR PRODUCTO 23/12


        // CALCULAR EL NUMERO DE PROMOCIOENS NUEVAS

        foreach($dataPrueba as $posicionDatPrueba => $datPrueba){

            $numeroPromocionesNuevas = 0;

            foreach($datPrueba['promocionesOrdenadas'] as $promocionesOrdenadas){
                if($promocionesOrdenadas['cspnuevapromocion'] == true){
                    $numeroPromocionesNuevas = $numeroPromocionesNuevas + 1;
                }
            }

            $dataPrueba[$posicionDatPrueba]['cantidadPromocionesNuevas'] = $numeroPromocionesNuevas;
        }







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
                        'cspnuevapromocion'  => false,
                    );

                }

                $datos[$contadorDat]['promocionesOrdenadas'] = $nuevasPromos;

            }
        }
        
        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            // 'datos'          => $datos,
            'datos'          => $dataPrueba,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            'dataPrueba'     => $dataPrueba,
            'arrProductosTotal' => $arrProductosTotal,
        ]);
        
        return $requestsalida;
    }
}
