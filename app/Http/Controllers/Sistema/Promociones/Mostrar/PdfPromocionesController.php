<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use iio\libmergepdf\Merger;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\usuusuarios;
use App\sucsucursales;

class PdfPromocionesController extends Controller
{
    public function MostrarPdfPromociones(Request $request)
    {
        $usutoken   = $request->header('api_token');

        $re_idsucursal = $request['idsucursal'];
        $re_anio = $request['anio'];
        $re_mes = $request['mes'];

        $suc = sucsucursales::where('sucid', $re_idsucursal)->first();

        $titulocaratula = $suc->sucnombre." - ".$re_mes." ".$re_anio;


        if($re_mes == "ENE"){
            $re_mes = "Enero";
        }else if($re_mes == "FEB"){
            $re_mes = "Febrero";
        }else if($re_mes == "MAR"){
            $re_mes = "Marzo";
        }else if($re_mes == "ABR"){
            $re_mes = "Abril";
        }else if($re_mes == "MAY"){
            $re_mes = "Mayo";
        }else if($re_mes == "JUN"){
            $re_mes = "Junio";
        }else if($re_mes == "JUL"){
            $re_mes = "Julio";
        }else if($re_mes == "AGO"){
            $re_mes = "Agosto";
        }else if($re_mes == "SET"){
            $re_mes = "Setiembre";
        }else if($re_mes == "OCT"){
            $re_mes = "Octubre";
        }else if($re_mes == "NOV"){
            $re_mes = "Noviembre";
        }else if($re_mes == "DIC"){
            $re_mes = "Diciembre";
        }
        $fechaPromocion = $re_mes." del ".$re_anio;

        $usu = usuusuarios::where('usutoken', $usutoken)->first(['usuid']);

        if($usu){
            $usuid = $usu->usuid;
        }
        
        $dataCategorias = $request['categorias'];

        $m = new Merger();

        
        $data = array(
            array(
                "catid" => 1,
                "catnombre" => "Family Care",
                "canales" => array(
                    array(
                        "cannombre" => "Mayorista",
                        "promocionesOrdenadas" => array(
                            array(
                                "cspcantidadcombo" => "2012",
                                "cspcantidadplancha" => "11"
                            )
                        )
                    )
                )
            ),
            array(
                "catid" => 2,
                "catnombre" => "Infant Care",
                "canales" => array(
                    array(
                        "cannombre" => "Mayorista",
                        "promocionesOrdenadas" => array(
                            array(
                                "cspcantidadcombo" => "2012",
                                "cspcantidadplancha" => "11"
                            )
                        )
                    )
                )
            ),
            array(
                "catid" => 3,
                "catnombre" => "Adult Care",
                "canales" => array(
                    array(
                        "cannombre" => "Mayorista",
                        "promocionesOrdenadas" => array(
                            array(
                                "cspcantidadcombo" => "2012",
                                "cspcantidadplancha" => "11"
                            )
                        )
                    )
                )
            ),

            array(
                "catid" => 4,
                "catnombre" => "Wipes",
                "canales" => array(
                    array(
                        "cannombre" => "Mayorista",
                        "promocionesOrdenadas" => array(
                            array(
                                "cspcantidadcombo" => "2012",
                                "cspcantidadplancha" => "11"
                            )
                        )
                    )
                )
            ),

            array(
                "catid" => 5,
                "catnombre" => "Fem Care",
                "canales" => array(
                    array(
                        "cannombre" => "Mayorista",
                        "promocionesOrdenadas" => array(
                            array(
                                "cspcantidadcombo" => "2012",
                                "cspcantidadplancha" => "11"
                            )
                        )
                    )
                )
            ),

            array(
                "catid" => 6,
                "catnombre" => "MultiCategoria",
                "canales" => array(
                    array(
                        "cannombre" => "Mayorista",
                        "promocionesOrdenadas" => array(
                            array(
                                "cspcantidadcombo" => "2012",
                                "cspcantidadplancha" => "11"
                            )
                        )
                    )
                )
            ),
        );

        foreach($dataCategorias as $posicionDataCategoria => $dataCategoria){

            if(sizeof($dataCategoria['canales']) > 0){
                $pdf = app('dompdf.wrapper');
                $pdf->setPaper('A3','landscape');
                $pdf->loadView('pdf.promociones.indice', ["data" => $dataCategoria, "posicion" => $posicionDataCategoria, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $suc->sucnombre]);
                $m->addRaw($pdf->output());

                $numeroPdfsAbajo = $dataCategoria['cantidadPromociones'] / 3;
                $numeroPdfsAbajo = ceil($numeroPdfsAbajo);

                $mostrarPdfA4 = false;
                // if(sizeof($dataCategoria['canales']) < 3){
                //     $mostrarPdfA4 = true;
                // }

                for($i = 0; $i < $numeroPdfsAbajo; $i++ ){
                    if($i == 0){
                        $pdf2 = app('dompdf.wrapper');

                        if($mostrarPdfA4 == true){
                            $pdf2->setPaper('A4','portrait');
                            $pdf2->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 0, "hasta" => 2, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $suc->sucnombre ] );
                        }else{
                            $pdf2->setPaper('A3','landscape');
                            $pdf2->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 0, "hasta" => 2, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $suc->sucnombre  ] );
                        }

                        $m->addRaw($pdf2->output());
                    }else if($i == 1){
                        $pdf3 = app('dompdf.wrapper');
                        if($mostrarPdfA4 == true){
                            $pdf3->setPaper('A4','portrait');
                            $pdf3->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 3, "hasta" => 5, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $suc->sucnombre ] );
                        }else{
                            $pdf3->setPaper('A3','landscape');
                            $pdf3->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 3, "hasta" => 5, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $suc->sucnombre ] );
                        }
                        
                        $m->addRaw($pdf3->output());
                    }
                }
            } 

        }


        file_put_contents('Sistema/Pdf/'.$usutoken.'.pdf', $m->merge());
        
        // return $m->stream();
        return true;
        // return $pdf2->stream();
        // dd($resultPromociones);
    }
}
