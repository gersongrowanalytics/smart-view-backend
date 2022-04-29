<?php

namespace App\Http\Controllers;

use App\audauditorias;
use App\ussusuariossucursales;
use App\usuusuarios;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerarExcelController extends Controller
{
    public function GenerarExcelAuditoriaLogin (Request $request) 
    {      
        $re_fechaInicio = $request['re_fechaInicio'];
        $re_fechaFinal  = $request['re_fechaFinal'];
        // $re_fechaInicio = "2021-07-01";
        // $re_fechaFinal  = "2021-07-30";

        $aud = audauditorias::join('usuusuarios as usu', 'usu.usuid','audauditorias.usuid')
                        ->join('perpersonas as per', 'per.perid', 'usu.perid')
                        ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usu.tpuid')
                        ->orderBy('audauditorias.audid', 'DESC')
                        ->where('audauditorias.audaccion', "LOGIN")
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

    public function GenerarExcelUsuario (Request $request) 
    {      
        // $re_fechaInicio = $request['re_fechaInicio'];
        // $re_fechaFinal  = $request['re_fechaFinal'];
        $re_fechaInicio = "2019-11-03";
        $re_fechaFinal  = "2022-12-30";

        $usu = usuusuarios::join('perpersonas as per', 'per.perid', 'usuusuarios.perid')
                        ->join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                        ->orderBy('usuusuarios.usuid', 'DESC')
                        ->where('usuusuarios.usuid', '!=', 1)
                        ->where('usuusuarios.tpuid', '!=', 1)
                        ->whereNotNull('usuusuario')
                        ->where('usuusuario','not like','%prueba%')
                        ->where('usuusuario','not like','%grow%')
                        ->where('usuusuario','not like','%eunice%')
                        ->where('usuusuario','not like','%gerson%')
                        ->where('usuusuario','not like','%usuario%')
                        ->whereBetween('usuusuarios.created_at', [$re_fechaInicio, $re_fechaFinal])
                        ->get([
                            'usuusuarios.usuid',
                            'per.pernombrecompleto',
                            'tpu.tpunombre',
                            'usuusuarios.usuusuario'
                        ]);

        
        foreach ($usu as $key => $usuario) {
            $uss = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                            ->where('ussusuariossucursales.usuid', $usuario->usuid)
                                            ->get(['ussusuariossucursales.ussid','suc.sucnombre']);

            if ($uss) {
                $sucursalesUsuario = "";
                foreach ($uss as $key => $sucursal) {
                    $sucursalesUsuario .= $sucursal['sucnombre'].",";
                }
                $usuario['distribuidoras'] = $sucursalesUsuario;
            }   
        }
      
        $documento = new Spreadsheet();
        $hoja = $documento->getActiveSheet();
        $hoja->setTitle('Usuarios');
        $hoja->setCellValue("A1","pernombrecompleto");
        $hoja->setCellValue("B1","tpunombre");
        $hoja->setCellValue("C1","usuusuario");
        $hoja->setCellValue("D1","distribuidoras");
        foreach ($usu as $pos =>$usuario) {
            $posicion = $pos +2;
            $hoja->setCellValue("A".$posicion, $usuario->pernombrecompleto);
            $hoja->setCellValue("B".$posicion, $usuario->tpunombre);
            $hoja->setCellValue("C".$posicion, $usuario->usuusuario);
            $hoja->setCellValue("D".$posicion, $usuario->distribuidoras);
        }
        
        $fileNameExcel = "Usuarios.xlsx";
        $writer = new Xlsx($documento);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileNameExcel).'"');
        $writer->save('php://output');
    }
    //usuario no bettween
}
