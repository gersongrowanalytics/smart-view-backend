<?php

namespace App\Http\Controllers\Sistema\Promociones\ReportePagos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\repreconocimientopago;
use App\fecfechas;
use App\sucsucursales;
use App\usuusuarios;

class MostrarReportePagosController extends Controller
{
    public function MostrarReportePagos(Request $request)
    {

        $usutoken   = $request['usutoken'];
        $sucs       = $request['sucs'];
        $dia        = "01";
        $mes        = $request['mes'];
        $anio       = $request['ano'];


        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['ususoldto']);

        $respuesta      = true;
        $mensaje        = '';
        $datos          = []; 
        $mensajeDetalle = '';
        $mensajedev     = null;

        try{

            $usss = sucsucursales::where(function ($query) use($sucs) {
                                    foreach($sucs as $suc){
                                        if(isset($suc['sucpromociondescarga'])){
                                            if($suc['sucpromociondescarga'] == true){
                                                $query->orwhere('sucid', $suc['sucid']);
                                            }
                                        }
                                    }
                                })
                                ->get(['sucsoldto', 'sucnombre']);

            $nuevoArray = array(
                array(
                    "columns" => [],
                    "data"    => []
                )
            );

            $fec = fecfechas::where('fecdia', 'LIKE', "%".$dia."%")
                            ->where('fecmes', 'LIKE', "%".$mes."%")
                            ->where('fecano', 'LIKE', "%".$anio."%")
                            ->first(['fecid']);

            if($fec){
                
                $reps = repreconocimientopago::join('sucsucursales as suc', 'suc.sucid', 'repreconocimientopago.sucid')
                                                ->join('fecfechas as fec', 'fec.fecid', 'repreconocimientopago.fecid')
                                                ->where('fec.fecid', $fec->fecid)
                                                ->where(function ($query) use($sucs) {
                                                    foreach($sucs as $suc){
                                                        if(isset($suc['sucpromociondescarga'])){
                                                            if($suc['sucpromociondescarga'] == true){
                                                                $query->orwhere('suc.sucid', $suc['sucid']);
                                                            }
                                                        }
                                                    }
                                                })
                                                ->get([
                                                    'repid',
                                                    'fec.fecid',
                                                    'fec.fecmes',
                                                    'fec.fecano',
                                                    'sucsoldto',
                                                    'sucnombre',
                                                    'repconcepto',
                                                    'reptipodocumento',
                                                    'repnumerodocumento',
                                                    'repfechadocumento',
                                                    'repcategoria',
                                                    'repimporte'
                                                ]);

                foreach($reps as $posicionRep => $rep){

                    if($posicionRep == 0){
                        $arrayTitulos = array(
                            array("title" => "Sold To"),
                            array("title" => "Clientes"),
                            array("title" => "Concepto"),
                            array("title" => "Tipo Doc."),
                            array("title" => "Nro. Doc."),
                            array("title" => "Fecha Doc."),
                            array("title" => "Categoría"),
                            array("title" => "Importe (sin igv)")
                        );
                        $nuevoArray[0]['columns'] = $arrayTitulos;
                    }

                    $arrayFilaExcel = array(
                        array("value" => $rep->sucsoldto),
                        array("value" => $rep->sucnombre),
                        array("value" => $rep->repconcepto),
                        array("value" => $rep->reptipodocumento),
                        array("value" => $rep->repnumerodocumento),
                        array("value" => $rep->repfechadocumento),
                        array("value" => $rep->repimporte)
                    );

                    $nuevoArray[0]['data'][] = $arrayFilaExcel;
                }

                $datos     = $nuevoArray;
            }else{
                $respuesta = false;
                $mensaje = "Lo sentimos, no pudimos encontrar la fecha seleccionada";
                $mensajeDetalle = "Vuelve a seleccionar la fecha o comunicate con soporte";
            }



        }catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $respuesta      = false;
        }


        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
        ]);

        return $requestsalida;

    }
}
