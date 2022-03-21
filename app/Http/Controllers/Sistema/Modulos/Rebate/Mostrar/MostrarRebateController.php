<?php

namespace App\Http\Controllers\Sistema\Modulos\Rebate\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\fecfechas;
use App\catcategorias;
use App\tprtipospromociones;
use App\trrtiposrebatesrebates;
use App\rtprebatetipospromociones;

class MostrarRebateController extends Controller
{
    public function MostrarRebate(Request $request)
    {

        $re_mes  = $request['mes'];
        $re_anio = $request['anio'];

        $respuesta = true;

        $fec = fecfechas::where('fecmes', $re_mes)
                        ->where('fecano', $re_anio)
                        ->where('fecdia', '01')
                        ->first();

        $arr_data = array();
        $trrs_grupos = array();

        if($fec){ 

            $tprs = tprtipospromociones::get(['tprid', 'tprnombre']);


            foreach($tprs as $tpr){


                $rtps = rtprebatetipospromociones::where('fecid', $fec->fecid)
                                                ->where('tprid', $tpr->tprid)
                                                ->get();

                foreach($rtps as $rtp){

                    $trrs_grupos = trrtiposrebatesrebates::join('tretiposrebates as tre', 'trrtiposrebatesrebates.treid', 'tre.treid')
                                                    ->where('rtpid', $rtp->rtpid)
                                                    ->distinct('trrtiposrebatesrebates.treid')
                                                    ->get([
                                                        'trrtiposrebatesrebates.treid',
                                                        'trenombre'
                                                    ]);

                    foreach($trrs_grupos as $posicionTrr => $trrs_grupo){
                        
                        $arr_data_agregar = array();

                        $arr_data_agregar['trenombre'] = $trrs_grupo->trenombre;
                        $arr_data_agregar['rtpporcentajedesde'] = $rtp->rtpporcentajedesde;
                        $arr_data_agregar['rtpporcentajehasta'] = $rtp->rtpporcentajehasta;
                        $arr_data_agregar['rtpporcentajerebate'] = $rtp->rtpporcentajerebate;
                        $arr_data_agregar['tprnombre'] = $tpr->tprnombre;



                        $trrs = trrtiposrebatesrebates::where('rtpid', $rtp->rtpid)
                                                    ->where('treid', $trrs_grupo->treid)
                                                    ->get([
                                                        'catid'
                                                    ]);

                        foreach($trrs as $trr){
                            $arr_data_agregar['cat-'.$trr->catid] = $rtp->rtpporcentajerebate;
                        }

                        $trrs_grupos[$posicionTrr]['data'] = $arr_data_agregar;
                        $arr_data[] = $arr_data_agregar;

                    }



                }


            }



            

        }else{
            $respuesta = false;
        }

        return response()->json([
            "datos"     => $arr_data,
            "tablas"    => $trrs_grupos,
            "respuesta" => $respuesta,
            "datos_enviado" => [$re_mes, $re_anio]
        ]);

    }
}
