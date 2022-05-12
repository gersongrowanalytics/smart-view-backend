<?php

namespace App\Http\Controllers\Sistema\Ventas\Eliminar;

use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Controller;
use App\trftrimestresfechas;
use App\ttrtritre;
use Illuminate\Http\Request;

class EliminarRebateTrimestralController extends Controller
{
    public function EliminarTrimiestreRebate(Request $request){

        $respuesta = true;
        $mensaje = "El rebate fue eliminado correctamente";
        $usutoken  = $request->header('api_token');
        $pkid = array();
        $log  = array();

        $re_mes  = $request['mes'];
        $re_anio = $request['anio'];

        $re_tprid  = $request['tprid'];
        $re_treid  = $request['treid'];
        $re_porcentajedesde  = $request['porcentajedesde'];
        $re_porcentajehasta  = $request['porcentajehasta'];
        // $re_porcentajerebate = $request['porcentajerebate'];

        $trf = trftrimestresfechas::join('fecfechas as fec', 'fec.fecid', 'trftrimestresfechas.fecid')
                                    ->where('fec.fecano', $re_anio)
                                    ->where('fec.fecmes', $re_mes)
                                    ->where('fec.fecdia', "01")
                                    ->first();

        if($trf){
            $ttr = ttrtritre::where('tprid', $re_tprid)
                            ->where('fecid', $trf->fecid)
                            ->where('treid', $re_treid)
                            ->where('ttrporcentajedesde', $re_porcentajedesde)
                            ->where('ttrporcentajehasta', $re_porcentajehasta)
                            // ->where('ttrporcentajerebate', $re_porcentajerebate)
                            ->delete();
            
            if ($ttr > 0) {
                
            }else{
                $respuesta = false;
                $mensaje = "Lo sentimos no se encontro el registro, recomendamos actualizar la información";
            }

        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos la fecha no se encontro, recomendamos actualizar la información";
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
            'Eliminar una linea de rebate trimestre',
            'ELIMINAR',
            '/eliminar-rebate-trimestre', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
