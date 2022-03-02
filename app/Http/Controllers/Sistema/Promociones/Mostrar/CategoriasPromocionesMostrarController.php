<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\csccanalessucursalescategorias;
use App\scasucursalescategorias;
use App\prppromocionesproductos;
use App\prbpromocionesbonificaciones;
use App\cspcanalessucursalespromociones;
use App\usuusuarios;
use App\ussusuariossucursales;
use App\fecfechas;
use App\carcargasarchivos;
use App\sucsucursales;

class CategoriasPromocionesMostrarController extends Controller
{
    public function mostrarCategoriasPromociones(Request $request)
    {

        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
        $dia        = $request['dia'];
        $mes        = $request['mes'];
        $anio       = $request['ano'];
        
        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['ususoldto']);

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $arrCodigosPrincipales = [];

        try{

            $nuevoArray = array(
                array(
                    "ANIO"                  => "",
                    "MES"                   => "",
                    "CATEGORIA"             => "",
                    "CANAL"                 => "",
                    "MECANICA"              => "",
                    "SKU"                   => "",
                    "PRODUCTO"              => "",
                    "SKU BONIFICADO"        => "",
                    "PRODUCTO BONIFICADO"   => "",
                    "PLANCHAS ROTAR"        => "",
                    "#COMBOS"               => "",
                    "RECONOCER X COMBO"     => "",
                    "RECONOCER X PLANCHA"   => "",
                    "TOTAL SOLES"           => "",
                    "ACCION"                => ""
                )
            );

            $scasucursalescategorias = scasucursalescategorias::join('fecfechas as fec', 'scasucursalescategorias.fecid', 'fec.fecid')
                                                                ->join('catcategorias as cat', 'cat.catid', 'scasucursalescategorias.catid')
                                                                ->where('scasucursalescategorias.sucid', $sucid)
                                                                ->where('fec.fecano', $anio)
                                                                ->where('fec.fecmes', $mes)
                                                                ->where('fec.fecdia', $dia)
                                                                ->where('scasucursalescategorias.tsuid', null)
                                                                ->get([
                                                                    'scasucursalescategorias.scaid',
                                                                    'cat.catid',
                                                                    'cat.catnombre',
                                                                    'cat.catimagenfondo',
                                                                    'cat.catimagenfondoseleccionado',
                                                                    'cat.catimagenfondoopaco',
                                                                    'cat.caticono',
                                                                    'cat.caticonohover',
                                                                    'cat.catcolorhover',
                                                                    'cat.catcolor',
                                                                    'cat.caticonoseleccionado',
                                                                    'fec.fecfecha'
                                                                ]);

            if(sizeof($scasucursalescategorias) > 0){
                $contador = 0;
                foreach($scasucursalescategorias as $posicionSca => $sca){
                    $nuevoArray[$contador]['ANIO']      = $anio;
                    $nuevoArray[$contador]['MES']       = $mes;
                    $nuevoArray[$contador]['CATEGORIA'] = $sca->catnombre;


                    $csccanalessucursalescategorias = csccanalessucursalescategorias::join('cancanales as can', 'can.canid', 'csccanalessucursalescategorias.canid')
                                                                        ->where('csccanalessucursalescategorias.scaid', $sca->scaid)
                                                                        ->get([
                                                                            'csccanalessucursalescategorias.cscid',
                                                                            'can.canid',
                                                                            'can.cannombre'
                                                                        ]);
                    //**************************************** */
                    if(sizeof($csccanalessucursalescategorias) > 0){
                        $primeraCsc = false;
                        foreach($csccanalessucursalescategorias as $posicion => $csccanalesucursalcategoria){
                            if($primeraCsc == false){
                                $nuevoArray[$contador]['CANAL'] = $csccanalesucursalcategoria->cannombre;
                                $primeraCsc = true;
                            }else{
                                $contador = $contador+1;
                                $nuevoArray[$contador]['ANIO']      = $anio;
                                $nuevoArray[$contador]['MES']       = $mes;
                                $nuevoArray[$contador]['CATEGORIA'] = $sca->catnombre;
                                $nuevoArray[$contador]['CANAL']     = $csccanalesucursalcategoria->cannombre;
                            }

                            $cspcanalessucursalespromociones = cspcanalessucursalespromociones::join('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                                                                ->where('cscid', $csccanalesucursalcategoria->cscid)
                                                                                                ->get([
                                                                                                    'cspcanalessucursalespromociones.cspid',
                                                                                                    'prm.prmid',
                                                                                                    'cspcanalessucursalespromociones.cspvalorizado',
                                                                                                    'cspcanalessucursalespromociones.cspplanchas',
                                                                                                    'cspcanalessucursalespromociones.cspcompletado',
                                                                                                    'cspcanalessucursalespromociones.cspcantidadcombo',
                                                                                                    'prm.prmmecanica',
                                                                                                    'cspcanalessucursalespromociones.cspcantidadplancha',
                                                                                                    'cspcanalessucursalespromociones.csptotalcombo',
                                                                                                    'cspcanalessucursalespromociones.csptotalplancha',
                                                                                                    'cspcanalessucursalespromociones.csptotal',
                                                                                                    'prm.prmaccion'
                                                                                                ]);
                            $primeraCsp = false;
                            if(sizeof($cspcanalessucursalespromociones) > 0){
                                foreach($cspcanalessucursalespromociones as $posicionPromociones => $cspcanalesucursalpromocion){
                                    if($primeraCsp == false){
                                        $nuevoArray[$contador]['MECANICA']            = $cspcanalesucursalpromocion->prmmecanica;
                                        $nuevoArray[$contador]['PLANCHAS ROTAR']      = $cspcanalesucursalpromocion->cspcantidadplancha;
                                        $nuevoArray[$contador]['#COMBOS']             = $cspcanalesucursalpromocion->cspcantidadcombo;
                                        $nuevoArray[$contador]['RECONOCER X COMBO']   = $cspcanalesucursalpromocion->csptotalcombo;
                                        $nuevoArray[$contador]['RECONOCER X PLANCHA'] = $cspcanalesucursalpromocion->csptotalplancha;
                                        $nuevoArray[$contador]['TOTAL SOLES']         = $cspcanalesucursalpromocion->csptotal;
                                        $nuevoArray[$contador]['ACCION']              = $cspcanalesucursalpromocion->prmaccion;
                                        $primeraCsp = true;
                                    }else{
                                        $contador = $contador+1;
                                        $nuevoArray[$contador]['ANIO']                = $anio;
                                        $nuevoArray[$contador]['MES']                 = $mes;
                                        $nuevoArray[$contador]['CATEGORIA']           = $sca->catnombre;
                                        $nuevoArray[$contador]['CANAL']               = $csccanalesucursalcategoria->cannombre;
                                        $nuevoArray[$contador]['MECANICA']            = $cspcanalesucursalpromocion->prmmecanica;
                                        $nuevoArray[$contador]['PLANCHAS ROTAR']      = $cspcanalesucursalpromocion->cspcantidadplancha;
                                        $nuevoArray[$contador]['#COMBOS']             = $cspcanalesucursalpromocion->cspcantidadcombo;
                                        $nuevoArray[$contador]['RECONOCER X COMBO']   = $cspcanalesucursalpromocion->csptotalcombo;
                                        $nuevoArray[$contador]['RECONOCER X PLANCHA'] = $cspcanalesucursalpromocion->csptotalplancha;
                                        $nuevoArray[$contador]['TOTAL SOLES']         = $cspcanalesucursalpromocion->csptotal;
                                        $nuevoArray[$contador]['ACCION']              = $cspcanalesucursalpromocion->prmaccion;
                                    }


                                    // $codigoPrincipal = $usuusuario->ususoldto.;

                                    $prppromocionesproductos = prppromocionesproductos::join('proproductos as pro', 'pro.proid', 'prppromocionesproductos.proid')
                                                                                        ->where('prppromocionesproductos.prmid', $cspcanalesucursalpromocion->prmid )
                                                                                        // ->where(function ($query) use($arrCodigosPrincipales) {

                                                                                        //     for($i = 0; $i < sizeof($arrCodigosPrincipales); $i++){
                                                                                        //         $query->where('prpcodigoprincipal', '!=' , $arrCodigosPrincipales[$i]);
                                                                                        //     }

                                                                                        // })
                                                                                        ->where('prpcodigoprincipal', 'LIKE', $usuusuario->ususoldto.'%')
                                                                                        ->get([
                                                                                            'pro.proid',
                                                                                            'pro.prosku',
                                                                                            'pro.pronombre',
                                                                                            'pro.proimagen',
                                                                                            'prpproductoppt',
                                                                                            'prpcomprappt',
                                                                                            'prpcodigoprincipal'
                                                                                        ]);
                                    $primeraPrp = false;
                                    if(sizeof($prppromocionesproductos) > 0){
                                        foreach($prppromocionesproductos as $posicionProductos => $prp ){
                                            $ignorar = false;
                                            for($i = 0; $i < sizeof($arrCodigosPrincipales); $i++){
                                                if($arrCodigosPrincipales[$i] == $prp->prpcodigoprincipal){
                                                    $ignorar = true;
                                                    break;
                                                }
                                            }
                                            $arrCodigosPrincipales[] = $prp->prpcodigoprincipal;
                                            if($primeraPrp == false){
                                                $nuevoArray[$contador]['SKU']      = $prp->prosku;
                                                $nuevoArray[$contador]['PRODUCTO'] = $prp->prpproductoppt;
                                                $primeraPrp = true;
                                            }else{
                                                $contador = $contador+1;
                                                $nuevoArray[$contador]['ANIO']                = $anio;
                                                $nuevoArray[$contador]['MES']                 = $mes;
                                                $nuevoArray[$contador]['CATEGORIA']           = $sca->catnombre;
                                                $nuevoArray[$contador]['CANAL']               = $csccanalesucursalcategoria->cannombre;
                                                $nuevoArray[$contador]['MECANICA']            = $cspcanalesucursalpromocion->prmmecanica;
                                                $nuevoArray[$contador]['PLANCHAS ROTAR']      = $cspcanalesucursalpromocion->cspcantidadplancha;
                                                $nuevoArray[$contador]['#COMBOS']             = $cspcanalesucursalpromocion->cspcantidadcombo;
                                                $nuevoArray[$contador]['RECONOCER X COMBO']   = $cspcanalesucursalpromocion->csptotalcombo;
                                                $nuevoArray[$contador]['RECONOCER X PLANCHA'] = $cspcanalesucursalpromocion->csptotalplancha;
                                                $nuevoArray[$contador]['TOTAL SOLES']         = $cspcanalesucursalpromocion->csptotal;
                                                $nuevoArray[$contador]['ACCION']              = $cspcanalesucursalpromocion->prmaccion;
                                                $nuevoArray[$contador]['SKU']                 = $prp->prosku;
                                                $nuevoArray[$contador]['PRODUCTO']            = $prp->prpproductoppt;
                                            }



                                            $prbpromocionesbonificaciones = prbpromocionesbonificaciones::join('proproductos as pro', 'pro.proid', 'prbpromocionesbonificaciones.proid')
                                                                                                ->where('prbpromocionesbonificaciones.prmid', $cspcanalesucursalpromocion->prmid )
                                                                                                ->where('prbcodigoprincipal', $prp->prpcodigoprincipal )
                                                                                                ->first([
                                                                                                    'pro.proid',
                                                                                                    'pro.prosku',
                                                                                                    'pro.pronombre',
                                                                                                    'pro.proimagen',
                                                                                                    'prbproductoppt',
                                                                                                    'prbcomprappt',
                                                                                                    'prbcodigoprincipal'
                                                                                                ]);
                                    
                                            if($prbpromocionesbonificaciones){
                                                $nuevoArray[$contador]['SKU BONIFICADO']      = $prbpromocionesbonificaciones->prosku;
                                                $nuevoArray[$contador]['PRODUCTO BONIFICADO'] = $prbpromocionesbonificaciones->prbproductoppt;
                                            }else{
                                                $nuevoArray[$contador]['SKU BONIFICADO']      = '';
                                                $nuevoArray[$contador]['PRODUCTO BONIFICADO'] = '';
                                            }
                                            
                                        }
                                    }else{
                                        $nuevoArray[$contador]['SKU']      = '';
                                        $nuevoArray[$contador]['PRODUCTO'] = '';
                                    }
                                }
                            }else{
                                $nuevoArray[$contador]['MECANICA']            = '';
                                $nuevoArray[$contador]['PLANCHAS ROTAR']      = '';
                                $nuevoArray[$contador]['#COMBOS']             = '';
                                $nuevoArray[$contador]['RECONOCER X COMBO']   = '';
                                $nuevoArray[$contador]['RECONOCER X PLANCHA'] = '';
                                $nuevoArray[$contador]['TOTAL SOLES']         = '';
                                $nuevoArray[$contador]['ACCION']              = '';
                            }
                        }
                    }else{
                        $nuevoArray[$contador]['CANAL'] = '';
                    }

                    
                    $contador = $contador+1;
                    //**************************************** */
                }
                $respuesta = true;
                $datos     = $nuevoArray;
            }else{
                $respuesta      = false;
                $linea          = __LINE__;
                $mensaje        = 'Lo sentimos, no se contramos categorias registradas a este filtro.';
                $mensajeDetalle = sizeof($scasucursalescategorias).' registros encontrados.';
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
            'mensajedev'     => $mensajedev
        ]);
        
        return $requestsalida;
    }

