<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\scasucursalescategorias;
use App\catcategorias;

class CategoriasController extends Controller
{
    public function mostrarCategorias(Request $request)
    {
        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
        $dia        = $request['dia'];
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
                        if($categoria->catnombre == $sca->catnombre){
                            break;
                        }elseif($posicionsca == sizeof($scasucursalescategorias)-1){
                            $nuevoArray = array(
                                "scaid"                      => 0,
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
                                "fecfecha"                   => $sca->fecfecha,
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

        return response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev
        ]);
    }
}
