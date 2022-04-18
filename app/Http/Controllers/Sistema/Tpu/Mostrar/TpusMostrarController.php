<?php

namespace App\Http\Controllers\Sistema\Tpu\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\tputiposusuarios;

class TpusMostrarController extends Controller
{
    public function MostrarTpus(Request $request)
    {
        $respuesta  = true;
        $mensaje    = "Se cargaron todos los tpus satisfactoriamente";
        $data       = [];

        $tpus = tputiposusuarios::get(['tpuid', 'tpunombre']);

        $arr_tpus = array();

        foreach($tpus as $posicionTpu => $tpu){

            if($posicionTpu == 0){
                $arr_tpus[] = array(
                    "tpuid" => 0,
                    "tpunombre" => "Nombre Tipo Usuario"
                );
            }

            $arr_tpus[] = array(
                "tpuid" => $tpu->tpuid,
                "tpunombre" => $tpu->tpunombre
            );

        }


        if(sizeof($tpus) > 0){
            // $data = $tpus;
            $data = $arr_tpus;
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos, no se encontraron Tpus registrados";
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $data
        ]);
    }
}
