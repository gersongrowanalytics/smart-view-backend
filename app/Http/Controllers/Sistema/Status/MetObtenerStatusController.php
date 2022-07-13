<?php

namespace App\Http\Controllers\Sistema\Status;

use App\coacontrolarchivos;
use App\Http\Controllers\Controller;
use App\Mail\MailInformarStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use function PHPSTORM_META\map;

class MetObtenerStatusController extends Controller
{
    public function MetObtenerStatus(Request $request)
    {
        $respuesta = false;
        $mensaje   = "";
        $datos      = [];

        $coas = coacontrolarchivos::leftJoin('carcargasarchivos as car', 'car.carid', 'coacontrolarchivos.carid')
                                    ->leftJoin('fecfechas as feccar', 'feccar.fecid', 'car.fecid')
                                    ->leftJoin('usuusuarios as usucar', 'usucar.usuid', 'car.usuid')
                                    ->leftJoin('perpersonas as percar', 'percar.perid', 'usucar.perid')
                                    ->join('usuusuarios as usu', 'usu.usuid', 'coacontrolarchivos.usuid')
                                    ->join('perpersonas as perresp', 'perresp.perid', 'usu.perid')
                                    ->join('estestados as est' ,'est.estid', 'coacontrolarchivos.estid')
                                    ->join('areareas as are', 'are.areid', 'coacontrolarchivos.areid')
                                    ->orderby('coaid')
                                    ->get([
                                        'coaid',
                                        'coabasedatos',
                                        'coadiasretraso',
                                        'are.areid',
                                        'are.arenombre',
                                        'car.carid',
                                        'car.carnombrearchivo',  
                                        'car.carurl',
                                        'usu.usuid as usuidresponsable',
                                        'usu.usuusuario as usuusuarioresponsable',  
                                        'usucar.usuid as usuidsubida',
                                        'percar.pernombrecompleto as pernombrecompletosubida', 
                                        'feccar.fecfecha',
                                        'est.estid',
                                        'est.estnombre',
                                        'coafechacaducidad',
                                        'car.created_at as fechaCar',
                                        'perresp.pernombrecompleto as pernombrecompletoresponsable', 
                                    ]);


        if (sizeof($coas) > 0) {
            $respuesta = true;
            $mensaje   = "Se obtuvieron los registros con exito";

            foreach ($coas as $key => $coa) {
                $coa['fecfecha'] = $this->MetFormatearFecha($coa->fecfecha);
                $datos[] = [
                    "areaid"       => $coa->areid,
                    "area"         => $coa->arenombre,
                    "base_datos"   => $coa->coabasedatos,
                    "responsable"  => $coa->pernombrecompletoresponsable,
                    "usuario"      => $coa->pernombrecompletosubida,
                    "deadline"     => "24 May 2022",
                    "fecha_carga"  => $coa->fecfecha,
                    "dias_retraso" => $coa->coadiasretraso,
                    "status"       => $coa->estnombre

                ];
            }
            Mail::to(['marco.espinoza@grow-analytics.com.pe','jeanmarcoe@gmail.com'])->send(new MailInformarStatus($datos));
        }else{
            $respuesta = false;
            $mensaje   = "Lo siento, no se encontraron registros";
        }

        return response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "datos"     => $coas
        ]);

    }

    public function MetFormatearFecha($fecha)
    {
        $meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
        $anioActualizacion = date("Y", strtotime($fecha));
        $mesActualizacion = $meses[date('n', strtotime($fecha))-1];
        $diaActualizacion = date("j", strtotime($fecha));
        $fechaFormateada = $diaActualizacion." ".$mesActualizacion." ".$anioActualizacion;
        return $fechaFormateada;
    }
}
