<?php

namespace App\Http\Controllers\Sistema\ControlArchivos\Complementos;

use App\badbasedatos;
use App\coacontrolarchivos;
use App\fecfechas;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class MetCrearRegistrosControlArchivosController extends Controller
{
    public function MetCrearRegistrosControlArchivos (Request $request)
    {
        $respuesta = false;
        $mensaje = [];

        $mesAbrev = array("ENE","FEBR","MAR","ABR","MAY","JUN","JUL","AGO","SET","OCT","NOV","DIC");
        
        $req_anio = $request['req_anio'];
        $req_mes  = $request['req_mes'];

        date_default_timezone_set("America/Lima");
        $fecha = "01-".$req_mes."-".$req_anio;
        $fechaDate = new DateTime($fecha);

        $fecn = new fecfechas();
        $fecn->fecfecha     = $fechaDate;
        $fecn->fecdia       = 01;
        $fecn->fecano       = $req_anio;
        $fecn->fecmes       = $mesAbrev[$req_mes-1];
        $fecn->fecmesnumero = $req_mes;

        if ($fecn->save()) {
            
            $bads = badbasedatos::get(['badid', 'badnombre', 'areid']);

            if (sizeof($bads) > 0) {
                foreach ($bads as $key => $bad) {

                    $coan = new coacontrolarchivos();
                    // $coan->usuid = 639;
                    // $coan->carid = 15;
                    $coan->fecid = $fecn->fecid;
                    // $coan->estid = 4;
                    $coan->badid = $bad['badid'];
                    // $coan->coadiasretraso = 5;
                    if ($req_mes == '02' || $req_mes == '2') {
                        $coan->coafechacaducidad = new DateTime('25 '.$mesAbrev[$req_mes-1].' '.$req_anio); 
                    }else{
                        $coan->coafechacaducidad = new DateTime('30 '.$mesAbrev[$req_mes-1].' '.$req_anio); 
                    }

                    if ($coan->save()) {
                        $respuesta = true;
                        $mensaje   = ["Los registros del coacontrolarchivos fueron creados exitosamente"];
                    }else{
                        break;
                        $respuesta = false;
                        $mensaje   = ["Lo siento, error al momento de crear un registro del coacontrolarchivos"];
                    }
                }
            } 
        }else{
            $respuesta = false;
            $mensaje   = ["Lo siento, error al momento de crear un registro de la fecha"];
        }

        return response()->json([
            'respuesta' => $respuesta,
            'mensaje'   => $mensaje
        ]);
    }
}
