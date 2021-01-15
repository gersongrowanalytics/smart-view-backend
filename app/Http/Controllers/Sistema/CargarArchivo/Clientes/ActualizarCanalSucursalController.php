<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Clientes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ActualizarCanalSucursalController extends Controller
{
    public function ActualizarCanalSucursal(Request $request)
    {
        $archivo = $_FILES['file']['name'];

        $fichero_subido = base_path().'/public/Sistema/cargaArchivos/clientes/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
            $objPHPExcel    = IOFactory::load($fichero_subido);
            $objPHPExcel->setActiveSheetIndex(0);
            $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

            for ($i=2; $i <= $numRows ; $i++) {
                $codSoldTo        = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                $soldTo           = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                $codEjecutivo     = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
            }

        }else{

        }

    }
}
