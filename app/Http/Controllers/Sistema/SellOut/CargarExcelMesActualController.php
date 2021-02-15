<?php

namespace App\Http\Controllers\Sistema\SellOut;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\sucsucursales;
use App\proproductos;
use App\catcategorias;

class CargarExcelMesActualController extends Controller
{
    public function CargarExcelMesActual(Request $request)
    {

        $respuesta = true;
        
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
        ];

        $nuevoArray = array(
            array(
                "columns" => [],
                "data"    => []
            )
        );

        $arrayTitulos = array(
            array("title" => "SOLDTO",),
            array("title" => "SUCURSAL"),
            array("title" => "CATEGORIA"),
            array("title" => "REAL"),
            array("title" => "AÑO"),
            array("title" => "MES"),
        );

        $nuevoArray[0]['columns'] = $arrayTitulos;

        $arrayFilaExcel = array(
            array(
                "value" => ""
            )
        );

        $meses = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

        $fechaActual = date('Y-m-d H:i:s');
        $mes = $meses[date('n', strtotime($fechaActual))-1];
        $anio = date("Y", strtotime($fechaActual));

        $datosSucursales = [];

        $cats = catcategorias::all();

        $datos = json_decode( file_get_contents('http://backend-api.leadsmartview.com/ws/obtenerSellOutEspecifico/'.$anio.'/'.$mes.'/0'), true );

        foreach($datos as $posicion => $dato){

            $anio      = $dato['YEAR'];
            $soldto    = $dato['COD_SOLD_TO'];
            $sku       = $dato['SKU'];
            $real      = $dato['SELLS'];

            if($dato['SELLS'] == null){
                $real = 0;
            }else{
                $real = $dato['SELLS'];
            }

            $dia       = $dato['DAY'];
            if(strlen($dia) == 1){
                $dia = "0$dia";
            }

            $contadorColumna = 0;

            if(sizeof($datosSucursales) > 0){

                foreach($datosSucursales as $datoSucursal){

                    if($datoSucursal['SOLDTO'] == $soldto){
                        $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                    ->where('prosku', $sku)
                                    ->first(['cat.catnombre']);
                            
                        if($pro){
                            if($datoSucursal['CATEGORIA'] == $pro->catnombre){
                                $datoSucursal['REAL'] = $datoSucursal['REAL'] + $real;
                            }else{
                                $datoSucursal['REAL'] = $real;
                            }
                        }else{

                        }
                    }

                }

            }else{
                $datosSucursales[0]["SOLDTO"] = $soldto;

                $suc = sucsucursales::where('sucsoldto', $soldto)->first();
                if($suc){
                    $datosSucursales[0]["SUCURSAL"] = $suc->sucnombre;
                }else{
                    $datosSucursales[0]["SUCURSAL"] = "NO EXISTE";
                }

                $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                        ->where('prosku', $sku)
                                        ->first(['cat.catnombre']);
                if($pro){
                    $datosSucursales[0]["CATEGORIA"] = $pro->catnombre;
                }else{
                    $datosSucursales[0]["CATEGORIA"] = "NO EXISTE";
                }
                
                $datosSucursales[0]["REAL"] = $real;
                $datosSucursales[0]["AÑO"] = $anio;
                $datosSucursales[0]["MES"] = $mes;
                
            }

            foreach($columnasExcel as $abc) {
                if($abc == "D"){
                    $arrayFilaExcel[$contadorColumna]['value'] = $anio;
                }else if($abc == "E"){
                    $arrayFilaExcel[$contadorColumna]['value'] = $mes;
                }else if($abc == "H"){
                    $arrayFilaExcel[$contadorColumna]['value'] = $soldto;
                }else if($abc == "I"){

                    $suc = sucsucursales::where('sucsoldto', $soldto)->first();
                    if($suc){
                        $arrayFilaExcel[$contadorColumna]['value'] = $suc->sucnombre;
                    }else{
                        $arrayFilaExcel[$contadorColumna]['value'] = "NO EXISTE";
                    }
                }else if($abc == "J"){
                    $arrayFilaExcel[$contadorColumna]['value'] = $sku;
                }else if($abc == "M"){

                    $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                        ->where('prosku', $sku)
                                        ->first(['cat.catnombre']);
                    if($pro){
                        $arrayFilaExcel[$contadorColumna]['value'] = $pro->catnombre;
                    }else{
                        $arrayFilaExcel[$contadorColumna]['value'] = "NO EXISTE";   
                    }
                }else if($abc == "N"){
                    $arrayFilaExcel[$contadorColumna]['value'] = $real;
                }else if($abc == "O"){
                    $arrayFilaExcel[$contadorColumna]['value'] = $real;
                }else{
                    $arrayFilaExcel[$contadorColumna]['value'] = " - ";
                }

                $contadorColumna = $contadorColumna + 1;
            }
            $nuevoArray[0]['data'][] = $arrayFilaExcel;
        }

