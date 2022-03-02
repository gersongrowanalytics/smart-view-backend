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

class ConvertirExcelController extends Controller
{
    public function ConvertirExcel(Request $request)
    {

        $abc = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        $re_data = $request['data'];
        $re_titulo = $request['titulo'];

        $re_data_columnas = $re_data[0]['columns'];
        $re_data_cuerpos  = $re_data[0]['data'];


        // 

        $documento = new Spreadsheet();
        $hoja = $documento->getActiveSheet();
        $hoja->setTitle($re_titulo);
        

        foreach($re_data_columnas as $posicionColumna => $re_data_columna){

            $hoja->setCellValue($abc[$posicionColumna]."1", $re_data_columna['title']);

            $hoja->getStyle($abc[$posicionColumna]."1")->getFont()->getColor()->setARGB($re_data_columna['style']['font']['color']['rgb']);
            $hoja->getStyle($abc[$posicionColumna]."1")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($re_data_columna['style']['fill']['fgColor']['rgb']);
        }

        foreach($re_data_cuerpos as $posicionCuerpo => $re_data_cuerpo){

            $numeroFila = $posicionCuerpo + 2;

            foreach($re_data_cuerpo as $posicionReData => $re_data){

                $hoja->setCellValue($abc[$posicionReData].$numeroFila, $re_data['value']);
            }

        }

        $worksheet1 = $documento->createSheet();
        $worksheet1->setTitle('Another sheet')
                    ->getTabColor()->setRGB('FF0000');

        $fileNameExcel="test PROMOS.xlsx";
        $writer = new Xlsx($documento);
        $writer->save($fileNameExcel);

    }
}
