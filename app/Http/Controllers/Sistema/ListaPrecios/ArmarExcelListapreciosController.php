<?php

namespace App\Http\Controllers\Sistema\ListaPrecios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ltplistaprecios;
use App\ussusuariossucursales;

class ArmarExcelListapreciosController extends Controller
{
    public function ObtenerGruposPermitidos(Request $request)
    {
        $usutoken = $request->header('api_token');

        $uss = ussusuariossucursales::join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                    ->join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                    ->where('usu.usutoken', $usutoken)
                                    ->distinct('suc.treid')
                                    ->get([
                                        'treid'
                                    ]);

                                    
    }
    
    public function ArmarExcelListaprecios(Request $request)
    {

        // $re_treid = $request['treid'];
        $re_treid = 26;
        $usutoken = $request->header('api_token');
        $re_anio = $request['anio'];
        $re_mes  = $request['mes'];
        $re_dia  = $request['dia'];

        $ltps = ltplistaprecios::join('fecfechas as fec', 'fec.fecid', 'ltplistaprecios.fecid')
                                ->where('fecano', $re_anio)
                                ->where('fecmes', $re_mes)
                                ->where('fecdia', $re_dia)
                                ->where('treid', $re_treid)
                                ->get();

        $nuevoArray = array(
            array(
                "columns" => [],
                "data"    => []
            )
        );

        $tituloHojaExcel = "";

        if($re_treid == 26){
            $tituloHojaExcel = "A - ESTRATEGICO";
        }else if($re_treid == 15){
            $tituloHojaExcel = "B - TÁCTICO";
        }else if($re_treid == 24){
            $tituloHojaExcel = "C - BROKER";
        }


        $cabeceras = [
            "CAMBIO",
            "Categoría",
            "Subcategoría",
            "Código SAP",
            "EAN",
            "Descripción de producto",
            "Unidad de venta",
            "Precio Lista Sin IGV",
            "% Alza",
            "SD / TPR",
            "Precio Lista con IGV",
            "MF Ruta Mayorista",
            "Reventa Mayorista",
            "Margen Mayorista",
            "Marcaje Mayorista",
            "",
            "MF Ruta Minorista",
            "Reventa Minorista",
            "Margen Minorista",
            "Marcaje Minorista",
            "",
            "MF Ruta Horizontal",
            "Reventa Bodega",
            "Margen Bodega",
            "PVP"
        ];

        foreach($ltps as $posicionLtp => $ltp){

            if($posicionLtp == 0){

                $arrayTitulos = array(
                    array("title" => "", "width" => array("wpx" => 0)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 40)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 40)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),

                );

                $nuevoArray[0]['columns'] = $arrayTitulos;

                $arrayFilaExcel = array( // FILA 2
                    array(),
                    array(
                        "value" => $tituloHojaExcel,
                        "style" => array(
                            "font" => array(
                                "sz" => "20",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            )
                            
                        )
                    ),
                );

                $nuevoArray[0]['data'][] = $arrayFilaExcel;

                $arrayFilaExcel = array( //FILA 3
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(
                        "value" => "MAYORISTA",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                                "vertical" => "right",
                                "horizontal" => "right",
                            )
                            
                        )
                    ),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(
                        "value" => "MINORISTA",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            )
                            
                        )
                    ),

                    array(),
                    array(),
                    array(),
                    array(),
                    array(
                        "value" => "BODEGA",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            )
                            
                        )
                    ),

                );

                $nuevoArray[0]['data'][] = $arrayFilaExcel;

                $arrayFilaExcel = array( // FILA 4
                );
                $nuevoArray[0]['data'][] = $arrayFilaExcel;


                $arrayFilaExcel = array(); // FILA 5

                foreach($cabeceras as $cabecera){

                    $coloLetra = "FFFFFFFF";
                    $colorFondo = "FF44546A";
                    
                    if($cabecera == "MF Ruta Mayorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Reventa Mayorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Margen Mayorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "Marcaje Mayorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "MF Ruta Minorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Reventa Minorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Margen Minorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "Marcaje Minorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "MF Ruta Horizontal"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Reventa Bodega"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Margen Bodega"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "PVP"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }

                    $arrayFilaExcel[] = array(
                        "value" => $cabecera,
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => $coloLetra
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => $colorFondo
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                            
                        )
                    );
                }
                $nuevoArray[0]['data'][] = $arrayFilaExcel;
            }

            $arrayFilaExcel = array(
                array(
                    "value" => "-",
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => $ltp->ltpcategoria,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => $ltp->ltpsubcategoria,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                    )
                ),
                array(
                    "value" => $ltp->ltpcodigosap,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => $ltp->ltpean,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => $ltp->ltpdescripcionproducto,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpunidadventa),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltppreciolistasinigv),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpalza),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpsdtpr),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltppreciolistaconigv),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmfrutamayorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpreventamayorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFE2EFDA"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmargenmayorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmarcajemayorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFD9E1F2"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => "-",
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmfrutaminorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpreventaminorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFE2EFDA"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmargenminorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmarcajeminorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFD9E1F2"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => "-",
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmfrutahorizontal),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpreventabodega),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFE2EFDA"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmargenbodega),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltppvp),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFD9E1F2"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
            );

            $nuevoArray[0]['data'][] = $arrayFilaExcel;

        }

        return response()->json([
            'excel' => $nuevoArray
        ]);

    }


}
