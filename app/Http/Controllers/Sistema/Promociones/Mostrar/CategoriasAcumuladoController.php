<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\scasucursalescategorias;
use App\catcategorias;
use App\carcargasarchivos;
use App\csccanalessucursalescategorias;


class CategoriasAcumuladoController extends Controller
{
    public function CategoriasAcumulado(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
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

        try{

            $categorias = catcategorias::where('catid', '!=', 7)-get();

            foreach($categorias as $cat){
                $scasucursalescategorias = scasucursalescategorias::join('fecfechas as fec', 'scasucursalescategorias.fecid', 'fec.fecid')
                                                                ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
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
                                                                ->where('fec.fecano', $ano)
                                                                ->where('fec.fecmes', $mes)
                                                                ->where('fec.fecdia', $dia)
                                                                ->where('scasucursalescategorias.tsuid', null)
                                                                ->where('scasucursalescategorias.catid', $cat->catid)
                                                                ->get();

                $numeroPromociones = 0;
                $numeroPromocionesNuevas = 0;
                $numeroCodigosPromociones = 0;
                $numeroCodigosPromocionesNuevas = 0;
                $numeroCanales = 0;
                
                $promociones = [];
                $canales = [];

                foreach($scasucursalescategorias as $sca){
                    $cscs = csccanalessucursalescategorias::join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                                            ->join('prmpromociones as prm', 'prm.prmid', 'csp.prmid')
                                                            ->join('cancanales as can', 'can.canid', 'csccanalessucursalescategorias.canid')
                                                            ->where('csccanalessucursalescategorias.scaid', $sca->scaid)
                                                            // ->where('csp.cspcantidadcombo', "!=", 0)
                                                            ->where('csp.cspcantidadplancha', "!=", "0")
                                                            ->where('csp.cspestado', 1)
                                                            ->get([
                                                                'prmcodigo',
                                                                'can.cannombre',
                                                                'prmmecanica',
                                                                'cspnuevapromocion'
                                                            ]);

                    foreach($cscs as $csc){
                        if(sizeof($promociones) > 0){
                            foreach($promociones as $posicionPromocion => $promocion){
                                // if($promocion == $csc->prmcodigo){
                                if($promocion == $csc->prmmecanica){
                                    break;
                                }

                                if($posicionPromocion+1 == sizeof($promociones)){
                                    // $promociones[] = $csc->prmcodigo;
                                    $promociones[] = $csc->prmmecanica;
                                    $numeroCodigosPromociones = $numeroCodigosPromociones+1;

                                    if($csc->cspnuevapromocion == true){
                                        $numeroCodigosPromocionesNuevas = $numeroCodigosPromocionesNuevas + 1;
                                    }

                                }
                            }
                        }else{
                            // $promociones[] = $csc->prmcodigo;
                            $promociones[] = $csc->prmmecanica;
                            $numeroCodigosPromociones = $numeroCodigosPromociones+1;

                            if($csc->cspnuevapromocion == true){
                                $numeroCodigosPromocionesNuevas = $numeroCodigosPromocionesNuevas + 1;
                            }
                        }

                        if(sizeof($canales) > 0){
                            foreach($canales as $posicionCanal => $canal){
                                if($canal == $csc->cannombre){
                                    break;
                                }

                                if($posicionCanal+1 == sizeof($canales)){
                                    $canales[] = $csc->cannombre;
                                    $numeroCanales = $numeroCanales+1;
                                }
                            }
                        }else{
                            $canales[] = $csc->cannombre;
                            $numeroCanales = $numeroCanales+1;
                        }

                    }

                    $countCsc = csccanalessucursalescategorias::join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                                            ->where('csccanalessucursalescategorias.scaid', $sca->scaid)
                                                            // ->where('csp.cspcantidadcombo', "!=", 0)
                                                            ->where('csp.cspcantidadplancha', "!=", "0")
                                                            ->where('csp.cspestado', 1)
                                                            ->count();

                    $countCscNuevas = csccanalessucursalescategorias::join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                                            ->join('prmpromociones as prm', 'prm.prmid', 'csp.prmid')
                                                            ->where('csccanalessucursalescategorias.scaid', $sca->scaid)
                                                            // ->where('csp.cspcantidadcombo', "!=", 0)
                                                            ->where('csp.cspcantidadplancha', "!=", "0")
                                                            ->where('csp.cspestado', 1)
                                                            ->where('csp.cspnuevapromocion', 1)
                                                            ->distinct('prmmecanica')
                                                            ->count();

                    $numeroPromociones = $numeroPromociones + $countCsc;
                    $numeroPromocionesNuevas = $numeroPromocionesNuevas + $countCscNuevas;
                }

                


                $nuevoArray = array(
                    "scaid"                      => $cat->catid,
                    "catid"                      => $cat->catid,
                    "catnombre"                  => $cat->catnombre,
                    "catimagenfondo"             => $cat->catimagenfondo,
                    "catimagenfondoseleccionado" => $cat->catimagenfondoseleccionado,
                    "catimagenfondoopaco"        => $cat->catimagenfondoopaco,
                    "caticono"                   => $cat->caticono,
                    "caticonohover"              => $cat->caticonohover,
                    "catcolorhover"              => $cat->catcolorhover,
                    "catcolor"                   => $cat->catcolor,
                    "caticonoseleccionado"       => $cat->caticonoseleccionado,
                    "fecfecha"                   => "",
                    "cantidadPromociones"        => $numeroPromociones,
                    "cantidadPromocionesNuevas"  => $numeroPromocionesNuevas,
                    "cantidadCodigosPromocion"   => $numeroCodigosPromociones,
                    "cantidadCodigosPromocionNuevas"   => $numeroCodigosPromocionesNuevas,
                    "cantidadCanales"            => $numeroCanales,
                );

                $datos[] = $nuevoArray;
            }

            $linea          = __LINE__;
            $respuesta      = true;
            $mensaje        = 'Las categorias fueron cargadas satisfactoriamente.';
            $mensajeDetalle = sizeof($datos).' registros encontrados.';
            
        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }


        if(sizeof($datos) > 0){
            usort(
                $datos,
                function ($a, $b)  {
                    if ($a['cantidadCodigosPromocion'] > $b['cantidadCodigosPromocion']) {
                        return -1;
                    } else if ($a['cantidadCodigosPromocion'] < $b['cantidadCodigosPromocion']) {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            );
        }

        // OBTENER RESUMEN
        $arr_resumenPromociones = array();
        foreach($datos as $dato){
            $arr_resumenPromociones[] = array(
                "catnombre" => $dato['catnombre'],
                "total"   => doubleval($dato['cantidadCodigosPromocion']),
                "nueva"   => doubleval($dato['cantidadPromocionesNuevas']),
                "regular" => doubleval($dato['cantidadCodigosPromocion']) - doubleval($dato['cantidadPromocionesNuevas'])
            );
        }

        return response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'arr_resumenPromociones' => $arr_resumenPromociones,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            'fechaActualiza' => ""
        ]);
    }
}
