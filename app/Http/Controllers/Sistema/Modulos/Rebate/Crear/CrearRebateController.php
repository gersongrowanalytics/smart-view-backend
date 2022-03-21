<?php

namespace App\Http\Controllers\Sistema\Modulos\Rebate\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\rtprebatetipospromociones;
use App\fecfechas;
use App\trrtiposrebatesrebates;

class CrearRebateController extends Controller
{
    public function CrearRebate(Request $request)
    {

        $respuesta = true;
        $mensaje   = "Los rebate fueron creados satisfactoriamente";
        $usutoken  = $request->header('api_token');
        $pkid = array();
        $log  = array();

        $re_datas = $request['data'];
        $re_mes   = $request['mes'];
        $re_anio  = $request['anio'];

        $cats = catcategorias::where('catid', '<', 6)
                            ->get();

        $fec = fecfechas::where('fecmes', $re_mes)
                        ->where('fecano', $re_anio)
                        ->where('fecdia', '01')
                        ->first();

        if($fec){

            foreach($re_datas as $re_data){

                if($re_data['editando'] == true){
                    
                    $rtp = rtprebatetipospromociones::where('fecid', $fec->fecid)
                                                    ->where('tprid', $re_data['tprideditando'])
                                                    ->where('rtpporcentajedesde', $re_data['desdeeditando'])
                                                    ->where('rtpporcentajehasta', $re_data['hastaeditando'])
                                                    ->where('rtpporcentajerebate', $re_data['rebateeditando'])
                                                    ->first();
                    
                    $rtpid = 0;

                    if($rtp){
                        $rtpid = $rtp->rtpid;
                    }else{
                        $rtpn = new rtprebatetipospromociones;
                        $rtpn->fecid = $fec->fecid;
                        $rtpn->tprid = $re_data['tprideditando'];
                        $rtpn->rtpporcentajedesde  = $re_data['desdeeditando'];
                        $rtpn->rtpporcentajehasta  = $re_data['hastaeditando'];
                        $rtpn->rtpporcentajerebate = $re_data['rebateeditando'];
                        if($rtpn->save()){
                            $rtpid = $rtpn->rtpid;
                        }else{
                            $respuesta = false;
                            $mensaje = "Lo sentimos algunos rebates no fueron agregados";
                        }
                    }

                    foreach($cats as $cat){

                        $trr = trrtiposrebatesrebates::where('treid', $re_data['treideditando'] )
                                                    ->where('rtpid', $rtpid)
                                                    ->where('catid', $cat->catid)
                                                    ->first();

                        if($trr){

                        }else{
                            $trrn = new trrtiposrebatesrebates;
                            $trrn->treid = $re_data['treideditando'];
                            $trrn->rtpid = $rtpid;
                            $trrn->catid = $cat->catid;
                            if($trrn->save()){

                            }else{
                                $respuesta = false;
                                $mensaje = "Lo sentimos algunos rebates no fueron agregados";
                            }
                        }

                    }

    
                }
    
            }

        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos el mes seleccionado no ha sido aperturado";
        }


        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            null,
            $request,
            $requestsalida,
            'Agregar varios rebates',
            'AGREGAR',
            '/crear-varios-rebate', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
