<?php

namespace App\Http\Controllers\Sistema\Ventas\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\catcategorias;
use App\trftrimestresfechas;
use App\ttrtritre;

class CrearRebateTrimestralController extends Controller
{
    public function CrearRebateTrimestral(Request $request)
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

        $trf = trftrimestresfechas::join('fecfechas as fec', 'fec.fecid', 'trftrimestresfechas.fecid')
                                    ->where('fec.fecano', $re_anio)
                                    ->where('fec.fecmes', $re_mes)
                                    ->where('fec.fecdia', "01")
                                    ->first();

        if($trf){

            if($re_reiniciar == true){
                ttrtritre::where('triid', $trf->triid)->delete();
            }


            foreach($re_datas as $re_data){

                foreach($re_data['data'] as $data){

                    if(isset($data['editando'])){
                        if($data['editando'] == true){

                            foreach($cats as $cat){

                                $ttrn = new ttrtritre;
                                $ttrn->fecid = $trf->fecid;
                                $ttrn->triid = $trf->triid;
                                $ttrn->treid = $data['treideditando'];
                                $ttrn->catid = $cat->catid;
                                $ttrn->tprid = $data['tprideditando'];
                                $ttrn->ttrporcentajedesde  = $data['desdeeditando'];
                                $ttrn->ttrporcentajehasta  = $data['hastaeditando'];
                                $ttrn->ttrporcentajerebate = $data['cat-'.$cat->catid];
                                $ttrn->save();

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
            'Agregar varios rebates trimestrales',
            'AGREGAR',
            '/crear-varios-rebate-trimestral', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
