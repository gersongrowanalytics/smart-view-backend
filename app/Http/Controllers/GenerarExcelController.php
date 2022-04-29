<?php

namespace App\Http\Controllers;

use App\audauditorias;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerarExcelController extends Controller
{
    public function GenerarExcel (Request $request) 
    {      
        // $re_fechaInicio = $request['re_fechaInicio'];
        // $re_fechaFinal  = $request['re_fechaFinal'];
        $re_fechaInicio = "2021-07-01";
        $re_fechaFinal  = "2021-07-30";

        $aud = audauditorias::join('usuusuarios as usu', 'usu.usuid','audauditorias.usuid')
                        ->join('perpersonas as per', 'per.perid', 'usu.perid')
                        ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usu.tpuid')
                        ->orderBy('audauditorias.audid', 'DESC')
                        ->where('audauditorias.audaccion', "EDITAR")
                        ->where('usu.usuid', '!=', 1)
                        ->where('usu.tpuid', '!=', 1)
                        ->whereBetween('audauditorias.created_at', [$re_fechaInicio, $re_fechaFinal])
                        ->get([
                            'per.pernombrecompleto',
                            'usu.usuusuario',
                            'tpu.tpunombre',
                            'audauditorias.created_at'
                        ]);
                        
        $documento = new Spreadsheet();
        $hoja = $documento->getActiveSheet();
        $hoja->setTitle('Auditoria');
        $hoja->setCellValue("A1","pernombrecompleto");
        $hoja->setCellValue("B1","usuusuario");
        $hoja->setCellValue("C1","tpunombre");
        $hoja->setCellValue("D1","created_at");
        foreach ($aud as $pos =>$auditoria) {
            $posicion = $pos +2;
            $hoja->setCellValue("A".$posicion, $auditoria->pernombrecompleto);
            $hoja->setCellValue("B".$posicion, $auditoria->usuusuario);
            $hoja->setCellValue("C".$posicion, $auditoria->tpunombre);
            $hoja->setCellValue("D".$posicion, $auditoria->created_at);
        }
        
        $fileNameExcel = "auditoria.xlsx";
        $writer = new Xlsx($documento);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileNameExcel).'"');
        $writer->save('php://output');
    }
}
