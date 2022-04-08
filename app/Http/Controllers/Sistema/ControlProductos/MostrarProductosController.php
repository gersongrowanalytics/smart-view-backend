<?php

namespace App\Http\Controllers\Sistema\ControlProductos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\proproductos;
use App\prppromocionesproductos;
use App\prbpromocionesbonificaciones;
use App\impimagenesproductos;
use Illuminate\Support\Str;
use \DateTime;

class MostrarProductosController extends Controller
{
    public function MostrarProductos(Request $request)
    {

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');


        $prosSinImagenes = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                        ->where('proimagen', "/")
                                        ->where('proespromocion', 1)
                                        ->limit(200)
                                        ->get([
                                            'proproductos.proid',
                                            'prosku',
                                            'pronombre',
                                            'catnombre',
                                            'proimagen',
                                            'proproductos.created_at',
                                            'proproductos.updated_at',
                                            'profechainicio',
                                            'profechafinal'
                                        ]);

        $prosConImagenes = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                        ->where('proimagen', "!=", "/")
                                        ->where('proespromocion', 1)
                                        ->limit(200)
                                        ->get([
                                            'proproductos.proid',
                                            'prosku',
                                            'pronombre',
                                            'catnombre',
                                            'proimagen',
                                            'proproductos.created_at',
                                            'proproductos.updated_at',
                                            'profechainicio',
                                            'profechafinal'
                                        ]);

        $imps = impimagenesproductos::join('proproductos as pro', 'pro.proid', 'impimagenesproductos.proid')
                                    ->join('catcategorias as cat', 'cat.catid', 'pro.catid')
                                    ->get([
                                        'pro.proid',
                                        'prosku',
                                        'pronombre',
                                        'catnombre',
                                        'proimagen',
                                        'pro.created_at',
                                        'pro.updated_at',
                                        'profechainicio',
                                        'profechafinal'
                                    ]);

        $prosVencidos = [];
        $prosConImagenesFormat = array();

        foreach($prosConImagenes as $prosConImagen){
            $prosConImagenesFormat[] = [
                "proid" => $prosConImagen->proid,
                "prosku" => $prosConImagen->prosku,
                "pronombre" => $prosConImagen->pronombre,
                "catnombre" => $prosConImagen->catnombre,
                "proimagen" => $prosConImagen->proimagen,
                "created_at" => $prosConImagen->created_at,
                "updated_at" => $prosConImagen->updated_at,
                "profechainicio" => $prosConImagen->profechainicio,
                "profechafinal" => $prosConImagen->profechafinal,
            ];
        }

        foreach($prosConImagenesFormat as $posicionProConImagen => $prosConImagen){


            if(isset($prosConImagen['profechafinal'])){

                $date1 = new DateTime($fechaActual);
                $date2 = new DateTime($prosConImagen['profechafinal']);

                $diff = $date1->diff($date2);

                if($diff->invert == 1){
                    
                    unset($prosConImagenesFormat[$posicionProConImagen]);
                    $prosVencidos[] = array(
                        "proid"           => $prosConImagen['proid'],
                        "prosku"          => $prosConImagen['prosku'],
                        "pronombre"       => $prosConImagen['pronombre'],
                        "catnombre"       => $prosConImagen['catnombre'],
                        "proimagen"       => $prosConImagen['proimagen'],
                        "created_at"      => $prosConImagen['created_at'],
                        "updated_at"      => $prosConImagen['updated_at'],
                        "profechainicio"  => $prosConImagen['profechainicio'],
                        "profechafinal"   => $prosConImagen['profechafinal'],
                    );

                }else{
                    
                } 

            }
        }


        foreach($imps as $imp){
            $prosVencidos[] = array(
                "proid"           => $imp['proid'],
                "prosku"          => $imp['prosku'],
                "pronombre"       => $imp['pronombre'],
                "catnombre"       => $imp['catnombre'],
                "proimagen"       => $imp['proimagen'],
                "created_at"      => $imp['created_at'],
                "updated_at"      => $imp['updated_at'],
                "profechainicio"  => $imp['profechainicio'],
                "profechafinal"   => $imp['profechafinal'],
            );
        }


        $requestsalida = response()->json([
            "prosSinImagenes" => $prosSinImagenes,
            "prosConImagenes" => $prosConImagenesFormat,
            "prosVencidos" => $prosVencidos,
        ]);

        return $requestsalida;

    }

