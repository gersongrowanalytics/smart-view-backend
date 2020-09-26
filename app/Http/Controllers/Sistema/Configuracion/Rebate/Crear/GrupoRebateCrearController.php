<?php

namespace App\Http\Controllers\Sistema\Configuracion\Rebate\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tretiposrebates;
use App\Http\Controllers\AuditoriaController;

class GrupoRebateCrearController extends Controller
{
    public function CrearGrupoRebate(Request $request)
    {

        $nombreGrupoRebate = $request['nombreGrupoRebate'];
        $usutoken       = $request->header('api_token');
        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log            = [];

        $pkid           = 0;

        $tre = tretiposrebates::where('trenombre', $nombreGrupoRebate)->first(['treid']);

        if($tre){
            $respuesta  = false;
            $mensaje    = "Lo sentimos, ese nombre de grupo rebate ya existe";
            $linea      = __LINE__;
            $log[]      = "El tre (nombre de grupo de rebate) ya existe";
        }else{
            $nuevoTre = new tretiposrebates;
            $nuevoTre->trenombre = $nombreGrupoRebate;
            if($nuevoTre->save()){

                $respuesta  = true;
                $mensaje    = "Se agrego satisfactoriamente, el nuevo grupo rebate";
                $datos      = $nuevoTre;
                $linea      = __LINE__;
                $log[]      = "El grupo rebate se creo satisfactoriamente";
                $pkid       = $nuevoTre->treid;
            }else{
                $respuesta  = false;
                $mensaje    = "Lo sentimos, ocurrio un error al momento de guardar el grupo rebate";
                $linea      = __LINE__;
                $log[]      = "Ocurrio un error al momento de intentar crear el grupo rebate";
            }
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
            'Agregar un nuevo grupo rebate',
            'AGREGAR',
            '/configuracion/rebate/crear/GrupoRebate', //ruta
            'TRE-'.$pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
