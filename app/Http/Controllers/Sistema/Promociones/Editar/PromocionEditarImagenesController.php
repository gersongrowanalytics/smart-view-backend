<?php

namespace App\Http\Controllers\Sistema\Promociones\Editar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\prppromocionesproductos;
use App\prbpromocionesbonificaciones;
use App\Http\Controllers\AuditoriaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PromocionEditarImagenesController extends Controller
{
    public function EditarImagenesPromocion(Request $request)
    {
        $usutoken   = $request->header('api_token');
        $prpid              = $request['prpid'];
        $fecid              = $request['fecid'];
        $imagenProducto     = $request['imagenProducto'];
        $prbid              = $request['prbid'];
        $imagenBonificado   = $request['imagenBonificado'];

        $respuesta      = true;
        $mensaje        = 'Las imagenes se actualizaron correctamente';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log = [];
        $pkidprp = 0;
        $pkidprb = 0;

        if($fecid == null){
            $fecid = 3;
        }

        DB::beginTransaction();

        try{
            if($prpid != 0){
                $prp = prppromocionesproductos::find($prpid);

                if($prp){
                    list(, $base64) = explode(',', $imagenProducto);
                    $fichero = '/Sistema/promociones/IMAGENES/PRODUCTOS/';
                    
                    $archivo = base64_decode($base64);

                    $nombre  = $fecid."-".$prp->prmid."-".$prp->proid."-".$prp->prpproductoppt."-".$prp->prpcomprappt.".png";
                    // Str::random(10).'.png';

                    file_put_contents(base_path().'/public'.$fichero.$nombre, $archivo);
                    $prp->prpimagen = env('APP_URL').$fichero.$nombre;

                    if($prp->update()){
                        $pkidprp = "PRP-".$prp->prpid;
                        $log[]   = "SE EDITO LA IMAGEN DEL PRODUCTO: ".$prp->prpimagen;
                    }else{
                        $respuesta = false;
                        $log[]   = "NO SE EDITO LA IMAGEN DEL PRODUCTO";
                        $mensaje = 'Lo sentimos, ocurrio un error al momento de editar la imagen de producto';
                    }

                }else{
                    $respuesta = false;
                    $log[]   = "NO SE ENCONTRO EL ID DEL PRODUCTO PRP-".$prpid;
                }
            }

            if($prbid != 0){
                $prb = prbpromocionesbonificaciones::find($prbid);

                if($prb){
                    list(, $base64) = explode(',', $imagenBonificado);
                    $fichero = '/Sistema/promociones/IMAGENES/BONIFICADOS/';
                    
                    $archivo = base64_decode($base64);
                    $nombre  = $fecid."-".$prb->prmid."-".$prb->proid."-".$prb->prbproductoppt."-".$prb->prbcomprappt.".png";
                    // Str::random(10).'.png';

                    file_put_contents(base_path().'/public'.$fichero.$nombre, $archivo);
                    $prb->prbimagen = env('APP_URL').$fichero.$nombre;;
                    if($prb->update()){
                        $pkidprb = "PRB-".$prb->prpid;
                        $log[]   = "SE EDITO LA IMAGEN DEL PRODUCTO BONIFICADO: ".$prb->prbimagen ;
                    }else{
                        $respuesta = false;
                        $log[]   = "NO SE EDITO LA IMAGEN DEL PRODUCTO";
                        $mensaje = 'Lo sentimos, ocurrio un error al momento de editar la imagen de bonificado';
                    }
                }else{
                    $respuesta = false;
                    $log[]   = "NO SE ENCONTRO EL ID DEL PRODUCTO BONIFICADO PRB-".$prbid;
                }
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            $respuesta  = false;
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            $log[]      = "ERROR SERVIDOR: ".$e->getMessage();
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
            'EDITAR LAS IMAGENES DE PRODUCTOS Y BONIFICACIONES DE UNA PROMOCION EN ESPECIFICO',
            'EDITAR',
            '/promociones/editar/imagenes', //ruta
            $pkidprp.', '.$pkidprb,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
