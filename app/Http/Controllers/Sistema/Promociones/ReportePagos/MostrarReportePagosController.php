<?php

namespace App\Http\Controllers\Sistema\Promociones\ReportePagos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\prlpromocionesliquidadas;
use App\repreconocimientopago;
use App\fecfechas;
use App\sucsucursales;
use App\usuusuarios;

class MostrarReportePagosController extends Controller
{
    public function MostrarReportePagos(Request $request)
    {

        $fechainicio = $request['fechainicio'];
        $fechafinal  = $request['fechafinal'];

        $usutoken   = $request['usutoken'];
        $sucs       = $request['sucs'];
        $dia        = "01";
        $mes        = $request['mes'];
        $anio       = $request['ano'];


        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['ususoldto']);

        $respuesta      = true;
        $mensaje        = '';
        $datos          = []; 
        $datosReconocimiento = [];
        $datosPromociones = [];
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

            $nuevoArrayReconocimiento = array(
                array(
                    "columns" => [],
                    "data"    => []
                )
            );

            $nuevoArrayPromocionesLiquidadas = array(
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
                                                ->leftjoin('cascanalessucursales as cas', 'cas.casid', 'suc.casid')
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
                                                    'cas.casnombre',
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
                                                    'repimporte',
                                                    'repmonedalocal',
                                                    'reptexto'
                                                ]);
                $totalImporte = 0;
                foreach($reps as $posicionRep => $rep){
                    
                    $totalImporte = $totalImporte + $rep->repimporte;

                    if($posicionRep == 0){
                        $arrayTitulos = array(
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100))
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
                                            "rgb" => "FFA7D8E3"
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
                                            "rgb" => "FFA7D8E3"
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
                                            "rgb" => "FFA7D8E3"
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
                                            "rgb" => "FFA7D8E3"
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
                                            "rgb" => "FFA7D8E3"
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
                                            "rgb" => "FFA7D8E3"
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
                                            "rgb" => "FFA7D8E3"
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
                                            "rgb" => "FFA7D8E3"
                                        )
                                    )
                                )
                            )
                        );
                        
                        $nuevoArray[0]['data'][] = $arrayFilaExcel;
                    }

                    $celdaPintada = array();

                    if($posicionRep % 2 == 0){
                        $celdaPintada = array("patternType" => 'solid',"fgColor" => array("rgb" => "FFDCEDF4"));
                    }else{
                        $celdaPintada = array("patternType" => 'solid',"fgColor" => array("rgb" => "FFFFFFFF"));
                    }

                    $arrayFilaExcel = array(
                        array("value" => ""),
                        array("value" => $rep->sucsoldto, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                        array("value" => $rep->sucnombre, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                        array("value" => $rep->repconcepto, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                        array("value" => $rep->reptipodocumento, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                        array("value" => $rep->repnumerodocumento, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                        array("value" => $rep->repfechadocumento, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                        array("value" => $rep->repcategoria, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                        array("value" => floatval($rep->repimporte), "style" => array("font" => array("sz" => "10"),"fill" => $celdaPintada,"numFmt" => "#,##0.00")),
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
                    array("value" => floatval($totalImporte), "style" => array("font" => array("sz" => "12","bold" => true),"numFmt" => "#,##0.00")), 
                );

                $nuevoArray[0]['data'][] = $arrayFilaExcel;
                

                $datos     = $nuevoArray;


                // PLANTILLA PARA DESCARGAR RECONOCIMIENTO
                foreach($reps as $posicionRep => $rep){

                    if($posicionRep == 0){
                        $arrayTitulos = array(
                            array("title" => "", "width" => array("wpx" => 10)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 250)),
                        );
                        $nuevoArrayReconocimiento[0]['columns'] = $arrayTitulos;

                        $arrayFilaExcel = array(
                            array("value" => ""),
                            array(
                                "value" => "Detalle de reconocimiento", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "22",
                                        "color" => array(
                                            "rgb" => "FF31859B"
                                        )
                                    ), 
                                    
                                )
                            ),
                        );
                        
                        $nuevoArrayReconocimiento[0]['data'][] = $arrayFilaExcel;

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
                        
                        $nuevoArrayReconocimiento[0]['data'][] = $arrayFilaExcel;


                        $arrayFilaExcel = array(
                            array("value" => ""),
                            array(
                                "value" => "GBA", 
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
                                "value" => "Año promoción", 
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
                                "value" => "Mes promoción", 
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
                                "value" => "Cliente", 
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
                                "value" => "Tipo Doc.", 
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
                                "value" => "Nro. Doc.", 
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
                            ),
                            array(
                                "value" => "Moneda local", 
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
                                "value" => "Texto", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11",
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFD9D9D9"
                                        )
                                    )
                                )
                            ),
                        );
                        
                        $nuevoArrayReconocimiento[0]['data'][] = $arrayFilaExcel;
                    }


                    $arrayFilaExcel = array(
                        array("value" => ""),
                        array("value" => $rep->casnombre, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->fecano, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->fecmes, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->repconcepto, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->sucsoldto, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->sucnombre, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->reptipodocumento, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->repfechadocumento, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->repnumerodocumento, "style" => array("font" => array("sz" => "10"))),
                        array("value" => floatval($rep->repimporte), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                        array("value" => $rep->repmonedalocal, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->repcategoria, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $rep->reptexto, "style" => array("font" => array("sz" => "10"))),
                    );

                    $nuevoArrayReconocimiento[0]['data'][] = $arrayFilaExcel;
                    
                }

                $datosReconocimiento = $nuevoArrayReconocimiento;

                // PLANTILLA PARA DESCARGAR PROMOCIONES LIQUIDADAS
                $prls = prlpromocionesliquidadas::join('sucsucursales as suc', 'suc.sucid', 'prlpromocionesliquidadas.sucid')
                                                ->leftjoin('cascanalessucursales as cas', 'cas.casid', 'suc.casid')
                                                ->join('fecfechas as fec', 'fec.fecid', 'prlpromocionesliquidadas.fecid')
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
                                                    'prlpromocionesliquidadas.prlid',
                                                    'cas.casnombre',
                                                    'fec.fecid',
                                                    'fec.fecano',
                                                    'fec.fecmes',
                                                    'prlconcepto',
                                                    'prlejecutivo',
                                                    'prlgrupo',
                                                    'suc.sucsoldto',
                                                    'sucnombre',
                                                    'prlcompra',
                                                    'prlbonificacion',
                                                    'prlmecanica',
                                                    'prlcategoria',
                                                    'prlsku',
                                                    'prlproducto',
                                                    'prlskubonificado',
                                                    'prlproductobonificado',
                                                    'prlplancha',
                                                    'prlcombo',
                                                    'prlreconocerxcombo',
                                                    'prlreconocerxplancha',
                                                    'prltotal',
                                                    'prlliquidacionso',
                                                    'prlliquidacioncombo',
                                                    'prlliquidacionvalorizado',
                                                    'prlliquidaciontotalpagar'
                                                ]);

                foreach ($prls as $posicionPrl => $prl) {
                    
                    if($posicionPrl == 0){
                        $arrayTitulos = array(
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 150)),

                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 80)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 100)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 50)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                            array("title" => "", "width" => array("wpx" => 150)),
                        );
                        $nuevoArrayPromocionesLiquidadas[0]['columns'] = $arrayTitulos;

                        $arrayFilaExcel = array(
                            array("value" => ""),
                            array(
                                "value" => "GBA", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Año promoción", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Mes promoción", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
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
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Ejecutivo", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Grupo", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
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
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Cliente", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Compra (UND)", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Bonificación (UND)", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),

                            array(
                                "value" => "Mecánica", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
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
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "SKU", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Producto", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Sku a Bonificar", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Producto a Bonificar", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Planchas a rotar o (Sell Out)", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "# Combos", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Reconocer x Combo S/IGV", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Reconocer x PL S/IGV", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "Total Soles S/IGV", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFC6ED59"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "LIQUIDACION:  Sell out planchas", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFF2AD68"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "LIQUIDACION: Combos usados", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFF2AD68"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "LIQUIDACION: Valorizado", 
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFF2AD68"
                                        )
                                    )
                                )
                            ),
                            array(
                                "value" => "LIQUIDACION: Total a Pagar",
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11", 
                                        "bold" => true
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => "FFF2AD68"
                                        )
                                    )
                                )
                            ),
                        );
                        
                        $nuevoArrayPromocionesLiquidadas[0]['data'][] = $arrayFilaExcel;
                    }

                    
                    $arrayFilaExcel = array(
                        array("value" => ""),
                        array("value" => $prl->casnombre, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->fecano, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->fecmes, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlconcepto, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlejecutivo, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlgrupo, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->sucsoldto, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->sucnombre, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlcompra, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlbonificacion, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlmecanica, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlcategoria, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlsku, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlproducto, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlskubonificado, "style" => array("font" => array("sz" => "10"))),
                        array("value" => $prl->prlproductobonificado, "style" => array("font" => array("sz" => "10"))),
                        array("value" => floatval($prl->prlplancha), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                        array("value" => floatval($prl->prlcombo), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                        array("value" => floatval($prl->prlreconocerxcombo), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                        array("value" => floatval($prl->prlreconocerxplancha), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                        array("value" => floatval($prl->prltotal), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                        array("value" => floatval($prl->prlliquidacionso), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                        array("value" => floatval($prl->prlliquidacioncombo), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                        array("value" => floatval($prl->prlliquidacionvalorizado), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                        array("value" => floatval($prl->prlliquidaciontotalpagar), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00"))
                    );

                    $nuevoArrayPromocionesLiquidadas[0]['data'][] = $arrayFilaExcel;
                }

                $datosPromociones = $nuevoArrayPromocionesLiquidadas;

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
            'datosReconocimiento' => $datosReconocimiento,
            'datosPromociones'    => $datosPromociones,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            "actualizacion"  => "Actualización 24 de Junio 2021"
        ]);

        return $requestsalida;

    }

    public function MostrarReportePagosXFechaIncioFechaFin(Request $request)
    {

        $fechaInicio = $request['fechaInicio'];
        $fechaFinal  = $request['fechaFinal'];

        $usutoken   = $request['usutoken'];
        $sucs       = $request['sucs'];
        $dia        = "01";
        $mes        = $request['mes'];
        $anio       = $request['ano'];


        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['ususoldto']);

        $respuesta      = true;
        $mensaje        = '';
        $datos          = []; 
        $datosReconocimiento = [];
        $datosPromociones = [];
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

            $nuevoArrayReconocimiento = array(
                array(
                    "columns" => [],
                    "data"    => []
                )
            );

            $nuevoArrayPromocionesLiquidadas = array(
                array(
                    "columns" => [],
                    "data"    => []
                )
            );

            // $fec = fecfechas::where('fecdia', 'LIKE', "%".$dia."%")
            //                 ->where('fecmes', 'LIKE', "%".$mes."%")
            //                 ->where('fecano', 'LIKE', "%".$anio."%")
            //                 ->first(['fecid']);

            $fechaInicio = new \DateTime(date("Y-m-d", strtotime($fechaInicio)));
            $fechaFinal  = new \DateTime(date("Y-m-d", strtotime($fechaFinal)));
            // $fec = fecfechas::where('fecfecha', $fecha)->first(['fecid']);

            $reps = repreconocimientopago::join('sucsucursales as suc', 'suc.sucid', 'repreconocimientopago.sucid')
                                            ->leftjoin('cascanalessucursales as cas', 'cas.casid', 'suc.casid')
                                            ->join('fecfechas as fec', 'fec.fecid', 'repreconocimientopago.fecid')
                                            ->whereBetween('fecfecha', [$fechaInicio, $fechaFinal])
                                            // ->where('fec.fecid', $fec->fecid)
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
                                                'cas.casnombre',
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
                                                'repimporte',
                                                'repmonedalocal',
                                                'reptexto'
                                            ]);
            $totalImporte = 0;
            foreach($reps as $posicionRep => $rep){
                
                $totalImporte = $totalImporte + $rep->repimporte;

                if($posicionRep == 0){
                    $arrayTitulos = array(
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 250)),
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
                    );
                    
                    $nuevoArray[0]['data'][] = $arrayFilaExcel;

                    // $arrayFilaExcel = array(
                    //     array("value" => ""),
                    //     array(
                    //         "value" => "Año promoción",
                    //         "style" => array(
                    //             "font" => array(
                    //                 "sz" => "12",
                    //                 "color" => array(
                    //                     "rgb" => "FF31859B"
                    //                 )
                    //             ),
                    //             "fill" => array(
                    //                 "patternType" => 'solid',
                    //                 "fgColor" => array(
                    //                     "rgb" => "FFFFFFCC"
                    //                 )
                    //             )
                    //         )
                    //     ),
                    //     array(
                    //         "value" => $anio,
                    //         "style" => array(
                    //             "font" => array(
                    //                 "sz" => "12",
                    //             ),
                    //             "fill" => array(
                    //                 "patternType" => 'solid',
                    //                 "fgColor" => array(
                    //                     "rgb" => "FFFFFFCC"
                    //                 )
                    //             )
                    //         )
                    //     ),
                    //     array("value" => ""),
                    //     array("value" => ""),
                    //     array("value" => ""),
                    //     array("value" => ""),
                    //     array("value" => ""),
                    //     array("value" => "")
                    // );
                    
                    // $nuevoArray[0]['data'][] = $arrayFilaExcel;

                    // $arrayFilaExcel = array(
                    //     array("value" => ""),
                    //     array(
                    //         "value" => "Mes promoción",
                    //         "style" => array(
                    //             "font" => array(
                    //                 "sz" => "12",
                    //                 "color" => array(
                    //                     "rgb" => "FF31859B"
                    //                 )
                    //             ),
                    //             "fill" => array(
                    //                 "patternType" => 'solid',
                    //                 "fgColor" => array(
                    //                     "rgb" => "FFFFFFCC"
                    //                 )
                    //             )
                    //         )
                    //     ),
                    //     array(
                    //         "value" => $rep->fecmes,
                    //         "style" => array(
                    //             "font" => array(
                    //                 "sz" => "12",
                    //             ),
                    //             "fill" => array(
                    //                 "patternType" => 'solid',
                    //                 "fgColor" => array(
                    //                     "rgb" => "FFFFFFCC"
                    //                 )
                    //             )
                    //         )
                    //     ),
                    //     array("value" => ""),
                    //     array("value" => ""),
                    //     array("value" => ""),
                    //     array("value" => ""),
                    //     array("value" => ""),
                    //     array("value" => "")
                    // );
                    
                    // $nuevoArray[0]['data'][] = $arrayFilaExcel;

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
                            "value" => "GBA", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFA7D8E3"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Año Promoción", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFA7D8E3"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Mes Promoción", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFA7D8E3"
                                    )
                                )
                            )
                        ),
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
                                        "rgb" => "FFA7D8E3"
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
                                        "rgb" => "FFA7D8E3"
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
                                        "rgb" => "FFA7D8E3"
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
                                        "rgb" => "FFA7D8E3"
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
                                        "rgb" => "FFA7D8E3"
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
                                        "rgb" => "FFA7D8E3"
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
                                        "rgb" => "FFA7D8E3"
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
                                        "rgb" => "FFA7D8E3"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Moneda local",
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFA7D8E3"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Texto",
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFA7D8E3"
                                    )
                                )
                            )
                        ),
                    );
                    
                    $nuevoArray[0]['data'][] = $arrayFilaExcel;
                }

                $celdaPintada = array();

                if($posicionRep % 2 == 0){
                    $celdaPintada = array("patternType" => 'solid',"fgColor" => array("rgb" => "FFDCEDF4"));
                }else{
                    $celdaPintada = array("patternType" => 'solid',"fgColor" => array("rgb" => "FFFFFFFF"));
                }

                $arrayFilaExcel = array(
                    array("value" => ""),
                    array("value" => $rep->casnombre, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->fecano, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->fecmes, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->sucsoldto, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->sucnombre, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->repconcepto, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->reptipodocumento, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->repnumerodocumento, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->repfechadocumento, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->repcategoria, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => floatval($rep->repimporte), "style" => array("font" => array("sz" => "10"),"fill" => $celdaPintada,"numFmt" => "#,##0.00")),
                    array("value" => $rep->repmonedalocal, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
                    array("value" => $rep->reptexto, "style" => array("font" => array("sz" => "10"), "fill" => $celdaPintada)),
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
                array("value" => ""),
                array("value" => ""),
                array("value" => ""),
                array("value" => "Total", "style" => array("font" => array("sz" => "12","bold" => true),)),
                array("value" => floatval($totalImporte), "style" => array("font" => array("sz" => "12","bold" => true),"numFmt" => "#,##0.00")), 
            );

            $nuevoArray[0]['data'][] = $arrayFilaExcel;
            
            $datos     = $nuevoArray;

            // PLANTILLA PARA DESCARGAR RECONOCIMIENTO
            foreach($reps as $posicionRep => $rep){

                if($posicionRep == 0){
                    $arrayTitulos = array(
                        array("title" => "", "width" => array("wpx" => 10)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 250)),
                    );
                    $nuevoArrayReconocimiento[0]['columns'] = $arrayTitulos;

                    $arrayFilaExcel = array(
                        array("value" => ""),
                        array(
                            "value" => "Detalle de reconocimiento", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "22",
                                    "color" => array(
                                        "rgb" => "FF31859B"
                                    )
                                ), 
                                
                            )
                        ),
                    );
                    
                    $nuevoArrayReconocimiento[0]['data'][] = $arrayFilaExcel;

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
                    
                    $nuevoArrayReconocimiento[0]['data'][] = $arrayFilaExcel;


                    $arrayFilaExcel = array(
                        array("value" => ""),
                        array(
                            "value" => "GBA", 
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
                            "value" => "Año promoción", 
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
                            "value" => "Mes promoción", 
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
                            "value" => "Cliente", 
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
                            "value" => "Tipo Doc.", 
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
                            "value" => "Nro. Doc.", 
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
                        ),
                        array(
                            "value" => "Moneda local", 
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
                            "value" => "Texto", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFD9D9D9"
                                    )
                                )
                            )
                        ),
                    );
                    
                    $nuevoArrayReconocimiento[0]['data'][] = $arrayFilaExcel;
                }


                $arrayFilaExcel = array(
                    array("value" => ""),
                    array("value" => $rep->casnombre, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->fecano, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->fecmes, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->repconcepto, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->sucsoldto, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->sucnombre, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->reptipodocumento, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->repfechadocumento, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->repnumerodocumento, "style" => array("font" => array("sz" => "10"))),
                    array("value" => floatval($rep->repimporte), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                    array("value" => $rep->repmonedalocal, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->repcategoria, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $rep->reptexto, "style" => array("font" => array("sz" => "10"))),
                );

                $nuevoArrayReconocimiento[0]['data'][] = $arrayFilaExcel;
                
            }

            $datosReconocimiento = $nuevoArrayReconocimiento;

            // PLANTILLA PARA DESCARGAR PROMOCIONES LIQUIDADAS
            $prls = prlpromocionesliquidadas::join('sucsucursales as suc', 'suc.sucid', 'prlpromocionesliquidadas.sucid')
                                            ->leftjoin('cascanalessucursales as cas', 'cas.casid', 'suc.casid')
                                            ->join('fecfechas as fec', 'fec.fecid', 'prlpromocionesliquidadas.fecid')
                                            ->whereBetween('fecfecha', [$fechaInicio, $fechaFinal])
                                            // ->where('fec.fecid', $fec->fecid)
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
                                                'prlpromocionesliquidadas.prlid',
                                                'cas.casnombre',
                                                'fec.fecid',
                                                'fec.fecano',
                                                'fec.fecmes',
                                                'prlconcepto',
                                                'prlejecutivo',
                                                'prlgrupo',
                                                'suc.sucsoldto',
                                                'sucnombre',
                                                'prlcompra',
                                                'prlbonificacion',
                                                'prlmecanica',
                                                'prlcategoria',
                                                'prlsku',
                                                'prlproducto',
                                                'prlskubonificado',
                                                'prlproductobonificado',
                                                'prlplancha',
                                                'prlcombo',
                                                'prlreconocerxcombo',
                                                'prlreconocerxplancha',
                                                'prltotal',
                                                'prlliquidacionso',
                                                'prlliquidacioncombo',
                                                'prlliquidacionvalorizado',
                                                'prlliquidaciontotalpagar'
                                            ]);

            foreach ($prls as $posicionPrl => $prl) {
                
                if($posicionPrl == 0){
                    $arrayTitulos = array(
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 150)),

                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 80)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 100)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 50)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                        array("title" => "", "width" => array("wpx" => 150)),
                    );
                    $nuevoArrayPromocionesLiquidadas[0]['columns'] = $arrayTitulos;

                    $arrayFilaExcel = array(
                        array("value" => ""),
                        array(
                            "value" => "GBA", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Año promoción", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Mes promoción", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
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
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Ejecutivo", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Grupo", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
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
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Cliente", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Compra (UND)", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Bonificación (UND)", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),

                        array(
                            "value" => "Mecánica", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
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
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "SKU", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Producto", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Sku a Bonificar", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Producto a Bonificar", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Planchas a rotar o (Sell Out)", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "# Combos", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Reconocer x Combo S/IGV", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Reconocer x PL S/IGV", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "Total Soles S/IGV", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFC6ED59"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "LIQUIDACION:  Sell out planchas", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFF2AD68"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "LIQUIDACION: Combos usados", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFF2AD68"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "LIQUIDACION: Valorizado", 
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFF2AD68"
                                    )
                                )
                            )
                        ),
                        array(
                            "value" => "LIQUIDACION: Total a Pagar",
                            "style" => array(
                                "font" => array(
                                    "sz" => "11", 
                                    "bold" => true
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFF2AD68"
                                    )
                                )
                            )
                        ),
                    );
                    
                    $nuevoArrayPromocionesLiquidadas[0]['data'][] = $arrayFilaExcel;
                }

                
                $arrayFilaExcel = array(
                    array("value" => ""),
                    array("value" => $prl->casnombre, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->fecano, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->fecmes, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlconcepto, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlejecutivo, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlgrupo, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->sucsoldto, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->sucnombre, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlcompra, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlbonificacion, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlmecanica, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlcategoria, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlsku, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlproducto, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlskubonificado, "style" => array("font" => array("sz" => "10"))),
                    array("value" => $prl->prlproductobonificado, "style" => array("font" => array("sz" => "10"))),
                    array("value" => floatval($prl->prlplancha), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                    array("value" => floatval($prl->prlcombo), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                    array("value" => floatval($prl->prlreconocerxcombo), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                    array("value" => floatval($prl->prlreconocerxplancha), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                    array("value" => floatval($prl->prltotal), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                    array("value" => floatval($prl->prlliquidacionso), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                    array("value" => floatval($prl->prlliquidacioncombo), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                    array("value" => floatval($prl->prlliquidacionvalorizado), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00")),
                    array("value" => floatval($prl->prlliquidaciontotalpagar), "style" => array("font" => array("sz" => "10"),"numFmt" => "#,##0.00"))
                );

                $nuevoArrayPromocionesLiquidadas[0]['data'][] = $arrayFilaExcel;
            }

            $datosPromociones = $nuevoArrayPromocionesLiquidadas;



        }catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $respuesta      = false;
        }


        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'datosReconocimiento' => $datosReconocimiento,
            'datosPromociones'    => $datosPromociones,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
            "actualizacion"  => "Actualización 24 de Junio 2021"
        ]);

        return $requestsalida;

    }
}
