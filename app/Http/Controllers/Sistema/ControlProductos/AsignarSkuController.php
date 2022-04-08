<?php

namespace App\Http\Controllers\Sistema\ControlProductos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\impimagenesproductos;
use App\proproductos;
use App\prppromocionesproductos;
use App\prbpromocionesbonificaciones;
use Illuminate\Support\Str;
use \DateTime;

class AsignarSkuController extends Controller
{
    public function AsignarSku(Request $request)
    {

        $req_imagen = $request['req_imagen'];
        $req_prosku = $request['req_prosku'];

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

        $respuesta = true;
        $mensaje   = "El producto se actualizo correctamente";

        $req_fechas = $request['req_fechas'];

        $proe = proproductos::where('prosku', $req_prosku)->first();

        if($proe){

            $impn = new impimagenesproductos;
            $impn->proid = $proe->proid;
            $impn->impimagen = $proe->proimagen;
            $impn->impfechainicio = $proe->profechainicio;
            $impn->impfechafinal = $proe->profechafinal;
            $impn->save();

            if(isset($req_fechas)){
                if(sizeof($req_fechas) > 0){

                    $fechaInicio = $req_fechas[0];
                    $fechaFinal  = $req_fechas[1];
    
                    $fe_ini = explode("/", $fechaInicio);
                    $fe_fin = explode("/", $fechaFinal);
    
                    $proe->profechainicio = $fe_ini[2]."-".$fe_ini[1]."-".$fe_ini[0];
                    $proe->profechafinal  = $fe_fin[2]."-".$fe_fin[1]."-".$fe_fin[0];
                }
            }

            list(, $base64) = explode(',', $req_imagen);
            $fichero = '/Sistema/promociones/IMAGENES/PRODUCTOSNUEVO/'.Str::random(5)."-".$fechaActual."-".$req_prosku.".png";
            $archivo = base64_decode($base64);
            file_put_contents(base_path().'/public'.$fichero, $archivo);

            $proe->proimagen = env('APP_URL').$fichero;
            $proe->update();

            prppromocionesproductos::join('prmpromociones as prm', 'prm.prmid', 'prppromocionesproductos.prmid')
                                    ->where('proid', $proe->proid)
                                    ->where('prm.fecid', '>' ,152)
                                    ->update(['prpimagen' => $proe->proimagen]);

            prbpromocionesbonificaciones::join('prmpromociones as prm', 'prm.prmid', 'prbpromocionesbonificaciones.prmid')
                                    ->where('proid', $proe->proid)
                                    ->where('prm.fecid', '>' ,152)
                                    ->update(['prbimagen' => $proe->proimagen]);


        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, no pudimos encontrar el sku seleccionado, recomendamos actualizar la pagina o comunicarse con alguien de soporte";
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
        ]);

        return $requestsalida;


    }
}
