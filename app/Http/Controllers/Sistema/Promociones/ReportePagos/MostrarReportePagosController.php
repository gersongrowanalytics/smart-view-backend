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
                $totalImporte = 0;
                foreach($reps as $posicionRep => $rep){
                    
                    $totalImporte = $totalImporte + $rep->repimporte;

                    if($posicionRep == 0){
                        $arrayTitulos = array(
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "Sold To", "width" => array("wpx" => 150)),
                            array("title" => "Clientes", "width" => array("wpx" => 150)),
                            array("title" => "Concepto", "width" => array("wpx" => 150)),
                            array("title" => "Tipo Doc.", "width" => array("wpx" => 150)),
                            array("title" => "Nro. Doc.", "width" => array("wpx" => 150)),
                            array("title" => "Fecha Doc.", "width" => array("wpx" => 150)),
                            array("title" => "Categoría", "width" => array("wpx" => 150)),
                            array("title" => "Importe (sin igv)", "width" => array("wpx" => 150))
                        );
                        $nuevoArray[0]['columns'] = $arrayTitulos;
                    }

                    $arrayFilaExcel = array(
                        array("value" => ""),
                        array("value" => $rep->sucsoldto),
                        array("value" => $rep->sucnombre),
                        array("value" => $rep->repconcepto, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->reptipodocumento, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->repnumerodocumento, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->repfechadocumento, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->repcategoria, "style" => array("font" => array("sz" => "10"))),
                        array("value" => floatval($rep->repimporte), "style" => array("font" => array("sz" => "10"))),
                    );

                    $nuevoArray[0]['data'][] = $arrayFilaExcel;
                    
                }

                
                $arrayFilaExcel = array(
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => "")
                );
                
                $nuevoArray[0]['data'][] = $arrayFilaExcel;

                $arrayFilaExcel = array(
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => ""),
                    array("value" => "Total"),
                    array("value" => floatval($totalImporte))
                );

                $nuevoArray[0]['data'][] = $arrayFilaExcel;
                

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
