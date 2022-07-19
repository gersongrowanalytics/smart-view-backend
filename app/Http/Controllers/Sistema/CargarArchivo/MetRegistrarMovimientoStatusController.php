<?php

namespace App\Http\Controllers\Sistema\CargarArchivo;

use App\coacontrolarchivos;
use App\fecfechas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MetRegistrarMovimientoStatusController extends Controller
{
    public function MetRegistrarMovimientoStatus ($badid, $carid, $fecid)
    {
        // $respuesta = false;

        // $fec = fecfechas::where('fecid', $fecid)->first(['fecano','fecmes']);

        // if ($fec) {
        //     // $coa = coacontrolarchivos::join('fecfechas as fec', 'fec.fecid', 'coacontrolarchivos.fecid')
        //     //                         ->where('badid', $badid)
        //     //                         ->where('fec.fecano', $fec->fecano)
        //     //                         ->where('fec.fecmes', $fec->fecmes)
        //     //                         ->first('coaid');
            
        //     // if ($coa) {
        //     //     $coa->carid = $carid;
        //     //     $coa->estid = 3;
        //     //     if ($coa->update()) {
        //     //         $respuesta = true;
        //     //     }
        //     // }
        // }      
        
        $respuesta = true;
        return $respuesta;
    }
}
