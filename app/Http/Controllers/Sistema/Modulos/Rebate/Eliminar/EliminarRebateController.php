<?php

namespace App\Http\Controllers\Sistema\Modulos\Rebate\Eliminar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\rtprebatetipospromociones;
use App\fecfechas;
use App\trrtiposrebatesrebates;

class EliminarRebateController extends Controller
{
    public function EliminarRebate(Request $request)
    {
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
        $re_porcentajerebate = $request['porcentajerebate'];

        $fec = fecfechas::where('fecmes', $re_mes)
                        ->where('fecano', $re_anio)
                        ->where('fecdia', '01')
                        ->first();

        if($fec){

            $rtp = rtprebatetipospromociones::where('tprid', $re_tprid)
                                            ->where('fecid', $fec->fecid)
                                            ->where('rtpporcentajedesde', $re_porcentajedesde )
                                            ->where('rtpporcentajehasta', $re_porcentajehasta )
                                            ->where('rtpporcentajerebate', $re_porcentajerebate )
                                            ->first();

            if($rtp){

                $trr = trrtiposrebatesrebates::where('rtpid', $rtp->rtpid)
                                            ->where('treid', $re_treid)
                                            ->delete();

            }else{
                $mensaje = "Lo sentimos no se encontro el registro, recomendamos actualizar la información";
            }

        }else{
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
            'Eliminar una linea de rebate',
            'ELIMINAR',
            '/eliminar-rebate-mensual', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
