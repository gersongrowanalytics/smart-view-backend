<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\ListaPrecios\TipoRebate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tretiposrebates;

class MostrarTipoRebateDescargaLPController extends Controller
{
    public function MostrarTipoRebate ()
    {
        // 15 -> ZB
        // 26 -> ZA
        // 24 -> ZC
        $rebates = ["26","15","24"];
        $mensaje = "Se obtuvieron los tipos de rebates exitosamente";
        $respuesta = true;

        foreach ($rebates as $key => $rebate) {            
            $tipoRebate = tretiposrebates::where("treid", $rebate)
                                            ->first();
            if ($tipoRebate) {
                $arrayRebates[] = $tipoRebate;
            }else{
                $respuesta = false;
                $mensaje = "No existe el id del tipo de rebate ingresado: ".$rebate;
                $arrayRebates = [];
                break;
            }
        }

        $requestsalida = response()->json([
            "respuesta"  => $respuesta,
            "mensaje"    => $mensaje,
            "data"       => $arrayRebates,
        ]);
        
        return $requestsalida;
    }
}
