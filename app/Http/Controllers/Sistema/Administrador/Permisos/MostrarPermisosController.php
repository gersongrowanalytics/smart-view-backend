<?php

namespace App\Http\Controllers\Sistema\Administrador\Permisos;

use App\Http\Controllers\Controller;
use App\pempermisos;
use Illuminate\Http\Request;

class MostrarPermisosController extends Controller
{
    public function MostrarPermisos()
    {
        $respuesta = false;
        $mensaje   = '';

        // $pem = pempermisos::join('tpemtipospermisos as tpem', 'tpem.tpemid', 'pempermisos.tpemid')
        $pem = pempermisos::get([
                                'pempermisos.pemid',
                                // 'tpem.tpemnombre',
                                'pempermisos.pemnombre',
                                'pempermisos.pemslug',
                                'pempermisos.pemruta',
                                'pempermisos.created_at'
                            ]);

        if (sizeof($pem) > 0) {
            $respuesta      = true;
            $mensaje        = 'Los permisos se cargaron satisfactoriamente';
        }else{
            $respuesta      = false;
            $mensaje        = 'Los permisos no se cargaron satisfactoriamente';
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "permisos"  => $pem
        ]);

        return $requestsalida;
    }
}
