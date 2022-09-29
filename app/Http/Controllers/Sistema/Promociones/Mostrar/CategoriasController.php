<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\scasucursalescategorias;
use App\catcategorias;
use App\carcargasarchivos;
use App\csccanalessucursalescategorias;

class CategoriasController extends Controller
{
    public function mostrarCategorias(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
        $dia        = "01";
        $mes        = $request['mes'];
        $ano        = $request['ano'];
        
        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{

            $scasucursalescategorias = scasucursalescategorias::join('fecfechas as fec', 'scasucursalescategorias.fecid', 'fec.fecid')
                                                                ->join('catcategorias as cat', 'cat.catid', 'scasucursalescategorias.catid')
                                                                ->where('scasucursalescategorias.sucid', $sucid)
                                                                ->where('fec.fecano', $ano)
                                                                ->where('fec.fecmes', $mes)
                                                                ->where('fec.fecdia', $dia)
                                                                ->where('scasucursalescategorias.tsuid', null)
                                                                ->where('cat.catid', '!=', 7)
                                                                ->orderBy('cat.catid')
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
                $categorias = catcategorias::all();
                foreach($categorias as $poscioncat => $categoria){
                    foreach($scasucursalescategorias as $posicionsca => $sca){
                        if($categorias[$poscioncat]['catnombre'] == $scasucursalescategorias[$posicionsca]['catnombre']){

                            $numeroPromociones = csccanalessucursalescategorias::join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                                            ->where('csccanalessucursalescategorias.scaid', $scasucursalescategorias[$posicionsca]['scaid'])
                                                            // ->where('csp.cspcantidadcombo', "!=", 0)
                                                            ->where('csp.cspcantidadplancha', "!=", "0")
                                                            ->where('csp.cspestado', 1)
                                                            ->count();

                            $numeroPromocionesNueva = csccanalessucursalescategorias::join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                                            ->where('csccanalessucursalescategorias.scaid', $scasucursalescategorias[$posicionsca]['scaid'])
                                                            // ->where('csp.cspcantidadcombo', "!=", 0)
                                                            ->where('csp.cspcantidadplancha', "!=", "0")
                                                            ->where('csp.cspestado', 1)
                                                            ->where('csp.cspnuevapromocion', 1)
                                                            ->count();

                            $scasucursalescategorias[$posicionsca]['cantidadPromociones'] = $numeroPromociones;
                            $scasucursalescategorias[$posicionsca]['cantidadPromocionesNuevas'] = $numeroPromocionesNueva;
                            break;
                        }elseif($posicionsca == sizeof($scasucursalescategorias)-1){
                            $nuevoArray = array(
                                "scaid"                      => '01'.$scasucursalescategorias[$posicionsca]['scaid'].'-0-'.sizeof($scasucursalescategorias),
                                "catid"                      => $categoria->catid,
                                "catnombre"                  => $categoria->catnombre,
                                "catimagenfondo"             => $categoria->catimagenfondo,
                                "catimagenfondoseleccionado" => $categoria->catimagenfondoseleccionado,
                                "catimagenfondoopaco"        => $categoria->catimagenfondoopaco,
                                "caticono"                   => $categoria->caticono,
                                "caticonohover"              => $categoria->caticonohover,
                                "catcolorhover"              => $categoria->catcolorhover,
                                "catcolor"                   => $categoria->catcolor,
                                "caticonoseleccionado"       => $categoria->caticonoseleccionado,
                                "fecfecha"                   => $scasucursalescategorias[$posicionsca]['fecfecha'],
                                "cantidadPromociones"        => 0,
                                "cantidadPromocionesNuevas"  => 0,
                            );

                            $scasucursalescategorias[] = $nuevoArray;
                            // $scasucursalescategorias[$posicionsca+1]['scaid']                       = 0;
                            // $scasucursalescategorias[$posicionsca+1]['catid']                       = $categoria->catid;
                            // $scasucursalescategorias[$posicionsca+1]['catnombre']                   = $categoria->catnombre;
                            // $scasucursalescategorias[$posicionsca+1]['catimagenfondo']              = $categoria->catimagenfondo;
                            // $scasucursalescategorias[$posicionsca+1]['catimagenfondoseleccionado']  = $categoria->catimagenfondoseleccionado;
                            // $scasucursalescategorias[$posicionsca+1]['catimagenfondoopaco']         = $categoria->catimagenfondoopaco;
                            // $scasucursalescategorias[$posicionsca+1]['caticono']                    = $categoria->caticono;
                            // $scasucursalescategorias[$posicionsca+1]['caticonohover']               = $categoria->caticonohover;
                            // $scasucursalescategorias[$posicionsca+1]['catcolorhover']               = $categoria->catcolorhover;
                            // $scasucursalescategorias[$posicionsca+1]['catcolor']                    = $categoria->catcolor;
                            // $scasucursalescategorias[$posicionsca+1]['caticonoseleccionado']        = $categoria->caticonoseleccionado;
                            // $scasucursalescategorias[$posicionsca+1]['fecfecha']                    = $sca->fecfecha;
                        }
                    }
                }
                

                $linea          = __LINE__;
                $datos          = $scasucursalescategorias;
                $respuesta      = true;
                $mensaje        = 'Las categorias fueron cargadas satisfactoriamente.';
                $mensajeDetalle = sizeof($scasucursalescategorias).' registros encontrados.';
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


        $car = carcargasarchivos::where('tcaid', 1)
                                ->OrderBy('carcargasarchivos.created_at', 'DESC')
                                ->first([
                                    'carcargasarchivos.created_at'
                                ]);
                    
        $fechaActualizacion = '';
        if($car){
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
            $diaActualizacion   = date("j", strtotime($car->created_at))." de ";
            $mesActualizacion   = $meses[date('n', strtotime($car->created_at))-1]." del ";
            $anioActualizacion  = date("Y", strtotime($car->created_at));
            $fechaActualizacion = $diaActualizacion.$mesActualizacion.$anioActualizacion;
        }else{

        }

        // OBTENER RESUMEN
        $arr_resumenPromociones = array();
        foreach($datos as $dato){
            $arr_resumenPromociones[] = array(
                "catnombre" => $dato['catnombre'],
                "total"   => doubleval($dato['cantidadPromociones']),
                "nueva"   => doubleval($dato['cantidadPromocionesNuevas']),
                "regular" => doubleval($dato['cantidadPromociones']) - doubleval($dato['cantidadPromocionesNuevas'])
            );
        }


        return response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            'fechaActualiza' => $fechaActualizacion,
            'arr_resumenPromociones' => $arr_resumenPromociones,
        ]);
    }

    public function mostrarCategoriasXZona(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
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

        try{

            $categorias = catcategorias::all();

            foreach($categorias as $cat){
                $scasucursalescategorias = scasucursalescategorias::join('fecfechas as fec', 'scasucursalescategorias.fecid', 'fec.fecid')
                                                                ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
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
                                                                ->where('fec.fecano', $ano)
                                                                ->where('fec.fecmes', $mes)
                                                                ->where('fec.fecdia', $dia)
                                                                ->where('scasucursalescategorias.tsuid', null)
                                                                ->where('scasucursalescategorias.catid', $cat->catid)
                                                                ->get();

                $numeroPromociones = 0;
                $numeroPromocionesNuevas = 0;
                $numeroCodigosPromociones = 0;
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
                                                                'prmmecanica'
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
                                }
                            }
                        }else{
                            // $promociones[] = $csc->prmcodigo;
                            $promociones[] = $csc->prmmecanica;
                            $numeroCodigosPromociones = $numeroCodigosPromociones+1;
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
                                                            ->where('csccanalessucursalescategorias.scaid', $sca->scaid)
                                                            // ->where('csp.cspcantidadcombo', "!=", 0)
                                                            ->where('csp.cspcantidadplancha', "!=", "0")
                                                            ->where('csp.cspestado', 1)
                                                            ->where('csp.cspnuevapromocion', 1)
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

        // OBTENER RESUMEN
        $arr_resumenPromociones = array();
        foreach($datos as $dato){
            $arr_resumenPromociones[] = array(
                "catnombre" => $dato['catnombre'],
                "total"   => doubleval($dato['cantidadPromociones']),
                "nueva"   => doubleval($dato['cantidadPromocionesNuevas']),
                "regular" => doubleval($dato['cantidadPromociones']) - doubleval($dato['cantidadPromocionesNuevas'])
            );
        }

        return response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'arr_resumenPromociones' => $arr_resumenPromociones,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            'fechaActualiza' => "",
        ]);
    }
}
