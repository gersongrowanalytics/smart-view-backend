<?php

namespace App\Http\Controllers\Sistema\Configuracion\Rebate\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\rtprebatetipospromociones;

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
            
            $rtp = rtprebatetipospromociones::
                                            
                                            join('fecfechas as fec', 'fec.fecid', 'rtprebatetipospromociones.fecid')
                                            ->join('tprtipospromociones as tpr', 'tpr.tprid', 'rtprebatetipospromociones.tprid')
                                            ->get([
                                                'rtpid',
                                                'fec.fecid',
                                                'fec.fecfecha',
                                                'fec.fecdia',
                                                'fec.fecmes',
                                                'fec.fecano',
                                                'tpr.tprid',
                                                'tpr.tprnombre',
                                                // 'tre.trenombre',
                                                'rtpporcentajedesde',
                                                'rtpporcentajehasta',
                                                'rtpporcentajerebate'
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

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            $request['ip'],
            $request,
            $requestsalida,
            'Mostrar todos los rebates',
            'MOSTRAR',
            '', //ruta
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }
}
