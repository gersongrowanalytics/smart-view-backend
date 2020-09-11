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

        try{
            // $usuusuario = usuusuarios::where('usutoken', $request->header('api_token'))->first(['usuid']);
            $fefe = new \DateTime(date("Y-m-d", strtotime($fecha)));
            
            $fecha = fecfechas::where('fecfecha', $fefe)->first();
            if($fecha){
                echo "existe la fecha";
            }else{
                echo "no existe la fecha";
            }

            // $rtp = new rtprebatetipospromociones;
            // if($rtp->save()){

            // }else{

            // }

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
            'Agregar un nuevo registro de rebate',
            'AGREGAR',
            '', //ruta
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
