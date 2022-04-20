<?php

namespace App\Http\Controllers\Sistema\Modulos\Rebate\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Sistema\Configuracion\Rebate\Actualizar\RebateActualizarController;
use App\rtprebatetipospromociones;
use App\fecfechas;
use App\trrtiposrebatesrebates;
use App\catcategorias;

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
        $re_reiniciar  = $request['reiniciar'];

        $cats = catcategorias::where('catid', '<', 6)
                            ->get();

        $fec = fecfechas::where('fecmes', $re_mes)
                        ->where('fecano', $re_anio)
                        ->where('fecdia', '01')
                        ->first();

        

        if($fec){

            // REINICIAR RTP Y TRR

            if($re_reiniciar == true){
                $rtps = rtprebatetipospromociones::where('fecid', $fec->fecid)->get();

                foreach($rtps as $rtp){
                    
                    trrtiposrebatesrebates::where('rtpid', $rtp->rtpid)->delete();
                    rtprebatetipospromociones::where('rtpid', $rtp->rtpid)->delete();

                }
            }

            foreach($re_datas as $re_data){

                foreach($re_data['data'] as $data){

                    if(isset($data['editando'])){
                        if($data['editando'] == true){ 
        
                            foreach($cats as $cat){

                                $rtp = rtprebatetipospromociones::where('fecid', $fec->fecid)
                                                                ->where('tprid', $data['tprideditando'])
                                                                ->where('rtpporcentajedesde', $data['desdeeditando'])
                                                                ->where('rtpporcentajehasta', $data['hastaeditando'])
                                                                ->where('rtpporcentajerebate', $data['cat-'.$cat->catid])
                                                                ->first();
                                
                                $rtpid = 0;
            
                                if($rtp){
                                    $rtpid = $rtp->rtpid;
                                }else{
                                    $rtpn = new rtprebatetipospromociones;
                                    $rtpn->fecid = $fec->fecid;
                                    $rtpn->tprid = $data['tprideditando'];
                                    $rtpn->rtpporcentajedesde  = $data['desdeeditando'];
                                    $rtpn->rtpporcentajehasta  = $data['hastaeditando'];
                                    $rtpn->rtpporcentajerebate = $data['cat-'.$cat->catid];
                                    if($rtpn->save()){
                                        $rtpid = $rtpn->rtpid;
                                    }else{
                                        $respuesta = false;
                                        $mensaje = "Lo sentimos algunos rebates no fueron agregados";
                                    }
                                }

        
                                $trr = trrtiposrebatesrebates::where('treid', $data['treideditando'] )
                                                            ->where('rtpid', $rtpid)
                                                            ->where('catid', $cat->catid)
                                                            ->first();
        
                                if($trr){
        
                                }else{
                                    $trrn = new trrtiposrebatesrebates;
                                    $trrn->treid = $data['treideditando'];
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
                }
    
            }

            $RebateActualizar = new RebateActualizarController;
            $actRebate = $RebateActualizar->actualizarValorizadoRebateFechaGet($fec->fecid, $usutoken);
            
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
