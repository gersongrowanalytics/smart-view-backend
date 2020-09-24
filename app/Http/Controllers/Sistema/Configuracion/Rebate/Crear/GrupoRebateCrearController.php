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

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $tre = tretiposrebates::where('trenombre', $nombreGrupoRebate)->first(['treid']);

        if($tre){
            $respuesta  = false;
            $mensaje    = "Lo sentimos, ese nombre de grupo rebate ya existe";
            $linea      = __LINE__;
        }else{
            $nuevoTre = new tretiposrebates;
            $nuevoTre->trenombre = $nombreGrupoRebate;
            if($nuevoTre->save()){

                $respuesta  = true;
                $mensaje    = "Se agrego satisfactoriamente, el nuevo grupo rebate";
                $datos      = $nuevoTre;
                $linea      = __LINE__;

            }else{
                $respuesta  = false;
                $mensaje    = "Lo sentimos, ocurrio un error al momento de guardar el grupo rebate";
                $linea      = __LINE__;
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
            null,
            null,
            $request['ip'],
            $request,
            $requestsalida,
            'Agregar un nuevo grupo rebate',
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
