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

        $prosSinImagenes = proproductos::where('proimagem', "/")->limit(200)->get();
        $prosConImagenes = proproductos::where('proimagem', "!=", "/")->limit(200)->get();


        $requestsalida = response()->json([
            "prosSinImagenes" => $prosSinImagenes,
            "prosConImagenes" => $prosConImagenes,
        ]);

        return $requestsalida;

    }

    public function ModificarImagenProductos()
    {

        $logs = array(
            "PRODUCTOS_MODIFICADO_PRP" => [],
            "PRODUCTOS_MODIFICADO_PRB" => [],
            "NO_SE_ENCONTRO_PRODUCTO" => []
        );

        proproductos::where('proimagem', '!=', "/")
                    ->update(['proimagem' => "/"]);

        $pros = proproductos::get();

        foreach ($pros as $posicionPro => $pro) {
            
            $prp = prppromocionesproductos::where('proid', $pro->proid)->first();

            if($prp){
                $proe = proproductos::find($pro->proid);
                $proe->proimagem = $prp->prpimagen;
                $proe->update();

                $logs["PRODUCTOS_MODIFICADO_PRP"][] = "Imagen de productos: ".$pro->proid." con la imagen: ".$prp->prpimagen;

            }else{
                $prb = prbpromocionesbonificaciones::where('proid', $pro->proid)->first();

                if($prb){
                    $proe = proproductos::find($pro->proid);
                    $proe->proimagem = $prb->prbimagen;
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
}
