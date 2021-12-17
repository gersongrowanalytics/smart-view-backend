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
use App\gsugrupossucursales;

class PdfPromocionesController extends Controller
{
    public function MostrarPdfPromociones(Request $request)
    {
        $usutoken   = $request->header('api_token');

        $re_idsucursal = $request['idsucursal'];
        $re_anio = $request['anio'];
        $re_mes = $request['mes'];
        $re_gsuid = $request['gsuid'];

        $fechasExpPromos = array(
            "fechaInicio" => "01/12",
            "fechaFinal"  => "30/12",
        );

        $nombreDTSeleccionado = "";

        if($re_gsuid != 0){
            $gsu = gsugrupossucursales::where('gsuid', $re_gsuid)->first();
            $nombreDTSeleccionado = $gsu->gsunombre;
        }else{
            $suc = sucsucursales::where('sucid', $re_idsucursal)->first();
            $nombreDTSeleccionado = $suc->sucnombre;
        }

        

        $titulocaratula = $nombreDTSeleccionado." - ".$re_mes." ".$re_anio;


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
        $fechaPromocion = $re_mes." ".$re_anio;

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

        $cantidadCategorias = 0;
        $pdfxgrupo = false;

        foreach($dataCategorias as $posicionDataCategoria => $dataCategoria){
            if(sizeof($dataCategoria['canales']) > 0){
                $cantidadCategorias = $cantidadCategorias + 1;
            }

            if($dataCategoria['scaid'] == 1){
                $pdfxgrupo = true; 
            }

            $canalesPromociones = $dataCategoria['canales'];
            foreach($canalesPromociones as $posicionCanal => $canal){
                if($posicionCanal == 0){
                    $cantidadPromociones = sizeof($canal['promocionesOrdenadas']);
                }
            }
        }

        if($pdfxgrupo == true){

            foreach($dataCategorias as $posicionDataCategoria => $dataCategoria){

                

            }

        }

        $numeroPagina = 1;

        foreach($dataCategorias as $posicionDataCategoria => $dataCategoria){

            if(sizeof($dataCategoria['canales']) > 0){
                $numeroPagina = $numeroPagina + 1;
                $pdf = app('dompdf.wrapper');
                $pdf->setPaper('A3','landscape');
                $pdf->loadView('pdf.promociones.indice', ["data" => $dataCategoria, "posicion" => $posicionDataCategoria, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "fechasExpPromos" => $fechasExpPromos]);
                $m->addRaw($pdf->output());

                $cantidadPromociones = 0;
                $canalesPromociones = $dataCategoria['canales'];
                foreach($canalesPromociones as $posicionCanal => $canal){
                    if($posicionCanal == 0){
                        $cantidadPromociones = sizeof($canal['promocionesOrdenadas']);
                    }
                }
                // $numeroPdfsAbajo = $dataCategoria['cantidadPromociones'] / 3;
                $numeroPdfsAbajo = $cantidadPromociones / 3;
                $numeroPdfsAbajo = ceil($numeroPdfsAbajo);

                $mostrarPdfA4 = false;
                // if(sizeof($dataCategoria['canales']) < 3){
                //     $mostrarPdfA4 = true;
                // }

                for($i = 0; $i < $numeroPdfsAbajo; $i++ ){
                    $numeroPagina = $numeroPagina + 1;
                    if($i == 0){
                        $pdf2 = app('dompdf.wrapper');

                        if($mostrarPdfA4 == true){
                            $pdf2->setPaper('A4','portrait');
                            $pdf2->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 0, "hasta" => 2, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }else{
                            $pdf2->setPaper('A3','landscape');
                            $pdf2->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 0, "hasta" => 2, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }

                        $m->addRaw($pdf2->output());
                    }else if($i == 1){
                        $pdf3 = app('dompdf.wrapper');
                        if($mostrarPdfA4 == true){
                            $pdf3->setPaper('A4','portrait');
                            $pdf3->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 3, "hasta" => 5, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }else{
                            $pdf3->setPaper('A3','landscape');
                            $pdf3->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 3, "hasta" => 5, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }
                        
                        $m->addRaw($pdf3->output());
                    }else if($i == 2){
                        $pdf4 = app('dompdf.wrapper');
                        if($mostrarPdfA4 == true){
                            $pdf4->setPaper('A4','portrait');
                            $pdf4->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 6, "hasta" => 8, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }else{
                            $pdf4->setPaper('A3','landscape');
                            $pdf4->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 6, "hasta" => 8, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }
                        
                        $m->addRaw($pdf4->output());
                    }else if($i == 3){
                        $pdf5 = app('dompdf.wrapper');
                        if($mostrarPdfA4 == true){
                            $pdf5->setPaper('A4','portrait');
                            $pdf5->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 9, "hasta" => 11, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }else{
                            $pdf5->setPaper('A3','landscape');
                            $pdf5->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 9, "hasta" => 11, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }
                        
                        $m->addRaw($pdf5->output());
                    }else if($i == 4){
                        $pdf6 = app('dompdf.wrapper');
                        if($mostrarPdfA4 == true){
                            $pdf6->setPaper('A4','portrait');
                            $pdf6->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 12, "hasta" => 14, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }else{
                            $pdf6->setPaper('A3','landscape');
                            $pdf6->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 12, "hasta" => 14, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }
                        
                        $m->addRaw($pdf6->output());
                    }else if($i == 5){
                        $pdf7 = app('dompdf.wrapper');
                        if($mostrarPdfA4 == true){
                            $pdf7->setPaper('A4','portrait');
                            $pdf7->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 15, "hasta" => 17, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }else{
                            $pdf7->setPaper('A3','landscape');
                            $pdf7->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 15, "hasta" => 17, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }
                        
                        $m->addRaw($pdf7->output());
                    }else if($i == 6){
                        $pdf8 = app('dompdf.wrapper');
                        if($mostrarPdfA4 == true){
                            $pdf8->setPaper('A4','portrait');
                            $pdf8->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 18, "hasta" => 20, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }else{
                            $pdf8->setPaper('A3','landscape');
                            $pdf8->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 18, "hasta" => 20, "opacidadcanal" => 1, "mostrarPdfA4" => $mostrarPdfA4, "titulocaratula" => $titulocaratula, "categorias" => $dataCategorias, "fechaPromocion" => $fechaPromocion, "sucursal" => $nombreDTSeleccionado, "cantidadCategorias" => $cantidadCategorias, "pagina" => $numeroPagina, "fechasExpPromos" => $fechasExpPromos ] );
                        }
                        
                        $m->addRaw($pdf8->output());
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

    public function AgregarFechaExpiracionPromociones($anio, $mes)
    {
        $fechaInicio = "01/";
        $fechaFin = "30/";

        if($mes == "ENE"){
            $fechaInicio = $fechaInicio."01";
            $fechaFin = $fechaFin."01";
        }else if($mes == "FEB"){
            $fechaInicio = $fechaInicio."02";
            $fechaFin = $fechaFin."02";
        }else if($mes == "MAR"){
            $fechaInicio = $fechaInicio."03";
            $fechaFin = $fechaFin."03";
        }else if($mes == "ABR"){
            $fechaInicio = $fechaInicio."04";
            $fechaFin = $fechaFin."04";
        }else if($mes == "MAY"){
            $fechaInicio = $fechaInicio."05";
            $fechaFin = $fechaFin."05";
        }else if($mes == "JUN"){
            $fechaInicio = $fechaInicio."06";
            $fechaFin = $fechaFin."06";
        }else if($mes == "JUL"){
            $fechaInicio = $fechaInicio."07";
            $fechaFin = $fechaFin."07";
        }else if($mes == "AGO"){
            $fechaInicio = $fechaInicio."08";
            $fechaFin = $fechaFin."08";
        }else if($mes == "SET"){
            $fechaInicio = $fechaInicio."09";
            $fechaFin = $fechaFin."09";
        }else if($mes == "OCT"){
            $fechaInicio = $fechaInicio."10";
            $fechaFin = $fechaFin."10";
        }else if($mes == "NOV"){
            $fechaInicio = $fechaInicio."11";
            $fechaFin = $fechaFin."11";
        }else if($mes == "DIC"){
            $fechaInicio = $fechaInicio."12";
            $fechaFin = $fechaFin."12";
        }

        // if($anio == "2021"){
        //     $fechaInicio = $fechaInicio."21";
        // }else if($anio == "2022"){
        //     $fechaInicio = $fechaInicio."22";
        // }

        return array(
            "fechaInicio" => $fechaInicio,
            "fechaFinal"  => $fechaFin,
        );

    }
}
