<?php

namespace App\Http\Controllers\Sistema\Configuracion\Rebate\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\fecfechas;
use App\rtprebatetipospromociones;
use App\tprtipospromociones;
use App\trrtiposrebatesrebates;

class RebateMostrarController extends Controller
{
    public function RebateMostrar(Request $request )
    {

        $usutoken = $request->header('api_token');

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{
            
            $rtp = rtprebatetipospromociones::join('trrtiposrebatesrebates as trr', 'trr.rtpid', 'rtprebatetipospromociones.rtpid')
                                            ->leftjoin('catcategorias as cat', 'cat.catid', 'trr.catid')
                                            ->join('tretiposrebates as tre', 'tre.treid', 'trr.treid')
                                            ->join('fecfechas as fec', 'fec.fecid', 'rtprebatetipospromociones.fecid')
                                            ->join('tprtipospromociones as tpr', 'tpr.tprid', 'rtprebatetipospromociones.tprid')
                                            ->OrderBy('rtprebatetipospromociones.created_at', 'DESC')
                                            ->get([
                                                'rtprebatetipospromociones.rtpid',
                                                'fec.fecid',
                                                'fec.fecfecha',
                                                'fec.fecdia',
                                                'fec.fecmes',
                                                'fec.fecano',
                                                'tpr.tprid',
                                                'tpr.tprnombre',
                                                'trr.trrid',
                                                'tre.treid',
                                                'tre.trenombre',
                                                'rtpporcentajedesde',
                                                'rtpporcentajehasta',
                                                'rtpporcentajerebate',
                                                'cat.catnombre'
                                            ]);

            if(sizeof($rtp) > 0){
                $respuesta      = true;
                $datos          = $rtp;
                $linea          = __LINE__;
                $mensaje        = 'Los rebates se cargaron satisfactoriamente.';
                $mensajeDetalle = sizeof($rtp).' registros encontrados.';
            }else{
                $respuesta      = false;
                $datos          = [];
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos, no se econtraron rebates registrados';
                $mensajeDetalle = sizeof($rtp).' registros encontrados.';
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);
        
        return $requestsalida;

    }

    public function MostrarRebateOrdenado(Request $request)
    {
        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $mensajeDetalle = '';

        $usutoken = $request->header('api_token');
        $fecha = $request['fecha'];

        $fecha = new \DateTime(date("Y-m-d", strtotime($fecha)));
        $fec = fecfechas::where('fecfecha', $fecha)->first(['fecid']);

        if($fec){
            $tprs = tprtipospromociones::all();

            foreach($tprs as $posicionTpr => $tpr){
                
                $rtps = rtprebatetipospromociones::where('fecid', $fec->fecid)
                                                ->where('tprid', $tpr->tprid)
                                                ->get();


                $cats = [];
                $tres = [];

                foreach($rtps as $posicionRtp => $rtp){
                    $trrs = trrtiposrebatesrebates::join('catcategorias as cat', 'cat.catid', 'trrtiposrebatesrebates.catid')
                                                    ->join('tretiposrebates as tre', 'tre.treid', 'trrtiposrebatesrebates.treid')
                                                    ->where('rtpid', $rtp->rtpid)
                                                    ->get([
                                                        'cat.catnombre',
                                                        'tre.trenombre'
                                                    ]);

                    foreach($trrs as $trr){
                        if(sizeof($cats) > 0){
                            foreach($cats as $posicionCat => $cat){
                                if($cat == $trr->catnombre){
                                    break;
                                }

                                if(sizeof($cats) == $posicionCat+1){
                                    $cats[] = $trr->catnombre;        
                                }

                            }
                        }else{
                            $cats[] = $trr->catnombre;
                        }

                        if(sizeof($tres) > 0){
                            foreach($tres as $posicionTre => $tre){
                                if($tre == $trr->trenombre){
                                    break;
                                }

                                if(sizeof($tres) == $posicionTre+1){
                                    $tres[] = $trr->trenombre;        
                                }

                            }
                        }else{
                            $tres[] = $trr->trenombre;
                        }


                    }
                    

                    $rtps[$posicionRtp]["cats"][] = $cats;
                    $rtps[$posicionRtp]["tres"][] = $tres;
                }

                $tprs[$posicionTpr]['rtps'] = $rtps;


            }

            $datos = $tprs;
        }else{
            $respuesta = false;
            $mensaje = "No existe la fecha seleccionada";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $datos,
        ]);
        
        return $requestsalida;

    }
}
