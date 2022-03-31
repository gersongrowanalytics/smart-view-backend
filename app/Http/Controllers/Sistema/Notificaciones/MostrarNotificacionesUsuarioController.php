<?php

namespace App\Http\Controllers\Sistema\Notificaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\nusnotificacionesusuarios;
use App\usuusuarios;

class MostrarNotificacionesUsuarioController extends Controller
{
    public function MostrarNotificacionesUsuario(Request $request)
    {

        $usutoken = $request->header('api_token');
        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        $nuss = array();
        $notificaciones_nuevas   = [];
        $notificaciones_antiguas = [];

        if($usu){

            $nuss = nusnotificacionesusuarios::join('tnotiposnotificaciones as tno', 'tno.tnoid', 'nusnotificacionesusuarios.tnoid')
                                            ->where('usuid', $usu->usuid)
                                            ->get([
                                                'tnotipo',
                                                'tnotitulo',
                                                'tnodescripcion',
                                                'tnoimagen',
                                                'tnolink',
                                                'nusfechaenviada',
                                                'nusleyo'
                                            ]);

            if(sizeof($nuss)){

                foreach($nuss as $nus){

                    if($nus->nusleyo == true){
                        $notificaciones_antiguas[] = $nus;
                    }else{
                        $notificaciones_nuevas[] = $nus;
                    }

                }

            }else{

            }

        }else{

        }

        $requestsalida = response()->json([
            "respuesta"    => true,
            "data"         => $nuss,
            "not_nuevas"   => $notificaciones_nuevas,
            "not_antiguas" => $notificaciones_antiguas
        ]);
        
        return $requestsalida;

    }
}
