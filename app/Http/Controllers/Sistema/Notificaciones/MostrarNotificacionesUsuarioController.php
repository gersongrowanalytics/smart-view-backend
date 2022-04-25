<?php

namespace App\Http\Controllers\Sistema\Notificaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\nusnotificacionesusuarios;
use App\usuusuarios;
use DateTime;

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
                                            ->orderBy('nusnotificacionesusuarios.created_at', 'DESC')
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

                
                $fecha_actual = new DateTime("now");

                foreach($nuss as $posNus => $nus){

                    $fecha_notificacion = new DateTime($nus['nusfechaenviada']);

                    $diff = $fecha_notificacion->diff($fecha_actual);

                    $dif_minutos = ( ($diff->days * 24 ) * 60 ) + ( $diff->i );
                    $dif_horas = $diff->h;
                    $dif_segundos = ( ($diff->days * 24 ) * 60 ) + ( $diff->i * 60 ) + $diff->s;
                    $dif_dias = $diff->days;

                    $texto_fecha = "Hace ";

                    if($dif_dias <= 0){

                        if($dif_horas > 0){
                
                            $texto_fecha = $texto_fecha.$dif_horas." horas";
                
                        }else{
                            if($dif_minutos <= 0){
                                $texto_fecha = $texto_fecha.$dif_segundos." segundos";
                            }else{
                                $texto_fecha = $texto_fecha.$dif_minutos." minutos";
                            }
                        }
                
                    }else{
                
                        if($dif_dias == 1){
                            $texto_fecha = "Ayer";
                        }else{
                            $texto_fecha = $texto_fecha.$dif_dias." días";
                        }
                    }

                    $nuss[$posNus]['textofechacreada'] = $texto_fecha;

                }

                foreach($nuss as $nus){

                    if($nus['nusleyo'] == true){
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