    public function mostrarCategoriasPromocionesExcel(Request $request)
    {

        $usutoken   = $request['usutoken'];
        $sucid      = $request['sucid'];
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
            "L",
            "M",
            "N",
            "O",
            "P",
            "Q",
            "R",
            "S",
            "T",
            "U",
            "V",
            "W",
            // "X",
            // "Y",
            "Z",
            "AA",
            // "AB",
            // "AC",
            "AD",
            "AE",
            // "AF",
            // "AG",
            "AH",
            "AI",
            "AJ",
            "AK",
            "AL",
            "AM",
            "AN",
            "AO",
            "AP",
            "AQ",
            "AR",
            // "AS",
            // "AT",
            // "AU",
            // "AV"
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

            $uss = ussusuariossucursales::join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid' )
                                        ->where('ussusuariossucursales.sucid', $sucid)
                                        ->get([
                                            'ussusuariossucursales.ussid',
                                            'ussusuariossucursales.usuid',
                                            'ussusuariossucursales.sucid',
                                            'usu.ususoldto'
                                        ]);

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
                $car = carcargasarchivos::where('fecid', $fec->fecid)
                                    ->where('tcaid', 1)
                                    ->first(['carid', 'carnombrearchivo']);

                if($car){
                    $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/'.$car->carnombrearchivo;
                    // $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/Promociones 2021 Enero.xlsx';

                    $objPHPExcel    = IOFactory::load($fichero_subido);
                    $objPHPExcel->setActiveSheetIndex(0);
                    $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                    $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                    for ($i=1; $i <= $numRows ; $i++) {

                        if($i == 1){

                            $arrayTitulos = array(
                                array(
                                    "title" => ""
                                )
                            );

                            $contadorTitulos = 0;
                            foreach($columnasExcel as $abc) {  
                                $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                                
                                $arrayTitulos[$contadorTitulos]['title'] = $columnasFilas;
                                $arrayTitulos[$contadorTitulos]['style']['fill']['patternType'] = 'solid';
                                if($abc == "A" || $abc == "B" || $abc == "J" || $abc == "K" || $abc == "M" || $abc == "N" || $abc == "Q" || $abc == "T" || $abc == "U" || $abc == "V" || $abc == "Z" || $abc == "AD" || $abc == "AG" || $abc == "AI" || $abc == "AK" || $abc == "AM" || $abc == "AN" || $abc == "AQ" || $abc == "AR" || $abc == "AS" || $abc == "AT"){
                                    $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorPlomo;
                                    $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                                }else if($abc == "C" || $abc == "D" || $abc == "E" || $abc == "F" || $abc == "G" || $abc == "H" || $abc == "I" || $abc == "L" || $abc == "O" || $abc == "P" || $abc == "Y" || $abc == "AC" || $abc == "AH" || $abc == "AL" || $abc == "AU"){
                                    $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorAzul;
                                    $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                                }else if($abc == "R" || $abc == "S" || $abc == "AO" || $abc == "AP"){
                                    $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorNaranjaClaro;
                                    $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                                }else if($abc == "W" || $abc == "X" || $abc == "AA" || $abc == "AB" || $abc == "AE" || $abc == "AF" || $abc == "R" ){
                                    $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorVerdeClaro;
                                }else if($abc == "AJ"){
                                    $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorRosa;
                                    $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                                }
                                $contadorTitulos = $contadorTitulos+1;
                            }

                            $nuevoArray[0]['columns'] = $arrayTitulos;

                        }else{
                            $nombreTituloSoldTo = $objPHPExcel->getActiveSheet()->getCell('O2')->getCalculatedValue();
                            $soldto = "";
                            if($nombreTituloSoldTo == "SOLD TO"){
                                $soldto = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();    
                            }else{
                                $soldto = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                            }

                            $pertenecedata = false;
                            foreach($uss as $u){
                                if($u->ususoldto == $soldto ){
                                    $pertenecedata = true;
                                    break;
                                }else{
                                    $pertenecedata = false;
                                }
                            }

                            if($pertenecedata == true){
                                $arrayFilaExcel = array(
                                    array(
                                        "value" => ""
                                    )
                                );
                                $contadorColumna = 0;

                                foreach($columnasExcel as $abc) {
                                    $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                                    
                                    if($columnasFilas == null || $columnasFilas == " " ){
                                        $columnasFilas = "";
                                    }else if($columnasFilas == "-"){
                                        $columnasFilas = "0";
                                    }

                                    if($abc != "A"){
                                        if(is_numeric($columnasFilas)){
                                            $columnasFilas = number_format($columnasFilas, 2);
                                        }
                                    }

                                    $arrayFilaExcel[$contadorColumna]['value'] = $columnasFilas;

                                    if($abc == "L" || $abc == "O" || $abc == "P" || $abc == "R" || $abc == "S" || $abc == "Y" || $abc == "AC" || $abc == "AL" || $abc == "AU"){
                                        $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                                        $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorPiel;
                                    }else if($abc == "W" || $abc == "X" ){
                                        $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                                        $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorVerdeLimon;
                                    }else if($abc == "AH" || $abc == "AR"){
                                        $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                                        $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorNaranjaClaro;
                                    }

                                    $contadorColumna = $contadorColumna+1;
                                }

                                $nuevoArray[0]['data'][] = $arrayFilaExcel;
                            }
                        }
                    }

                    $respuesta = true;
                    $datos     = $nuevoArray;
                    $archivo   = $car->carnombrearchivo;
                }else{
                    $respuesta = false;
                    $mensaje = "Lo sentimos, no pudimos encontrar un registro de excel subido a este mes seleccionado";
                    $mensajeDetalle = "Vuelve a seleccionar la fecha o comunicate con soporte";
                }         
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
            'archivo'        => $archivo
        ]);

        return $requestsalida;


    }

    public function MostrarSucursalesDescargarPromocionesExcelbk(Request $request)
    {

        $usutoken    = $request['usutoken'];
        $sucs        = $request['sucs'];
        $re_columnas = $request['columnas'];
        $dia         = "01";
        $mes         = $request['mes'];
        $anio        = $request['ano'];
        
        $usuusuario  = usuusuarios::where('usutoken', $usutoken)->first(['ususoldto']);

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
            "L",
            "M",
            "N",
            "O",
            "P",
            "Q",
            "R",
            "S",
            "T",
            "U",
            "V",
            "W",
            // "X",
            // "Y",
            "Z",
            "AA",
            // "AB",
            // "AC",
            "AD",
            "AE",
            // "AF",
            // "AG",
            "AH",
            "AI",
            "AJ",
            "AK",
            "AL",
            "AM",
            "AN",
            "AO",
            "AP",
            "AQ",
            "AR",
            // "AS",
            // "AT",
            // "AU",
            // "AV"
        ];

        $columnasExcel = [
            "A",
            "B",
            "region",
            "zona",
            "grupo",
            "clientehml",
            "P",
            "Q",
            "AK",
            "W",
            "AA",
            "V",
            "AN",
            "AQ",
            "AO",
            "AP"
        ];

        $colorPlomo         = "FF595959";
        $colorBlanco        = "FFFFFFFF";
        $colorAzul          = "FF002060";
        $colorVerdeClaro    = "FF66FF33";
        $colorRosa          = "FFFF9999";
        $colorNaranjaClaro  = "FFFFC000";
        $colorPiel          = "FFFFF2CC";
        $colorVerdeLimon    = "FFCCFFCC";
        $colorNegro    = "FF000000";

        $repetidas_mecanicas = array(
            array(
                "mecanica" => "",
                "sku" => "",
                "soldto" => ""
            )
        );

        try{

            $nuevoArray = array(
                array(
                    "columns" => [],
                    "data"    => []
                )
            );

            $fec = fecfechas::where('fecdia', 'LIKE', "%".$dia."%")
                            ->where('fecmes', 'LIKE', "%".$mes."%")
                            ->where('fecano', 'LIKE', "%".$anio."%")
                            ->first(['fecid', 'fecfecha']);

            if($fec){
                
                $csps = cspcanalessucursalespromociones::leftjoin('prmpromociones as prm', 'prm.prmid', 'cspcanalessucursalespromociones.prmid')
                                                        ->leftjoin('proproductos as pro', 'pro.prosku', 'prm.prmsku')
                                                        ->leftjoin('csccanalessucursalescategorias as csc', 'csc.cscid', 'cspcanalessucursalespromociones.cscid')
                                                        ->leftjoin('cancanales as can', 'can.canid', 'csc.canid')
                                                        ->leftjoin('scasucursalescategorias as sca', 'sca.scaid', 'csc.scaid')
                                                        ->leftjoin('catcategorias as cat', 'cat.catid', 'sca.catid')
                                                        ->leftjoin('sucsucursales as suc', 'sca.sucid', 'suc.sucid')
                                                        ->leftjoin('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                                        ->leftjoin('gsugrupossucursales as gsu', 'gsu.gsuid', 'suc.gsuid')
                                                        ->leftjoin('cascanalessucursales as cas', 'cas.casid', 'suc.casid')
                                                        ->where(function ($query) use($sucs) {
                                                            foreach($sucs as $suc){
                                                                if(isset($suc['sucpromociondescarga'])){
                                                                    if($suc['sucpromociondescarga'] == true){
                                                                        $query->orwhere('sca.sucid', $suc['sucid']);
                                                                    }
                                                                }
                                                            }
                                                        })
                                                        ->where('cspcanalessucursalespromociones.fecid', $fec->fecid)
                                                        ->get([
                                                            'cspcanalessucursalespromociones.cspid',
                                                            'casnombre',
                                                            'zonnombre',
                                                            'gsunombre',
                                                            'sucnombre',
                                                            'sucsoldto',
                                                            'cannombre',
                                                            'catnombre',
                                                            'prm.prmsku',
                                                            'pronombre',
                                                            'prmmecanica',
                                                            'cspcantidadcombo',
                                                            'cspcantidadplancha',
                                                            'csptotalcombo',
                                                            'csptotalplancha',
                                                            'csptotal',
                                                            'cspiniciopromo',
                                                            'cspfinpromo'
                                                        ]);


                // for ($i=1; $i <= $numRows ; $i++) {
                foreach($csps as $posicionCsp => $csp){

                    if($posicionCsp == 0){

                        $arrayTitulos = array();

                        if(isset($re_columnas)){

                            foreach($re_columnas as $re_columna){

                                if($re_columna['columna'] == "Inicio Promoción"){
                                    
                                    $arrayTitulos[] = array(
                                        "title" => "Inicio Promo",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Fin Promoción"){

                                    $arrayTitulos[] = array(
                                        "title" => "Fin Promo",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Región"){

                                    $arrayTitulos[] = array(
                                        "title" => "Región",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Zona"){

                                    $arrayTitulos[] = array(
                                        "title" => "Zona",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Grupo"){

                                    $arrayTitulos[] = array(
                                        "title" => "Grupo",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Cliente Hml"){

                                    $arrayTitulos[] = array(
                                        "title" => "Cliente Hml",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Sold To"){

                                    $arrayTitulos[] = array(
                                        "title" => "Sold To",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Tipo de Cliente"){

                                    $arrayTitulos[] = array(
                                        "title" => "Tipo de Cliente",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Categoría"){

                                    $arrayTitulos[] = array(
                                        "title" => "Categoría",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Sku"){

                                    $arrayTitulos[] = array(
                                        "title" => "Sku",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Producto"){

                                    $arrayTitulos[] = array(
                                        "title" => "Producto",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Mecánica"){

                                    $arrayTitulos[] = array(
                                        "title" => "Mecánica",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorAzul
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorBlanco
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Planchas a rotar"){

                                    $arrayTitulos[] = array(
                                        "title" => "Planchas a rotar o (Sell Out)",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorNaranjaClaro
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorNegro
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Reconocer x PL S/IGV"){

                                    $arrayTitulos[] = array(
                                        "title" => "Reconocer x PL S/IGV",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorNaranjaClaro
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorNegro
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "# Combos"){

                                    $arrayTitulos[] = array(
                                        "title" => "# Combos",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorNaranjaClaro
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorNegro
                                                )
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Reconocer x Combo S/"){

                                    $arrayTitulos[] = array(
                                        "title" => "Reconocer x Combo S/",
                                        "style" => array(
                                            "fill" => array(
                                                "patternType" => "solid",
                                                "fgColor" => array(
                                                    "rgb" => $colorNaranjaClaro
                                                )
                                            ),
                                            "font" => array(
                                                "color" => array(
                                                    "rgb" => $colorNegro
                                                )
                                            )
                                        )
                                    );

                                }else{

                                }

                            }

                        }else{

                            $arrayTitulos = array(
                                array(
                                    "title" => "Inicio Promo",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Fin Promo",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Región",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Zona",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Grupo",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Cliente Hml",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Sold To",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Cliente",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Tipo de Cliente",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Categoría",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Sku",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Producto",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Mecánica",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorAzul
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorBlanco
                                            )
                                        )
                                    )
                                ),
    
                                array(
                                    "title" => "Planchas a rotar o (Sell Out)",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorNaranjaClaro
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorNegro
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Reconocer x PL S/IGV",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorNaranjaClaro
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorNegro
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "# Combos",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorNaranjaClaro
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorNegro
                                            )
                                        )
                                    )
                                ),
                                array(
                                    "title" => "Reconocer x Combo S/",
                                    "style" => array(
                                        "fill" => array(
                                            "patternType" => "solid",
                                            "fgColor" => array(
                                                "rgb" => $colorNaranjaClaro
                                            )
                                        ),
                                        "font" => array(
                                            "color" => array(
                                                "rgb" => $colorNegro
                                            )
                                        )
                                    )
                                ),
                            );

                        }

                        $nuevoArray[0]['columns'] = $arrayTitulos;

                    }

                    $encontroMecanicaDup = false;

                    foreach($repetidas_mecanicas as $repetidas_mecanica){
                        if($csp->prmmecanica == $repetidas_mecanica['mecanica']){
                            if($csp->prmsku == $repetidas_mecanica['sku']){
                                if($csp->sucsoldto == $repetidas_mecanica['soldto']){

                                    $encontroMecanicaDup = true;

                                }
                            }
                        }
                    }

                    if($encontroMecanicaDup == true){

                    }else{

                        $repetidas_mecanicas[] = array(
                            "mecanica" => $csp->prmmecanica,
                            "sku"      => $csp->prmsku,
                            "soldto"   => $csp->sucsoldto
                        );

                        $desc_casnombre = $csp->casnombre;
                        $desc_zonnombre = $csp->zonnombre;
                        $desc_gsunombre = $csp->gsunombre;
                        $desc_sucnombre = $csp->sucnombre;
                        $desc_sucsoldto = $csp->sucsoldto;
                        $desc_cannombre = $csp->cannombre;
                        $desc_catnombre = $csp->catnombre;
                        $desc_prmsku    = $csp->prmsku;
                        $desc_pronombre = $csp->pronombre;
                        $desc_prmmecanica = $csp->prmmecanica;

                        $desc_cspcantidadcombo   = $csp->cspcantidadcombo;
                        $desc_cspcantidadplancha = $csp->cspcantidadplancha;
                        $desc_csptotalcombo      = $csp->csptotalcombo;
                        $desc_csptotalplancha    = $csp->csptotalplancha;
                        $desc_csptotal           = $csp->csptotal;

                        $desc_cspiniciopromo     = $csp->cspiniciopromo;
                        $desc_cspfinpromo        = $csp->cspfinpromo;

                        if($desc_casnombre == null || $desc_casnombre == " " || $desc_casnombre == "-" ){
                            $desc_casnombre = "0";
                        }
                        if($desc_zonnombre == null || $desc_zonnombre == " " || $desc_zonnombre == "-" ){
                            $desc_zonnombre = "0";
                        }
                        if($desc_gsunombre == null || $desc_gsunombre == " " || $desc_gsunombre == "-" ){
                            $desc_gsunombre = "0";
                        }
                        if($desc_sucnombre == null || $desc_sucnombre == " " || $desc_sucnombre == "-" ){
                            $desc_sucnombre = "0";
                        }
                        if($desc_sucsoldto == null || $desc_sucsoldto == " " || $desc_sucsoldto == "-" ){
                            $desc_sucsoldto = "0";
                        }
                        if($desc_cannombre == null || $desc_cannombre == " " || $desc_cannombre == "-" ){
                            $desc_cannombre = "0";
                        }
                        if($desc_catnombre == null || $desc_catnombre == " " || $desc_catnombre == "-" ){
                            $desc_catnombre = "0";
                        }
                        if($desc_prmsku == null || $desc_prmsku == " " || $desc_prmsku == "-" ){
                            $desc_prmsku = "0";
                        }
                        if($desc_pronombre == null || $desc_pronombre == " " || $desc_pronombre == "-" ){
                            $desc_pronombre = "0";
                        }
                        if($desc_prmmecanica == null || $desc_prmmecanica == " " || $desc_prmmecanica == "-" ){
                            $desc_prmmecanica = "0";
                        }
                        if($desc_cspcantidadcombo == null || $desc_cspcantidadcombo == " " || $desc_cspcantidadcombo == "-" ){
                            $desc_cspcantidadcombo = "0";
                        }
                        if($desc_cspcantidadplancha == null || $desc_cspcantidadplancha == " " || $desc_cspcantidadplancha == "-" ){
                            $desc_cspcantidadplancha = "0";
                        }
                        if($desc_csptotalcombo == null || $desc_csptotalcombo == " " || $desc_csptotalcombo == "-" ){
                            $desc_csptotalcombo = "0";
                        }
                        if($desc_csptotalplancha == null || $desc_csptotalplancha == " " || $desc_csptotalplancha == "-" ){
                            $desc_csptotalplancha = "0";
                        }
                        if($desc_csptotal == null || $desc_csptotal == " " || $desc_csptotal == "-" ){
                            $desc_csptotal = "0";
                        }

                        if(is_numeric($desc_cspcantidadcombo)){
                            $desc_cspcantidadcombo = floatval($desc_cspcantidadcombo);
                        }

                        if(is_numeric($desc_cspcantidadplancha)){
                            $desc_cspcantidadplancha = floatval($desc_cspcantidadplancha);
                        }

                        if(is_numeric($desc_csptotalcombo)){
                            $desc_csptotalcombo = floatval($desc_csptotalcombo);
                        }

                        if(is_numeric($desc_csptotalplancha)){
                            $desc_csptotalplancha = floatval($desc_csptotalplancha);
                        }

                        if(is_numeric($desc_csptotal)){
                            $desc_csptotal = floatval($desc_csptotal);
                        }


                        if($desc_cspiniciopromo == null){
                            $fechaInicio = date("d-m-Y", strtotime($fec->fecfecha));
                            $desc_cspiniciopromo = $fechaInicio;
                        }
                        if($desc_cspfinpromo == null){
                            $fechaFinal = date("m-Y", strtotime($fec->fecfecha));
                            $desc_cspfinpromo = "30-".$fechaFinal;
                        }

                        $arrayFilaExcel = array();

                        if(isset($re_columnas)){

                            foreach($re_columnas as $re_columna){

                                if($re_columna['columna'] == "Inicio Promoción"){
                                    
                                    $arrayFilaExcel[] = array(
                                        "value" => $csp->cspiniciopromo,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Fin Promoción"){
                                    
                                    $arrayFilaExcel[] = array(
                                        "value" => $csp->cspfinpromo,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Región"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_casnombre,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Zona"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_zonnombre,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Grupo"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_gsunombre,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Cliente Hml"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_sucnombre,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Sold To"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_sucsoldto,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Tipo de Cliente"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_cannombre,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Categoría"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_catnombre,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Sku"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_prmsku,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Producto"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_pronombre,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Mecánica"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_prmmecanica,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            )
                                        )
                                    );

                                }else if($re_columna['columna'] == "Planchas a rotar"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_cspcantidadplancha,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            ),
                                            "numFmt" => "#,##0.00"
                                        )
                                    );

                                }else if($re_columna['columna'] == "Reconocer x PL S/IGV"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_csptotalplancha,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            ),
                                            "numFmt" => "#,##0.00"
                                        )
                                    );

                                }else if($re_columna['columna'] == "# Combos"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_cspcantidadcombo,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            ),
                                            "numFmt" => "#,##0.00"
                                        )
                                    );
                                    
                                }else if($re_columna['columna'] == "Reconocer x Combo S/"){

                                    $arrayFilaExcel[] = array(
                                        "value" => $desc_csptotalcombo,
                                        "style" => array(
                                            "font" => array(
                                                "sz" => "9"
                                            ),
                                            "numFmt" => "#,##0.00"
                                        )
                                    );

                                }else{

                                }

                            }

                        }else{

                            $arrayFilaExcel = array(
                                array(
                                    "value" => $csp->cspiniciopromo,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $csp->cspfinpromo,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_casnombre,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_zonnombre,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_gsunombre,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_sucnombre,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_sucsoldto,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_sucnombre,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_cannombre,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_catnombre,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_prmsku,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_pronombre,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_prmmecanica,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        )
                                    )
                                ),
                                array(
                                    "value" => $desc_cspcantidadplancha,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        ),
                                        "numFmt" => "#,##0.00"
                                    )
                                ),
                                array(
                                    "value" => $desc_csptotalplancha,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        ),
                                        "numFmt" => "#,##0.00"
                                    )
                                ),
                                array(
                                    "value" => $desc_cspcantidadcombo,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        ),
                                        "numFmt" => "#,##0.00"
                                    )
                                ),
                                array(
                                    "value" => $desc_csptotalcombo,
                                    "style" => array(
                                        "font" => array(
                                            "sz" => "9"
                                        ),
                                        "numFmt" => "#,##0.00"
                                    )
                                ),
        
                            );

                        }
                        
                        $nuevoArray[0]['data'][] = $arrayFilaExcel;
                    }
                }



                $respuesta = true;
                $datos     = $nuevoArray;
                // $archivo   = $car->carnombrearchivo;
                       
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
            'repetidas_mecanicas'     => $repetidas_mecanicas,
            // 'archivo'        => $archivo
        ]);

        return $requestsalida;


    }

    public function MostrarSucursalesDescargarPromocionesExcel(Request $request)
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
            "L",
            "M",
            "N",
            "O",
            "P",
            "Q",
            "R",
            "S",
            "T",
            "U",
            "V",
            "W",
            // "X",
            // "Y",
            "Z",
            "AA",
            // "AB",
            // "AC",
            "AD",
            "AE",
            // "AF",
            // "AG",
            "AH",
            "AI",
            "AJ",
            "AK",
            "AL",
            "AM",
            "AN",
            "AO",
            "AP",
            "AQ",
            "AR",
            // "AS",
            // "AT",
            // "AU",
            // "AV"
        ];

        $columnasExcel = [
            "A",
            "B",
            "region",
            "zona",
            "grupo",
            "clientehml",
            "P",
            "Q",
            "AK",
            "W",
            "AA",
            "V",
            "AN",
            "AQ",
            "AO",
            "AP"
        ];

        $colorPlomo         = "FF595959";
        $colorBlanco        = "FFFFFFFF";
        $colorAzul          = "FF002060";
        $colorVerdeClaro    = "FF66FF33";
        $colorRosa          = "FFFF9999";
        $colorNaranjaClaro  = "FFFFC000";
        $colorPiel          = "FFFFF2CC";
        $colorVerdeLimon    = "FFCCFFCC";
        $colorNegro    = "FF000000";

        try{

            $uss = sucsucursales::join('zonzonas as zon', 'zon.zonid', 'sucsucursales.zonid')
                                ->join('gsugrupossucursales as gsu', 'gsu.gsuid', 'sucsucursales.gsuid')
                                ->join('cascanalessucursales as cas', 'cas.casid', 'sucsucursales.casid')
                                ->where(function ($query) use($sucs) {
                                    foreach($sucs as $suc){
                                        if(isset($suc['sucpromociondescarga'])){
                                            if($suc['sucpromociondescarga'] == true){
                                                $query->orwhere('sucid', $suc['sucid']);
                                            }
                                        }
                                    }
                                })
                                ->get([
                                    'casnombre',
                                    'zonnombre',
                                    'gsunombre',
                                    'sucsoldto',
                                    'sucnombre'
                                ]);

            $nuevoArray = array(
                array(
                    "columns" => [],
                    "data"    => []
                )
            );

            $fec = fecfechas::where('fecdia', 'LIKE', "%".$dia."%")
                            ->where('fecmes', 'LIKE', "%".$mes."%")
                            ->where('fecano', 'LIKE', "%".$anio."%")
                            ->first(['fecid', 'fecfecha']);

            if($fec){
                $car = carcargasarchivos::where('fecid', $fec->fecid)
                                    ->where('tcaid', 1)
                                    ->first(['carid', 'carnombrearchivo']);

                if($car){
                    $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/'.$car->carnombrearchivo;
                    // $fichero_subido = base_path().'/public/Sistema/cargaArchivos/promociones/Promociones 2021 Enero.xlsx';

                    $objPHPExcel    = IOFactory::load($fichero_subido);
                    $objPHPExcel->setActiveSheetIndex(0);
                    $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                    $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                    for ($i=1; $i <= $numRows ; $i++) {

                        if($i == 1){

                            $arrayTitulos = array(
                                array(
                                    "title" => ""
                                )
                            );

                            $contadorTitulos = 0;
                            foreach($columnasExcel as $abc) {  
                                if($abc == "region"){
                                    $columnasFilas = "Región";
                                }else if($abc == "zona"){
                                    $columnasFilas = "Zona";
                                }else if($abc == "grupo"){
                                    $columnasFilas = "Grupo";
                                }else if($abc == "clientehml"){
                                    $columnasFilas = "Cliente Hml";
                                }else{
                                    $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                                }
                                
                                if($columnasFilas == "SOLD TO"){
                                    $columnasFilas = "Sold To";
                                }else if($columnasFilas == "Año"){
                                    $columnasFilas = "Inicio Promo";
                                }else if($columnasFilas == "Mes"){
                                    $columnasFilas = "Fin Promo";
                                }
                                
                                $arrayTitulos[$contadorTitulos]['title'] = $columnasFilas;
                                $arrayTitulos[$contadorTitulos]['style']['fill']['patternType'] = 'solid';
                                $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorAzul;
                                $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;

                                if($abc == "AN" || $abc == "AQ" || $abc == "AO" || $abc == "AP" ){
                                    $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorNaranjaClaro;
                                    $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorNegro;
                                }

                                // if($abc == "A" || $abc == "B" || $abc == "J" || $abc == "K" || $abc == "M" || $abc == "N" || $abc == "Q" || $abc == "T" || $abc == "U" || $abc == "V" || $abc == "Z" || $abc == "AD" || $abc == "AG" || $abc == "AI" || $abc == "AK" || $abc == "AM" || $abc == "AN" || $abc == "AQ" || $abc == "AR" || $abc == "AS" || $abc == "AT"){
                                //     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorPlomo;
                                //     $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                                // }else if($abc == "C" || $abc == "D" || $abc == "E" || $abc == "F" || $abc == "G" || $abc == "H" || $abc == "I" || $abc == "L" || $abc == "O" || $abc == "P" || $abc == "Y" || $abc == "AC" || $abc == "AH" || $abc == "AL" || $abc == "AU"){
                                //     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorAzul;
                                //     $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                                // }else if($abc == "R" || $abc == "S" || $abc == "AO" || $abc == "AP"){
                                //     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorNaranjaClaro;
                                //     $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                                // }else if($abc == "W" || $abc == "X" || $abc == "AA" || $abc == "AB" || $abc == "AE" || $abc == "AF" || $abc == "R" ){
                                //     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorVerdeClaro;
                                // }else if($abc == "AJ"){
                                //     $arrayTitulos[$contadorTitulos]['style']['fill']['fgColor']['rgb'] = $colorRosa;
                                //     $arrayTitulos[$contadorTitulos]['style']['font']['color']['rgb'] = $colorBlanco;
                                // }
                                $contadorTitulos = $contadorTitulos+1;
                            }

                            $nuevoArray[0]['columns'] = $arrayTitulos;

                        }else{
                            $nombreTituloSoldTo = $objPHPExcel->getActiveSheet()->getCell('O2')->getCalculatedValue();
                            $soldto = "";
                            if($nombreTituloSoldTo == "SOLD TO"){
                                $soldto = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();    
                            }else{
                                $soldto = $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
                            }

                            $soldto = trim($soldto);


                            $pertenecedata = false;
                            
                            $sucursal = array();

                            foreach($uss as $u){

                                $pos = strpos($soldto, $u->sucsoldto);
                                // if($u->sucsoldto == $soldto ){
                                if($pos === false ){
                                    $pertenecedata = false;
                                }else{
                                    $pertenecedata = true;
                                    $sucursal = array(
                                        "casnombre" => $u->casnombre,
                                        "zonnombre" => $u->zonnombre,
                                        "gsunombre" => $u->gsunombre,
                                        "sucsoldto" => $u->sucsoldto,
                                        "sucnombre" => $u->sucnombre
                                    );

                                    break;
                                }
                            }

                            if($pertenecedata == true){
                                $arrayFilaExcel = array(
                                    array(
                                        "value" => ""
                                    )
                                );
                                $contadorColumna = 0;

                                foreach($columnasExcel as $abc) {

                                    if($abc == "region"){
                                        $columnasFilas = $sucursal['casnombre'];
                                    }else if($abc == "zona"){
                                        $columnasFilas = $sucursal['zonnombre'];
                                    }else if($abc == "grupo"){
                                        $columnasFilas = $sucursal['gsunombre'];
                                    }else if($abc == "clientehml"){
                                        $columnasFilas = $sucursal['sucnombre'];
                                    }else{
                                        $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                                    }
                                    
                                    if($columnasFilas == null || $columnasFilas == " " ){
                                        $columnasFilas = "0";
                                    }else if($columnasFilas == "-"){
                                        $columnasFilas = "0";
                                    }

                                    
                                    if($abc == "AR"){
                                        
                                    }

                                    if($abc != "A" && $abc != "P" && $abc != "Z" && $abc != "AD" && $abc != "AR" && $abc != "AN" && $abc != "AO" && $abc != "AP" && $abc != "AQ"){
                                        if(is_numeric($columnasFilas)){
                                            $columnasFilas = number_format($columnasFilas, 2);
                                            $columnasFilas = floatval($columnasFilas);
                                        }
                                    }

                                    if($abc == "AN" || $abc == "AQ" || $abc == "AO" || $abc == "AP" ){
                                        
                                        if(is_numeric ( $columnasFilas )){
                                            $columnasFilas = floatval($columnasFilas);
                                            $arrayFilaExcel[$contadorColumna]['style']['numFmt'] = '#,##0.00';
                                        }else{
                                            
                                        }

                                    }

                                    if($abc == "A" ){
                                        $fechaInicio = date("d-m-Y", strtotime($fec->fecfecha));
                                        $columnasFilas = $fechaInicio;
                                    }else if($abc == "B"){
                                        $fechaFinal = date("m-Y", strtotime($fec->fecfecha));
                                        $columnasFilas = "30-".$fechaFinal;
                                    }


                                    $arrayFilaExcel[$contadorColumna]['style']['font']['sz'] = '9';
                                    $arrayFilaExcel[$contadorColumna]['value'] = $columnasFilas;

                                    // if($abc == "L" || $abc == "O" || $abc == "P" || $abc == "R" || $abc == "S" || $abc == "Y" || $abc == "AC" || $abc == "AL" || $abc == "AU"){
                                    //     $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                                    //     $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorPiel;
                                    // }else if($abc == "W" || $abc == "X" ){
                                    //     $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                                    //     $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorVerdeLimon;
                                    // }else if($abc == "AH" || $abc == "AR"){
                                    //     $arrayFilaExcel[$contadorColumna]['style']['fill']['patternType']    = 'solid';
                                    //     $arrayFilaExcel[$contadorColumna]['style']['fill']['fgColor']['rgb'] = $colorNaranjaClaro;
                                    // }

                                    $contadorColumna = $contadorColumna+1;
                                }

                                $nuevoArray[0]['data'][] = $arrayFilaExcel;
                            }
                        }
                    }

                    $respuesta = true;
                    $datos     = $nuevoArray;
                    $archivo   = $car->carnombrearchivo;
                }else{
                    $respuesta = false;
                    $mensaje = "Lo sentimos, no pudimos encontrar un registro de excel subido a este mes seleccionado";
                    $mensajeDetalle = "Vuelve a seleccionar la fecha o comunicate con soporte";
                }         
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
            'archivo'        => $archivo
        ]);

        return $requestsalida;


    }
}
