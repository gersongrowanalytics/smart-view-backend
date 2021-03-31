<?php

namespace App\Http\Controllers\Sistema\Ventas\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\osiobjetivosssi;
use App\fecfechas;
use App\sucsucursales;
use App\usuusuarios;

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

            $uss = sucsucursales::where(function ($query) use($sucs) {
                                        foreach($sucs as $suc){
                                            if(isset($suc['sucpromociondescarga'])){
                                                if($suc['sucpromociondescarga'] == true){
                                                    $query->orwhere('sucid', $suc['sucid']);
                                                }
                                            }
                                        }
                                    })
                                ->get(['sucsoldto']);

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

                $osis = osiobjetivosssi::where('fecid', $fec->fecid)->where('osivalorizado', '!=', 0)->limit(100)->get();

                foreach($osis as $posicionOsi => $osi){
                    $respuesta = true;

                    if($posicionOsi == 0){
                        $arrayTitulos = array(
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
                    }else{

                        $arrayFilaExcel = array(
                            array("value" => "0"),
                            array("value" => "0"),
                            array("value" => "0"),
                            array("value" => "0"),
                            array("value" => "0"),
                            array("value" => "0"),
                            array("value" => "0"),
                            array("value" => "0"),
                            array("value" => "0"),
                            array("value" => "0"),
                            array("value" => $osi->osivalorizado),
                            array("value" => "0"),
                        );

                        $nuevoArray[0]['data'][] = $arrayFilaExcel;
                    }
                }

                $datos     = $nuevoArray;


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
            'linea'          => $linea,
            'mensajeDetalle' => $mensajeDetalle,
            'mensajedev'     => $mensajedev,
        ]);

        return $requestsalida;


    }
}
