<?php

namespace App\Http\Controllers\Sistema\Configuracion\Rebate\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\rtprebatetipospromociones;
use App\fecfechas;

class RebateCrearController extends Controller
{
    public function CrearRebate(Request $request )
    {
        $usutoken   = $request->header('api_token');

        $fecha            = $request['fecha'];
        $tipoPromocion    = $request['tipoPromocion'];
        $porcentajeDesde  = $request['porcentajeDesde'];
        $porcentajeHasta  = $request['porcentajeHasta'];
        $porcentajeRebate = $request['porcentajeRebate'];

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log            = [];
        $pkid           = 0;

        try{
            $fecha = new \DateTime(date("Y-m-d", strtotime($fecha)));

            $fecfecha = fecfechas::where('fecfecha', $fecha)->first(['fecid']);
            $fecid = 0;
            if($fecfecha){
                $fecid = $fecfecha->fecid;
                $log[] = "Existe la fecha";
            }else{
                $log[] = "No existe la fecha";
                $nuevafecha = new fecfechas;
                $nuevafecha->fecfecha = $fecha;
                $nuevafecha->fecdia   = '';
                $nuevafecha->fecmes   = '';
                $nuevafecha->fecano   = '';
                if($nuevafecha->save()){
                    $fecid = $nuevafecha->fecid;

                    $pkid = "FEC-".$fecid." ";
                    $log[] = "Se agrego la fecha";
                }else{
                    $pkid = "";
                    $log[] = "No se pudo agregar la fecha";
                }
            }

            $rtp = new rtprebatetipospromociones;
            $rtp->fecid               = $fecid;
            $rtp->tprid               = $tipoPromocion;
            $rtp->rtpporcentajedesde  = $porcentajeDesde;
            $rtp->rtpporcentajehasta  = $porcentajeHasta;
            $rtp->rtpporcentajerebate = $porcentajeRebate;
            if($rtp->save()){
                $respuesta      = true;
                $mensaje        = 'El rebate se registro correctamente';
                $datos          = $rtp;
                $linea          = __LINE__;
                $mensajeDetalle = 'Nuevo rebate agregado';
                $pkid           = $pkid."RTP-".$rtp->rtpid;
                $log[]          = "Se agrego el rebate";
            }else{
                $respuesta      = false;
                $mensaje        = 'Ocurrio un error al momento de agregar el rebate';
                $datos          = [];
                $linea          = __LINE__;
                $mensajeDetalle = 'El rebate no se agrego';
                $log[]          = "No se pudo agregar el rebate";
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[]      = "ERROR DE SERVIDOR: ".$mensajedev;
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
            null,
            $request,
            $requestsalida,
            'Agregar un nuevo registro de rebate',
            'AGREGAR',
            '/configuracion/rebate/crearRebate', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