    public function ModificarImagenProductos() // ASIGNAR IMAGENES A LOS PRODUCTOS
    {

        $logs = array(
            "PRODUCTOS_MODIFICADO_PRP" => [],
            "PRODUCTOS_MODIFICADO_PRB" => [],
            "NO_SE_ENCONTRO_PRODUCTO" => []
        );

        proproductos::where('proimagen', '!=', "/")
                    ->update(['proimagen' => "/"]);

        proproductos::where('proespromocion', 1)
                    ->update(['proespromocion' => 0]);

        $pros = proproductos::get();

        foreach ($pros as $posicionPro => $pro) {
            
            $prp = prppromocionesproductos::where('proid', $pro->proid)->first();

            if($prp){
                $proe = proproductos::find($pro->proid);
                $proe->proimagen = $prp->prpimagen;
                $proe->proespromocion = 1;
                $proe->update();

                $logs["PRODUCTOS_MODIFICADO_PRP"][] = "Imagen de productos: ".$pro->proid." con la imagen: ".$prp->prpimagen;

            }else{
                $prb = prbpromocionesbonificaciones::where('proid', $pro->proid)->first();

                if($prb){
                    $proe = proproductos::find($pro->proid);
                    $proe->proimagen = $prb->prbimagen;
                    $proe->proespromocion = 1;
                    $proe->update();

                    $logs["PRODUCTOS_MODIFICADO_PRB"][] = "Imagen de productos: ".$pro->proid." con la imagen: ".$prb->prbimagen;

                }else{
                    $logs["NO_SE_ENCONTRO_PRODUCTO"][] = "No se encontro imagen para: ".$pro->proid;
                }

            }
            
        }

        $requestsalida = response()->json([
            "logs" => $logs,
        ]);

        return $requestsalida;

    }

    public function AsignarImagenProducto(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d');

        $respuesta = true;
        $mensaje   = "El producto se actualizo correctamente";

        $req_prosku = $request['req_prosku'];
        $req_imagen = $request['req_imagen'];

        $req_fechas = $request['req_fechas'];

        $proe = proproductos::where('prosku', $req_prosku)->first();

        if($proe){

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
                                    ->where('prm.fecid', '>' ,62)
                                    ->update(['prpimagen' => $proe->proimagen]);

            prbpromocionesbonificaciones::join('prmpromociones as prm', 'prm.prmid', 'prbpromocionesbonificaciones.prmid')
                                    ->where('proid', $proe->proid)
                                    ->where('prm.fecid', '>' ,62)
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

    public function EliminarImagenProducto(Request $request)
    {
        $req_eliminarimagenes = $request['req_eliminarimagenes'];
        $req_skusproductos = $request['req_skusproductos']; // array de skus []
        $req_sku = $request['req_sku'];

        if($req_eliminarimagenes == true){

            foreach ($req_skusproductos as $sku) {
                $proe = proproductos::where('prosku', $sku)->first();
                $proe->proimagen = "/";
                $proe->update();
            }

        }else{

            $proe = proproductos::where('prosku', $req_sku)->first();
            $proe->proimagen = "/";
            $proe->update();

        }

        $requestsalida = response()->json([
            "mensaje"   => "Productos eliminados",
        ]);

        return $requestsalida;

    }

    public function AisngarImagensColumnasPrueba()
    {
        
        $pros = proproductos::where('proespromocion', 1)->get();

        foreach ($pros as $pro ) {
            
            prppromocionesproductos::join('prmpromociones as prm', 'prm.prmid', 'prppromocionesproductos.prmid')
                                    ->where('prm.fecid', '>' ,59)
                                    ->where('proid', $pro->proid)
                                    ->update(['prpimagen' => $pro->proimagen]);

            prbpromocionesbonificaciones::join('prmpromociones as prm', 'prm.prmid', 'prbpromocionesbonificaciones.prmid')
                                    ->where('prm.fecid', '>' ,59)
                                    ->where('proid', $pro->proid)
                                    ->update(['prbimagen' => $pro->proimagen]);

        }


        // ALTER TABLE `prppromocionesproductos` CHANGE `prpoimagen` `prpoimagen` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/';
        // ALTER TABLE `prbpromocionesbonificaciones` CHANGE `prboimagen` `prboimagen` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/';

        // ALTER TABLE `prppromocionesproductos` ADD `prpoimagen` VARCHAR(100) NOT NULL DEFAULT '/' ;
        // ALTER TABLE `prbpromocionesbonificaciones` ADD `prboimagen` VARCHAR(100) NOT NULL DEFAULT '/' ;



    }

}
