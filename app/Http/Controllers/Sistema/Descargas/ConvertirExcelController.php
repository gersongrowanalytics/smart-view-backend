<?php

namespace App\Http\Controllers\Sistema\Descargas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Mail\CorreConArchivos;
use App\Mail\CorreConArchivosPdf;
use Illuminate\Support\Facades\Mail;

class ConvertirExcelController extends Controller
{
    public function ConvertirExcel(Request $request)
    {

        $abc = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        $re_data = $request['data'];
        $re_nombreexcel = $request['nombreexcel'];
        $re_titulo = $request['titulo'];

        $re_data_columnas = $re_data[0]['columns'];
        $re_data_cuerpos  = $re_data[0]['data'];


        // 

        $documento = new Spreadsheet();
        $hoja = $documento->getActiveSheet();
        $hoja->setTitle($re_titulo);
        

        foreach($re_data_columnas as $posicionColumna => $re_data_columna){

            $hoja->setCellValue($abc[$posicionColumna]."1", $re_data_columna['title']);

            if(isset($re_data_columna['style'])){
                if(isset($re_data_columna['style']['font'])){
                    if(isset($re_data_columna['style']['font']['color'])){
                        if(isset($re_data_columna['style']['font']['color']['rgb'])){
                            $hoja->getStyle($abc[$posicionColumna]."1")->getFont()->getColor()->setARGB($re_data_columna['style']['font']['color']['rgb']);                
                        }

                    }

                }

                if(isset($re_data_columna['style']['fill'])){
                    if(isset($re_data_columna['style']['fill']['fgColor'])){
                        if(isset($re_data_columna['style']['fill']['fgColor']['rgb'])){
                            $hoja->getStyle($abc[$posicionColumna]."1")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($re_data_columna['style']['fill']['fgColor']['rgb']);
                        }

                    }

                }


            }

            // $hoja->getStyle($abc[$posicionColumna]."1")->getFont()->getColor()->setARGB($re_data_columna['style']['font']['color']['rgb']);
        }

        foreach($re_data_cuerpos as $posicionCuerpo => $re_data_cuerpo){

            $numeroFila = $posicionCuerpo + 2;

            foreach($re_data_cuerpo as $posicionReData => $re_data){

                $hoja->setCellValue($abc[$posicionReData].$numeroFila, $re_data['value']);

                if(isset($re_data['style'])){

                    if(isset($re_data['style']['font'])){
                        if(isset($re_data['style']['font']['sz'])){
                            $hoja->getStyle($abc[$posicionReData].$numeroFila)
                                    ->getFont()
                                    ->setSize($re_data['style']['font']['sz'])
                                    ->setName('Arial');
                        }
                    }

                    if(isset($re_data['style']['numFmt'])){
                        if($re_data['style']['numFmt'] == "#,##0.00"){
                            $hoja->getStyle($abc[$posicionReData].$numeroFila)->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);   
                        }
                    }
                }
            }

        }

        $codigoArchivoAleatorio = mt_rand(1, mt_getrandmax())/mt_getrandmax();
        $codigoArchivoAleatorio = substr($codigoArchivoAleatorio, -4);

        $fileNameExcel=$re_nombreexcel."-".$codigoArchivoAleatorio.".xlsx";
        $writer = new Xlsx($documento);
        $writer->save("Sistema/ExcelCorreo/".$fileNameExcel);

        return response()->json([
            'ubicacion' => '/Sistema/ExcelCorreo/'.$fileNameExcel,
            'excel' => $fileNameExcel 
        ]);

    }

    public function EnviarCorreo(Request $request)
    {
        $usutoken = $request->header('api_token');

        $asunto       = $request['asunto'];
        $destinatario = $request['destinatario'];
        $mensaje      = $request['mensaje'];
        $excel        = $request['excel'];
        $re_espdf     = $request['espdf'];

        $mensaje = str_replace('\n', "<br>", $mensaje);

        if($re_espdf == true){
            
            $excel = $usutoken.".pdf";
            Mail::to($destinatario)->send(new CorreConArchivosPdf($mensaje, $asunto, $excel));

        }else{
            Mail::to($destinatario)->send(new CorreConArchivos($mensaje, $asunto, $excel));

        }        
    }

}
