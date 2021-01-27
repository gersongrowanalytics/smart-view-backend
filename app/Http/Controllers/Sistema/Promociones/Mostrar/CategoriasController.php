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

                            $scasucursalescategorias[$posicionsca]['cantidadPromociones'] = $numeroPromociones;
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
                                "cantidadPromociones"        => 0
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

        return response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            'fechaActualiza' => $fechaActualizacion
        ]);
    }

    public function mostrarCategoriasXZona(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
        $zonid      = $request['zonid'];
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
                                                                ->where('suc.zonid', $zonid)
                                                                ->where('fec.fecano', $ano)
                                                                ->where('fec.fecmes', $mes)
                                                                ->where('fec.fecdia', $dia)
                                                                ->where('scasucursalescategorias.tsuid', null)
                                                                ->where('scasucursalescategorias.catid', $cat->catid)
                                                                ->get();
                $numeroPromociones = 0;

                foreach($scasucursalescategorias as $sca){
                    $countCsc = csccanalessucursalescategorias::join('cspcanalessucursalespromociones as csp', 'csp.cscid', 'csccanalessucursalescategorias.cscid')
                                                            ->where('csccanalessucursalescategorias.scaid', $sca->scaid)
                                                            // ->where('csp.cspcantidadcombo', "!=", 0)
                                                            ->where('csp.cspcantidadplancha', "!=", "0")
                                                            ->where('csp.cspestado', 1)
                                                            ->count();

                    $numeroPromociones = $numeroPromociones + $countCsc;
                }

                


                $nuevoArray = array(
                    "scaid"                      => 1,
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
                    "cantidadPromociones"        => $numeroPromociones
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

        return response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            'fechaActualiza' => ""
        ]);
    }
}
