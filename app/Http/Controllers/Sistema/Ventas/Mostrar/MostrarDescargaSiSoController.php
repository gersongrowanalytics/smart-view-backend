<?php

namespace App\Http\Controllers\Sistema\Ventas\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\osiobjetivosssi;
use App\osoobjetivossso;
use App\fecfechas;
use App\sucsucursales;
use App\usuusuarios;
use App\vsiventasssi;
use App\vsoventassso;
use App\rbbrebatesbonus;
use App\rbsrebatesbonussucursales;

class MostrarDescargaSiSoController extends Controller
{
    public function MostrarSucursalesDescargarVentasSiExcel(Request $request)
    {

        $usutoken   = $request['usutoken'];
        $sucs       = $request['sucs'];
        $dia        = "01";
        $mes        = $request['mes'];
        $anio       = $request['ano'];
        
        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['ususoldto']);

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $rebateBonus    = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;


        $columnasExcel = [
            "A",
            "B",
            "C",
            "D",
            "E",
            "F",
            "G",
            "H",
            "I",
            "J",
            "K",
        ];

        $colorPlomo         = "FF595959";
        $colorBlanco        = "FFFFFFFF";
        $colorAzul          = "FF002060";
        $colorVerdeClaro    = "FF66FF33";
        $colorRosa          = "FFFF9999";
        $colorNaranjaClaro  = "FFFFC000";
        $colorPiel          = "FFFFF2CC";
        $colorVerdeLimon    = "FFCCFFCC";        

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

