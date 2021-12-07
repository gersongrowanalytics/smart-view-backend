<?php

namespace App\Http\Controllers\Sistema\Promociones\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use iio\libmergepdf\Merger;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfPromocionesController extends Controller
{
    public function MostrarPdfPromociones(Request $request)
    {

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
            $pdf = app('dompdf.wrapper');
            $pdf->setPaper('A4', 'portrait');
            $pdf->loadView('pdf.promociones.indice', ["data" => $dataCategoria, "posicion" => $posicionDataCategoria]);
            $m->addRaw($pdf->output());

            $numeroPdfsAbajo = $dataCategoria['cantidadPromociones'] / 3;
            $numeroPdfsAbajo = ceil($numeroPdfsAbajo);

            for($i = 0; $i < $numeroPdfsAbajo; $i++ ){
                if($i == 0){
                    $pdf2 = app('dompdf.wrapper');
                    $pdf2->setPaper('A3','landscape');
                    $pdf2->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 0, "hasta" => 2 ] );
                    $m->addRaw($pdf2->output());
                }else if($i == 1){
                    $pdf3 = app('dompdf.wrapper');
                    $pdf3->setPaper('A3','landscape');
                    $pdf3->loadView('pdf.promociones.promocion', ["data" => $dataCategoria['canales'], "categoria" => $dataCategoria, "desde" => 3, "hasta" => 5 ] );
                    $m->addRaw($pdf3->output());
                }
            }

        }


        file_put_contents('Pdf-123afas123.pdf', $m->merge());
        
        // return $m->stream();
        return true;
        // return $pdf2->stream();
        // dd($resultPromociones);
    }
}