<?php

namespace App\Http\Controllers\Sistema\ControlProductos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\proproductos;
use App\prppromocionesproductos;
use App\prbpromocionesbonificaciones;

class MostrarProductosController extends Controller
{
    public function MostrarProductos(Request $request)
    {

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
                                            'proproductos.updated_at'
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
                                            'proproductos.updated_at'
                                        ]);


        $requestsalida = response()->json([
            "prosSinImagenes" => $prosSinImagenes,
            "prosConImagenes" => $prosConImagenes,
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

        $proe = proproductos::where('prosku', $req_prosku)->first();

        if($proe){

            list(, $base64) = explode(',', $req_imagen);
            $fichero = '/Sistema/promociones/IMAGENES/PRODUCTOSNUEVO/'.$fechaActual;
            $archivo = base64_decode($base64);
            file_put_contents(base_path().'/public'.$fichero.$req_prosku, $archivo);

            $proe->proimagen = env('APP_URL').$fichero.$req_prosku;
            $proe->update();

            prppromocionesproductos::join('prmpromociones as prm', 'prm.prmid', 'prppromocionesproductos.prmid')
                                    ->where('proid', $proe->proid)
                                    ->where('prm.fecid', '<' ,59)
                                    ->update(['prpoimagen' => $proe->proimagen]);

            prbpromocionesbonificaciones::join('prmpromociones as prm', 'prm.prmid', 'prppromocionesproductos.prmid')
                                    ->where('proid', $pro->proid)
                                    ->where('prm.fecid', '<' ,59)
                                    ->update(['prboimagen' => $proe->proimagen]);


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
            
            prppromocionesproductos::where('proid', $pro->proid)
                                    ->update(['prpoimagen' => $pro->proimagen]);

            prbpromocionesbonificaciones::where('proid', $pro->proid)
                                    ->update(['prboimagen' => $pro->proimagen]);

        }


        // ALTER TABLE `prppromocionesproductos` CHANGE `prpoimagen` `prpoimagen` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/';
        // ALTER TABLE `prbpromocionesbonificaciones` CHANGE `prboimagen` `prboimagen` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/';

        // ALTER TABLE `prppromocionesproductos` ADD `prpoimagen` VARCHAR(100) NOT NULL DEFAULT '/' ;
        // ALTER TABLE `prbpromocionesbonificaciones` ADD `prboimagen` VARCHAR(100) NOT NULL DEFAULT '/' ;



    }

}