                $osis = osiobjetivosssi::join('sucsucursales as suc', 'suc.sucid', 'osiobjetivosssi.sucid')
                                        ->leftjoin('cascanalessucursales as cas', 'cas.casid', 'suc.casid')
                                        ->leftjoin('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                        ->leftjoin('gsugrupossucursales as gsu', 'gsu.gsuid', 'suc.gsuid')
                                        ->join('proproductos as pro', 'pro.proid', 'osiobjetivosssi.proid')
                                        ->join('catcategorias as cat', 'cat.catid', 'pro.catid')
                                        ->where('osiobjetivosssi.fecid', $fec->fecid)
                                        // ->where('osiobjetivosssi.osivalorizado', '!=', 0)
                                        ->where(function ($query) use($sucs) {
                                            foreach($sucs as $suc){
                                                if(isset($suc['sucpromociondescarga'])){
                                                    if($suc['sucpromociondescarga'] == true){
                                                        $query->orwhere('osiobjetivosssi.sucid', $suc['sucid']);
                                                    }
                                                }
                                            }
                                        })
                                        ->get([
                                            'osivalorizado',
                                            'casnombre',
                                            'zonnombre',
                                            'gsunombre',
                                            'suc.sucid',
                                            'sucsoldto',
                                            'sucnombre',
                                            'pro.proid',
                                            'pronombre',
                                            'catnombre',
                                            'prosku',
                                        ]);

                foreach($osis as $posicionOsi => $osi){
                    $respuesta = true;

                    if($posicionOsi == 0){
                        $arrayTitulos = array(
                            array("title" => "INDICADOR"),
                            array("title" => "AÑO"),
                            array("title" => "MES"),
                            array("title" => "REGIÓN"),
                            array("title" => "ZONA"),
                            array("title" => "GRUPO"),
                            array("title" => "SOLD TO"),
                            array("title" => "CLIENTE"),
                            array("title" => "CATEGORIA"),
                            array("title" => "SKU"),
                            array("title" => "MATERIAL"),
                            array("title" => "CUOTA"),
                            array("title" => "REAL"),
                        );
                        $nuevoArray[0]['columns'] = $arrayTitulos;
                    }

                    $vsi = vsiventasssi::where('fecid', $fec->fecid)
                                    ->where('proid', $osi->proid)
                                    ->where('sucid', $osi->sucid)
                                    ->first();

                    if($vsi){
                        $real = floatval($vsi->vsivalorizado);
                    }else{
                        $real = 0;
                    }
                    
                    $casnombre = $osi->casnombre;
                    $zonnombre = $osi->zonnombre;
                    $gsunombre = $osi->gsunombre;
                    $sucsoldto = $osi->sucsoldto;
                    $sucnombre = $osi->sucnombre;
                    $pronombre = $osi->pronombre;
                    $catnombre = $osi->catnombre;
                    $prosku    = $osi->prosku;

                    if($casnombre == null || $casnombre == " " ){
                        $casnombre = "0";
                    }else if($casnombre == "-"){
                        $casnombre = "0";
                    }

                    if($zonnombre == null || $zonnombre == " " ){
                        $zonnombre = "0";
                    }else if($zonnombre == "-"){
                        $zonnombre = "0";
                    }

                    if($gsunombre == null || $gsunombre == " " ){
                        $gsunombre = "0";
                    }else if($gsunombre == "-"){
                        $gsunombre = "0";
                    }
                    
                    if($sucsoldto == null || $sucsoldto == " " ){
                        $sucsoldto = "0";
                    }else if($sucsoldto == "-"){
                        $sucsoldto = "0";
                    }

                    if($sucnombre == null || $sucnombre == " " ){
                        $sucnombre = "0";
                    }else if($sucnombre == "-"){
                        $sucnombre = "0";
                    }

                    if($pronombre == null || $pronombre == " " ){
                        $pronombre = "0";
                    }else if($pronombre == "-"){
                        $pronombre = "0";
                    }

                    if($catnombre == null || $catnombre == " " ){
                        $catnombre = "0";
                    }else if($catnombre == "-"){
                        $catnombre = "0";
                    }

                    if($prosku == null || $prosku == " " ){
                        $prosku = "0";
                    }else if($prosku == "-"){
                        $prosku = "0";
                    }

                    $arrayFilaExcel = array(
                        array("value" => "Sell In"),
                        array("value" => $anio),
                        array("value" => $mes),
                        array("value" => $casnombre),
                        array("value" => $zonnombre),
                        array("value" => $gsunombre),
                        array("value" => $sucsoldto),
                        array("value" => $sucnombre),
                        array("value" => $catnombre),
                        array("value" => $prosku),
                        array("value" => $pronombre),
                        array("value" => floatval($osi->osivalorizado)),
                        array("value" => $real),
                    );

                    $nuevoArray[0]['data'][] = $arrayFilaExcel;
                }

                $datos     = $nuevoArray;



                // REBATE BONUS ------------------

                // OBTENER EL REBATE BONUS
                $rbsObjetivo = 0;
                $rbsReal     = 0;
                $rbsRebate   = 0;
                $rbbdescripcion = "";

                $rbbs = rbbrebatesbonus::join('fecfechas as fec', 'rbbrebatesbonus.fecid', 'fec.fecid')
                                        ->where('fec.fecano', $anio)
                                        ->where('fec.fecmes', $mes)
                                        ->where('fec.fecdia', $dia)
                                        ->get();

                if(sizeof($rbbs) > 0){

                    $rbsObjetivo = 0;
                    $rbsReal     = 0;
                    $rbsRebate   = 0;
                    $rbbdescripcion   = "";

                    foreach($rbbs as $rbb){
                        $rbbdescripcion   = $rbb->rbbdescripcion;
                        $rbsSumaObjetivosActual = rbsrebatesbonussucursales::where('rbbid', $rbb->rbbid)
                                                                        ->where(function ($query) use($sucs) {
                                                                            foreach($sucs as $suc){
                                                                                if(isset($suc['sucpromociondescarga'])){
                                                                                    if($suc['sucpromociondescarga'] == true){
                                                                                        $query->orwhere('sucid', $suc['sucid']);
                                                                                    }
                                                                                }
                                                                            }
                                                                        })
                                                                        ->sum('rbsobjetivo');

                        $rbsSumaRealActual = rbsrebatesbonussucursales::where('rbbid', $rbb->rbbid)
                                                        ->where(function ($query) use($sucs) {
                                                            foreach($sucs as $suc){
                                                                if(isset($suc['sucpromociondescarga'])){
                                                                    if($suc['sucpromociondescarga'] == true){
                                                                        $query->orwhere('sucid', $suc['sucid']);
                                                                    }
                                                                }
                                                            }
                                                        })
                                                        ->sum('rbsreal');
                        
                        if($rbsSumaObjetivosActual > 0){
                            $rbsCumplimientoActual = ($rbsSumaRealActual * 100 ) / $rbsSumaObjetivosActual;
                        }else{
                            $rbsCumplimientoActual = $rbsSumaRealActual;
                        }

                        if($rbb->rbbcumplimiento <= $rbsCumplimientoActual){
                            $rbsRebateActual = ($rbsSumaObjetivosActual * $rbb->rbbporcentaje) / 100;
                        }else{
                            $rbsRebateActual = 0;
                        }



                        $rbsRebate   = $rbsRebate + $rbsRebateActual;
                        $rbsObjetivo = $rbsObjetivo + $rbsSumaObjetivosActual;
                        $rbsReal     = $rbsReal + $rbsSumaRealActual;

                        if($rbsObjetivo > 0){
                            $rbsCumplimiento = ($rbsReal * 100 ) / $rbsObjetivo;
                        }else{
                            $rbsCumplimiento = $rbsReal;
                        }

                        $rebatesBonus['objetivo']     = $rbsObjetivo;
                        $rebatesBonus['real']         = $rbsReal;
                        $rebatesBonus['cumplimiento'] = $rbsCumplimiento;
                        $rebatesBonus['rebate']       = $rbsRebate;
                        $rebatesBonus['descripcion']  = $rbb->rbbdescripcion;
                        
                    }
                }

                $rebateBonus = array(
                    array(
                        "columns" => [
                            array(
                                "title" => "",
                                "width" => array(
                                    "wch" => 40
                                ),
                            ),
                            array(
                                "title" => "",
                                "width" => array(
                                    "wpx" => 100
                                ),
                            ),
                            array(
                                "title" => "",
                                "width" => array(
                                    "wpx" => 100
                                ),
                            ),
                            array(
                                "title" => "",
                                "width" => array(
                                    "wpx" => 150
                                ),
                            ),
                            array(
                                "title" => "",
                                "width" => array(
                                    "wpx" => 150
                                ),
                            ),
                        ],
                        "data" => [
                            [
                                array(
                                    "value" => "REBATE BONUS",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "18",
                                            "bold" => true
                                        ),
                                    ),
                                ),
                            ],
                            [
                                array(
                                    "value" => "El cliente podrá acceder a un rebate denominado “Bonus” de 0.5 %, siempre que cumpla con lo siguiente:",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "14",
                                            "bold" => false
                                        ),
                                    ),
                                ),
                            ],
                            [
                                array(
                                    "value" => $rbbdescripcion,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "14",
                                            "bold" => false
                                        ),
                                    ),
                                ),
                            ],
                            [],
                            [
                                array(
                                    "value" => "Categoria",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "18",
                                            "bold" => true
                                        ),
                                    ),
                                ),
                                array(
                                    "value" => "Objetivo",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "18",
                                            "bold" => true
                                        ),
                                    ),
                                ),
                                array(
                                    "value" => "Real",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "18",
                                            "bold" => true
                                        ),
                                    ),
                                ),
                                array(
                                    "value" => "Cumplimiento",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "18",
                                            "bold" => true
                                        ),
                                    ),
                                ),
                                array(
                                    "value" => "Rebate Bonus",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "18",
                                            "bold" => true
                                        ),
                                    ),
                                ),
                            ],
                            [
                                array(
                                    "value" => "Infant Care",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "14",
                                            "bold" => false
                                        ),
                                    ),
                                ),
                                array(
                                    "value" => $rbsObjetivo,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "14",
                                            "bold" => false
                                        ),
                                    ),
                                ),
                                array(
                                    "value" => $rbsReal,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "14",
                                            "bold" => false
                                        ),
                                    ),
                                ),
                                array(
                                    "value" => ($rbsReal * 100)/$rbsObjetivo."%",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "14",
                                            "bold" => false
                                        ),
                                    ),
                                ),
                                array(
                                    "value" => $rbsRebate,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "14",
                                            "bold" => false
                                        ),
                                    ),
                                ),
                            ],
                            [],
                            [
                                array(
                                    "value" => "Sold To",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "18",
                                            "bold" => true
                                        ),
                                    ),
                                ),
                                array(
                                    "value" => "Cliente",
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "18",
                                            "bold" => true
                                        ),
                                    ),
                                ),
                            ]
                        ]
                    ),
                );

                foreach ($usss as $uss) {
                    $sucursal = [
                        array(
                            "value" => $uss->sucsoldto,
                            "style" => array(
                                "font" => array(
                                    "sz" => "14",
                                    "bold" => false
                                ),
                            ),
                        ),
                        array(
                            "value" => $uss->sucnombre,
                            "style" => array(
                                "font" => array(
                                    "sz" => "14",
                                    "bold" => false
                                ),
                            ),
                        ),
                    ];

                    $rebateBonus[0]["data"][] = $sucursal;
                }



                // $car = carcargasarchivos::where('fecid', $fec->fecid)
                //                     ->where('tcaid', 1)
                //                     ->first(['carid', 'carnombrearchivo']);

                // if($car){
                //     $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/'.$car->carnombrearchivo;
                //     // $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/Promociones 2021 Enero.xlsx';

                //     $objPHPExcel    = IOFactory::load($fichero_subido);
                //     $objPHPExcel->setActiveSheetIndex(0);
                //     $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                //     $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                //     for ($i=1; $i <= $numRows ; $i++) {

                //         if($i == 1){

                //             $arrayTitulos = array(
                //                 array(
                //                     "title" => ""
                //                 )
                //             );

                //             $contadorTitulos = 0;
                //             foreach($columnasExcel as $abc) {  
                //                 $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                                
                //                 $arrayTitulos[$contadorTitulos]['title'] = $columnasFilas;
                //                 $arrayTitulos[$contadorTitulos]['style']['fill']['patternType'] = 'solid';
                //                 if($abc == "A" || $abc == "B" || $abc == "J" || $abc == "K" || $abc == "M" || $abc == "N" || $abc == "Q" || $abc == "T" || $abc == "U" || $abc == "V" || $abc == "Z" || $abc == "AD" || $abc == "AG" || $abc == "AI" || $abc == "AK" || $abc == "AM" || $abc == "AN" || $abc == "AQ" || $abc == "AR" || $abc == "AS" || $abc == "AT"){
                //                     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorPlomo;
                //                     $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                //                 }else if($abc == "C" || $abc == "D" || $abc == "E" || $abc == "F" || $abc == "G" || $abc == "H" || $abc == "I" || $abc == "L" || $abc == "O" || $abc == "P" || $abc == "Y" || $abc == "AC" || $abc == "AH" || $abc == "AL" || $abc == "AU"){
                //                     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorAzul;
                //                     $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                //                 }else if($abc == "R" || $abc == "S" || $abc == "AO" || $abc == "AP"){
                //                     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorNaranjaClaro;
                //                     $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                //                 }else if($abc == "W" || $abc == "X" || $abc == "AA" || $abc == "AB" || $abc == "AE" || $abc == "AF" || $abc == "R" ){
                //                     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorVerdeClaro;
                //                 }else if($abc == "AJ"){
                //                     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorRosa;
                //                     $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                //                 }
                //                 $contadorTitulos = $contadorTitulos+1;
                //             }

                //             $nuevoArray[0]['columns'] = $arrayTitulos;

                //         }else{
                //             $nombreTituloSoldTo = $objPHPExcel->getActiveSheet()->getCell('O2')->getCalculatedValue();
                //             $soldto = "";
                //             if($nombreTituloSoldTo == "SOLD TO"){
                //                 $soldto = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();    
                //             }else{
                //                 $soldto = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                //             }

                //             $soldto = trim($soldto);


                //             $pertenecedata = false;
                //             foreach($uss as $u){

                //                 $pos = strpos($soldto, $u->sucsoldto);
                //                 // if($u->sucsoldto == $soldto ){
                //                 if($pos === false ){
                //                     $pertenecedata = false;
                //                 }else{
                //                     $pertenecedata = true;
                //                     break;                                    
                //                 }
                //             }

                //             if($pertenecedata == true){
                //                 $arrayFilaExcel = array(
                //                     array(
                //                         "value" => ""
                //                     )
                //                 );
                //                 $contadorColumna = 0;

                //                 foreach($columnasExcel as $abc) {
                //                     $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                                    
                //                     if($columnasFilas == null || $columnasFilas == " " ){
                //                         $columnasFilas = "";
                //                     }else if($columnasFilas == "-"){
                //                         $columnasFilas = "0";
                //                     }

                //                     // if($abc == "AD"){
                //                     //     $columnasFilas = strval("a$columnasFilas");
                //                     // }
                //                     if($abc == "AR"){
                //                         // $columnasFilas = floatval($columnasFilas);

                //                         // if(is_numeric ( $columnasFilas )){
                //                         //     $columnasFilas = number_format($columnasFilas, 2);
                //                         // }

                //                         // $columnasFilas = floatval($columnasFilas);
                //                     }

                //                     if($abc != "A" && $abc != "P" && $abc != "Z" && $abc != "AD" && $abc != "AR" && $abc != "AN" && $abc != "AO" && $abc != "AP" && $abc != "AQ"){
                //                         if(is_numeric($columnasFilas)){
                //                             $columnasFilas = number_format($columnasFilas, 2);
                //                             $columnasFilas = floatval($columnasFilas);
                //                         }
                //                     }

                                    

                //                     $arrayFilaExcel[$contadorColumna]['value'] = $columnasFilas;

                //                     if($abc == "L" || $abc == "O" || $abc == "P" || $abc == "R" || $abc == "S" || $abc == "Y" || $abc == "AC" || $abc == "AL" || $abc == "AU"){
                //                         $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                //                         $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorPiel;
                //                     }else if($abc == "W" || $abc == "X" ){
                //                         $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                //                         $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorVerdeLimon;
                //                     }else if($abc == "AH" || $abc == "AR"){
                //                         $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                //                         $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorNaranjaClaro;
                //                     }

                //                     $contadorColumna = $contadorColumna+1;
                //                 }

                //                 $nuevoArray[0]['data'][] = $arrayFilaExcel;
                //             }
                //         }
                //     }

                //     $respuesta = true;
                //     $datos     = $nuevoArray;
                //     $archivo   = $car->carnombrearchivo;
                // }else{
                //     $respuesta = false;
                //     $mensaje = "Lo sentimos, no pudimos encontrar un registro de excel subido a este mes seleccionado";
                //     $mensajeDetalle = "Vuelve a seleccionar la fecha o comunicate con soporte";
                // }         
            }else{
                $respuesta = false;
                $mensaje = "Lo sentimos, no pudimos encontrar la fecha seleccionada";
                $mensajeDetalle = "Vuelve a seleccionar la fecha o comunicate con soporte";
            }


        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'rebateBonus'    => $rebateBonus,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
        ]);

        return $requestsalida;


    }

    public function MostrarSucursalesDescargarVentasSoExcel(Request $request)
    {

        $usutoken   = $request['usutoken'];
        $sucs       = $request['sucs'];
        $dia        = "01";
        $mes        = $request['mes'];
        $anio       = $request['ano'];
        
        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['ususoldto']);

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;


        $columnasExcel = [
            "A",
            "B",
            "C",
            "D",
            "E",
            "F",
            "G",
            "H",
            "I",
            "J",
            "K",
        ];

        $colorPlomo         = "FF595959";
        $colorBlanco        = "FFFFFFFF";
        $colorAzul          = "FF002060";
        $colorVerdeClaro    = "FF66FF33";
        $colorRosa          = "FFFF9999";
        $colorNaranjaClaro  = "FFFFC000";
        $colorPiel          = "FFFFF2CC";
        $colorVerdeLimon    = "FFCCFFCC";        

        try{

            // $uss = sucsucursales::where(function ($query) use($sucs) {
            //                             foreach($sucs as $suc){
            //                                 if(isset($suc['sucpromociondescarga'])){
            //                                     if($suc['sucpromociondescarga'] == true){
            //                                         $query->orwhere('sucid', $suc['sucid']);
            //                                     }
            //                                 }
            //                             }
            //                         })
            //                     ->get(['sucsoldto']);

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

                $osos = osoobjetivossso::join('sucsucursales as suc', 'suc.sucid', 'osoobjetivossso.sucid')
                                        ->leftjoin('cascanalessucursales as cas', 'cas.casid', 'suc.casid')
                                        ->leftjoin('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                        ->leftjoin('gsugrupossucursales as gsu', 'gsu.gsuid', 'suc.gsuid')
                                        ->join('proproductos as pro', 'pro.proid', 'osoobjetivossso.proid')
                                        ->join('catcategorias as cat', 'cat.catid', 'pro.catid')
                                        ->where('osoobjetivossso.fecid', $fec->fecid)
                                        // ->where('osoobjetivossso.osovalorizado', '!=', 0)
                                        ->where(function ($query) use($sucs) {
                                            foreach($sucs as $suc){
                                                if(isset($suc['sucpromociondescarga'])){
                                                    if($suc['sucpromociondescarga'] == true){
                                                        $query->orwhere('osoobjetivossso.sucid', $suc['sucid']);
                                                    }
                                                }
                                            }
                                        })
                                        ->get([
                                            'osovalorizado',
                                            'casnombre',
                                            'zonnombre',
                                            'gsunombre',
                                            'suc.sucid',
                                            'sucsoldto',
                                            'sucnombre',
                                            'pro.proid',
                                            'pronombre',
                                            'catnombre',
                                            'prosku',
                                        ]);

                foreach($osos as $posicionOso => $oso){
                    $respuesta = true;

                    if($posicionOso == 0){
                        $arrayTitulos = array(
                            array("title" => "INDICADOR"),
                            array("title" => "AÑO"),
                            array("title" => "MES"),
                            array("title" => "REGIÓN"),
                            array("title" => "ZONA"),
                            array("title" => "GRUPO"),
                            array("title" => "SOLD TO"),
                            array("title" => "CLIENTE"),
                            array("title" => "CATEGORIA"),
                            array("title" => "SKU"),
                            array("title" => "MATERIAL"),
                            array("title" => "CUOTA"),
                            array("title" => "REAL"),
                        );
                        $nuevoArray[0]['columns'] = $arrayTitulos;
                    }

                    $vso = vsoventassso::where('fecid', $fec->fecid)
                                    ->where('proid', $oso->proid)
                                    ->where('sucid', $oso->sucid)
                                    ->first();

                    if($vso){
                        $real = floatval($vso->vsovalorizado);
                    }else{
                        $real = 0;
                    }
                    
                    $casnombre = $oso->casnombre;
                    $zonnombre = $oso->zonnombre;
                    $gsunombre = $oso->gsunombre;
                    $sucsoldto = $oso->sucsoldto;
                    $sucnombre = $oso->sucnombre;
                    $pronombre = $oso->pronombre;
                    $catnombre = $oso->catnombre;
                    $prosku    = $oso->prosku;

                    if($casnombre == null || $casnombre == " " ){
                        $casnombre = "0";
                    }else if($casnombre == "-"){
                        $casnombre = "0";
                    }

                    if($zonnombre == null || $zonnombre == " " ){
                        $zonnombre = "0";
                    }else if($zonnombre == "-"){
                        $zonnombre = "0";
                    }

                    if($gsunombre == null || $gsunombre == " " ){
                        $gsunombre = "0";
                    }else if($gsunombre == "-"){
                        $gsunombre = "0";
                    }
                    
                    if($sucsoldto == null || $sucsoldto == " " ){
                        $sucsoldto = "0";
                    }else if($sucsoldto == "-"){
                        $sucsoldto = "0";
                    }

                    if($sucnombre == null || $sucnombre == " " ){
                        $sucnombre = "0";
                    }else if($sucnombre == "-"){
                        $sucnombre = "0";
                    }

                    if($pronombre == null || $pronombre == " " ){
                        $pronombre = "0";
                    }else if($pronombre == "-"){
                        $pronombre = "0";
                    }

                    if($catnombre == null || $catnombre == " " ){
                        $catnombre = "0";
                    }else if($catnombre == "-"){
                        $catnombre = "0";
                    }

                    if($prosku == null || $prosku == " " ){
                        $prosku = "0";
                    }else if($prosku == "-"){
                        $prosku = "0";
                    }

                    $arrayFilaExcel = array(
                        array("value" => "Sell Out"),
                        array("value" => $anio),
                        array("value" => $mes),
                        array("value" => $casnombre),
                        array("value" => $zonnombre),
                        array("value" => $gsunombre),
                        array("value" => $sucsoldto),
                        array("value" => $sucnombre),
                        array("value" => $catnombre),
                        array("value" => $prosku),
                        array("value" => $pronombre),
                        array("value" => floatval($oso->osovalorizado)),
                        array("value" => $real),
                    );

                    $nuevoArray[0]['data'][] = $arrayFilaExcel;
                }

                $datos     = $nuevoArray;
     
            }else{
                $respuesta = false;
                $mensaje = "Lo sentimos, no pudimos encontrar la fecha seleccionada";
                $mensajeDetalle = "Vuelve a seleccionar la fecha o comunicate con soporte";
            }


        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => $datos,
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
        ]);

        return $requestsalida;


    }
}
