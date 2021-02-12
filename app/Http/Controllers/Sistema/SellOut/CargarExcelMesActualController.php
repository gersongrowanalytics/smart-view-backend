<?php

namespace App\Http\Controllers\Sistema\SellOut;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\sucsucursales;
use App\proproductos;

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
            array(
                 "title" => "RH1 Business Unit",
                 "title" => "RH3 Region",
                 "title" => "RH5 Area",
                 "title" => "Año",
                 "title" => "Mes",
                 "title" => "PL",
                 "title" => "Sales Office Sold",
                 "title" => "SoldTo",
                 "title" => "name SoldTo",
                 "title" => "Material Number",
                 "title" => "name material",
                 "title" => "Sector Profit Center",
                 "title" => "Business Category|Vision",
                 "title" => "DOMESTIC",
                 "title" => "Result",
                 "title" => "Categoria NIV",
            )
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

        $contadorColumna = 0;

        for($cont = 1; $cont <= 31; $cont++){
            $datos = json_decode( file_get_contents('http://backend-api.leadsmartview.com/ws/obtenerSellOutEspecifico/'.$anio.'/'.$mes.'/'.$cont), true );

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
                }


                $contadorColumna = $contadorColumna + 1;

            }

        }

        $nuevoArray[0]['data'][] = $arrayFilaExcel;

        $requestsalida = response()->json([
            'datos' => $datos,
            'respuesta' => $respuesta,
        ]);

        return $requestsalida;
    }
}