        $datos = $nuevoArray;

        $requestsalida = response()->json([
            'datos' => $datos,
            'respuesta' => $respuesta,
        ]);

        return $requestsalida;
    }

    public function CargarExcelMesActualSoldTos(Request $request)
    {

        $respuesta = true;
        
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
        ];

        $nuevoArray = array(
            array(
                "columns" => [],
                "data"    => []
            )
        );

        $arrayTitulos = array(
            array(
                 "title" => "RH1 Business Unit",
            ),
            // array("title" => "RH3 Region"),
            // array("title" => "RH5 Area"),
            // array("title" => "Año"),
            // array("title" => "Mes"),
            // array("title" => "PL"),
            // array("title" => "Sales Office Sold"),
            // array("title" => "SoldTo"),
            // array("title" => "name SoldTo"),
            // array("title" => "Material Number"),
            // array("title" => "name material"),
            // array("title" => "Sector Profit Center"),
            // array("title" => "Business Category|Vision"),
            // array("title" => "DOMESTIC"),
            // array("title" => "Result"),
            // array("title" => "Categoria NIV"),
        );

        $nuevoArray[0]['columns'] = $arrayTitulos;

        $arrayFilaExcel = array(
            array(
                "value" => ""
            )
        );

        $meses = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

        $fechaActual = date('Y-m-d H:i:s');
        $mes = $meses[date('n', strtotime($fechaActual))-1];
        $anio = date("Y", strtotime($fechaActual));


        // for($cont = 1; $cont <= 31; $cont++){
            $datos = json_decode( file_get_contents('http://backend-api.leadsmartview.com/ws/obtenerSellOutEspecifico/'.$anio.'/'.$mes.'/0'), true );

            foreach($datos as $posicion => $dato){

                $anio      = $dato['YEAR'];
                $soldto    = $dato['COD_SOLD_TO'];
                $sku       = $dato['SKU'];
                $real      = $dato['SELLS'];

                if($dato['SELLS'] == null){
                    $real = 0;
                }else{
                    $real = $dato['SELLS'];
                }

                $dia       = $dato['DAY'];
                if(strlen($dia) == 1){
                    $dia = "0$dia";
                }

                $contadorColumna = 0;

                foreach($columnasExcel as $abc) {
                    if($abc == "D"){
                        $arrayFilaExcel[$contadorColumna]['value'] = $anio;
                    }else if($abc == "E"){
                        $arrayFilaExcel[$contadorColumna]['value'] = $mes;
                    }else if($abc == "H"){
                        $arrayFilaExcel[$contadorColumna]['value'] = $soldto;
                    }else if($abc == "I"){

                        $suc = sucsucursales::where('sucsoldto', $soldto)->first();
                        if($suc){
                            $arrayFilaExcel[$contadorColumna]['value'] = $suc->sucnombre;
                        }else{
                            $arrayFilaExcel[$contadorColumna]['value'] = "NO EXISTE";
                        }
                    }else if($abc == "J"){
                        $arrayFilaExcel[$contadorColumna]['value'] = $sku;
                    }else if($abc == "M"){

                        $pro = proproductos::join('catcategorias as cat', 'cat.catid', 'proproductos.catid')
                                            ->where('prosku', $sku)
                                            ->first(['cat.catnombre']);
                        if($pro){
                            $arrayFilaExcel[$contadorColumna]['value'] = $pro->catnombre;
                        }else{
                            $arrayFilaExcel[$contadorColumna]['value'] = "NO EXISTE";   
                        }
                    }else if($abc == "N"){
                        $arrayFilaExcel[$contadorColumna]['value'] = $real;
                    }else if($abc == "O"){
                        $arrayFilaExcel[$contadorColumna]['value'] = $real;
                    }else{
                        $arrayFilaExcel[$contadorColumna]['value'] = " - ";
                    }

                    $contadorColumna = $contadorColumna + 1;
                }
                $nuevoArray[0]['data'][] = $arrayFilaExcel;
            }

        // }

        $datos = $nuevoArray;

        $requestsalida = response()->json([
            'datos' => $datos,
            'respuesta' => $respuesta,
        ]);

        return $requestsalida;
    }
}
