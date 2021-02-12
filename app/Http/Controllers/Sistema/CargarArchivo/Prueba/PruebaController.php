<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Prueba;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use App\carcargasarchivos;
use App\fecfechas;
use App\usuusuarios;
use App\perpersonas;
use App\ussusuariossucursales;
use App\sucsucursales;
use App\tsutipospromocionessucursales;
use App\scasucursalescategorias;
use App\rtprebatetipospromociones;
use App\proproductos;
use App\trrtiposrebatesrebates;
use App\tuptiposusuariospermisos;
use App\vsiventasssi;
use App\vsoventassso;

class PruebaController extends Controller
{
    public function prueba(Request $request)
    {
        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $numeroCelda    = 0;
        $usutoken       = $request->header('api_token');
        $archivo        = $_FILES['file']['name'];
        $skusNoExisten  = [];
        $soldtosNoExis  = [];
        $log            = array();
        $pkid           = 0;

        $cargarData = false;
        

        
        
        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/pruebas/'.basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
                $fecid = 0;

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
                
                if($cargarData == true){
                    for ($i=2; $i <= $numRows; $i++) {
                        $dia = '01';
    
                        $ano        = $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                        $mesTxt     = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                        $zona     = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
    
                        $soldto     = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                        $cliente    = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                        $sku        = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                        $producto   = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                        $sector     = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
                        $real       = $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
                        
                        $soldto = substr($soldto, 3);
            
                        // VERIFICAR SI EXISTE EL USUARIO
                        if($zona == "DTT2 PROVINCIAS"){
                            $suc = sucsucursales::where('sucsoldto', 'LIKE', "%".$soldto)
                                            ->first();

                            if($suc){
                                if($suc->casid == 2){
                                    $log["ESTA_EN_CAS"][] = "sucid: ". $suc->sucid." soldto: ".$suc->sucsoldto." nombre: ".$suc->sucnombre." linea: ".$i;
                                }else{
                                    $log["NO_ESTA_EN_CAS"][] = "sucid: ". $suc->sucid." soldto: ".$suc->sucsoldto." nombre: ".$suc->sucnombre." linea: ".$i;
                                }
                            }else{
                                $log["NO_EXISTE"][] = "soldto: ".$soldto." linea: ".$i;
                            }
                        }


                        
                    }
                }

               
            }else{
                $log["NO_SE_SUBIO"][] = "NO SE SUBIO EL ARCHIVO";
            }

        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
            // $log[]      = $mensajedev;
        }

        dd($log);
    }
}
