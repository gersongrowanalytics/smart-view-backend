<?php

namespace App\Http\Controllers\Sistema\CargarArchivo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\nusnotificacionesusuarios;

class RegistrarNotificacionController extends Controller
{
    public function RegistrarNotificacion(
        $usuid,
        $carid,
        $tipo,
        $logs,
        $mensaje
    )
    {

        $respuesta = false;

        $logs = json_encode($logs);

        $numerosNotificaciones = nusnotificacionesusuarios::where('usuid', $usuid)->count();

        $nusn = new nusnotificacionesusuarios;
        $nusn->usuid      = $usuid;
        $nusn->carid      = $carid;
        $nusn->estid      = 1;
        $nusn->nusnumero  = $numerosNotificaciones+1;
        $nusn->nustipo    = $tipo;
        $nusn->nuslogs    = $logs;
        $nusn->nusmensaje = $mensaje;
        if($nusn->save()){
            $respuesta = true;
        }

        return $respuesta;
    }
}
