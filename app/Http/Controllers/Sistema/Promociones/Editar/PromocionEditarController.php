<?php

namespace App\Http\Controllers\Sistema\Promociones\Editar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\cspcanalessucursalespromociones;
use App\Http\Controllers\AuditoriaController;

class PromocionEditarController extends Controller
{
    public function editarPromocion(Request $request)
    {
        $usutoken   = $request->header('api_token');
        $cspid      = $request['cspid'];
        $valorizado = $request['valorizado'];
        $planchas   = $request['planchas'];


        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{

            $csp = cspcanalessucursalespromociones::find($cspid);
            $csp->cspvalorizado = $valorizado;
            $csp->cspplanchas   = $planchas;
            $csp->cspcompletado = true;
            if($csp->update()){
                $linea          = __LINE__;
                $respuesta      = true;
                $datos          = $csp;
                $mensaje        = 'La promoción se actualizo correctamente';
                $mensajeDetalle = 'Recuerda que puedes seguir editando la promoción durante el resto del día';

            }else{
                $linea          = __LINE__;
                $respuesta      = false;
                $datos          = [];
                $mensaje        = 'Lo sentimos, la promoción no se pudo guardar';
                $mensajeDetalle = 'Actualice la pagina o comuniquese con alguien de soporte';
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
            'Editar la promocion, datos como el valorizado la plancha por usuario',
            'EDITAR',
            '', //ruta
            null
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
