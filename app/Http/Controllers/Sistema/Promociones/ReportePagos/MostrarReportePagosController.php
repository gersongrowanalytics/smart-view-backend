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
                            array("title" => "", "width" => array("wpx" => 200)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150))
                        );
                        $nuevoArray[0]['columns'] = $arrayTitulos;

                        // "fill" => array(
                        //     "patternType" => 'solid',
                        //     "fgColor" => array(
                        //         "rgb" => "FF31859B"
                        //     )
                        // ) 
                        $arrayFilaExcel = array(
                            array("value" => ""),
                            array(
                                "value" => "Registro de pagos", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "22",
                                        "color" => array(
                                            "rgb" => "FF31859B"
                                        )
                                    ), 
                                    
                                )
                            ),
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
                            array(
                                "value" => "Año promoción",
                                "style" => array(
                                    "font" => array(
                                        "sz" => "12",
                                        "color" => array(
                                            "rgb" => "FF31859B"
                                        )
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFFFFFCC"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => $anio,
                                "style" => array(
                                    "font" => array(
                                        "sz" => "12",
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFFFFFCC"
                                        )
                                    )
                                )
                            ),
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
                            array(
                                "value" => "Mes promoción",
                                "style" => array(
                                    "font" => array(
                                        "sz" => "12",
                                        "color" => array(
                                            "rgb" => "FF31859B"
                                        )
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFFFFFCC"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => $rep->fecmes,
                                "style" => array(
                                    "font" => array(
                                        "sz" => "12",
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFFFFFCC"
                                        )
                                    )
                                )
                            ),
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
                            array("value" => ""),
                            array("value" => "")
                        );
                        
                        $nuevoArray[0]['data'][] = $arrayFilaExcel;

                        $arrayFilaExcel = array(
                            array("value" => ""),
                            array("value" => "Detalle de reconocimiento", "style" => array("font" => array("sz" => "18"))),
                        );
                        
                        $nuevoArray[0]['data'][] = $arrayFilaExcel;


                        $arrayFilaExcel = array(
                            array("value" => ""),
                            array(
                                "value" => "Sold To", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFD0EAF0"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Clientes", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11",
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFD0EAF0"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Concepto", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11",
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFD0EAF0"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Tipo Doc", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11",
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFD0EAF0"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Nro. Doc", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11",
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFD0EAF0"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Fecha Doc.", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11",
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFD0EAF0"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Categoría", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11",
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFD0EAF0"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Importe (sin igv)", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11",
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFD0EAF0"
                                        )
                                    )
                                )
                            )
                        );
                        
                        $nuevoArray[0]['data'][] = $arrayFilaExcel;
                    }

                    $celdaPintada = array();

                    if($posicionRep % 2 == 0){
                        $celdaPintada = array();
                    }else{

                    }

                    $arrayFilaExcel = array(
                        array("value" => ""),
                        array("value" => $rep->sucsoldto, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->sucnombre, "style" => array("font" => array("sz" => "10"))),
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
                    array("value" => "Total", "style" => array("font" => array("sz" => "12","bold" => true),)),
                    array("value" => floatval($totalImporte), "style" => array("font" => array("sz" => "12","bold" => true),)),
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
