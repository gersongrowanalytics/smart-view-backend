<?php

namespace App\Http\Controllers\Sistema\CargarArchivo;

use App\coacontrolarchivos;
use App\fecfechas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MetRegistrarMovimientoStatusController extends Controller
{
    public function MetRegistrarMovimientoStatus ($badid, $carid)
    {
        $respuesta = false;

        $meses = ["ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SET", "OCT", "NOV", "DIC"];
        date_default_timezone_set("America/Lima");
        $anioActual = date('Y');
        $mesActual  = date('n');
        $mesActualTexto = $meses[$mesActual - 1];
        
        $coa = coacontrolarchivos::join('fecfechas as fec', 'fec.fecid', 'coacontrolarchivos.fecid')
                                ->where('badid', $badid)
                                ->where('fec.fecano', $anioActual)
                                ->where('fec.fecmes', $mesActualTexto)
                                ->first('coaid');

        if ($coa) {
            $coa->carid = $carid;
            $coa->estid = 3;
            if ($coa->update()) {
                $respuesta = true;
            }
        }
             
        // $respuesta = true;
        return $respuesta;
    }
}
