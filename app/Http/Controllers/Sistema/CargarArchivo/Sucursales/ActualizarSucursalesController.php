<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Sucursales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\fecfechas;
use App\tputiposusuarios;
use App\perpersonas;
use App\usuusuarios;
use App\ussusuariossucursales;
use App\sucsucursales;
use App\carcargasarchivos;
use App\cejclientesejecutivos;
use App\zonzonas;
use App\tsutipospromocionessucursales;
use App\tretiposrebates;
use App\trrtiposrebatesrebates;
use App\scasucursalescategorias;
use App\cascanalessucursales;

class ActualizarSucursalesController extends Controller
{
    public function ActualizarSucursales(Request $request)
    {
        $logs = array(
            "ERRORES"   => "",
            "NO_SE_SUBIO_ARCHIVO"   => "",
            "NO_EXISTE_CANAL"   => [],
            "NO_EXISTE_ZONA"    => [],
            "NUEVAS_SUCURSALES" => [],
            "EDITAR_SUCNOMBRE"  => [],
            "EDITAR_TREID"      => [],
            "EDITAR_ZONID"      => [],
            "EDITAR_CASID"      => []
        );

        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $usutoken = $request->header('api_token');
        $archivo  = $_FILES['file']['name'];

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);
        $fichero_subido = '';

        try{

            $fichero_subido = base_path().'/public/Sistema/cargaArchivos/clientes/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {

                $objPHPExcel    = IOFactory::load($fichero_subido);
                $objPHPExcel->setActiveSheetIndex(0);
                $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
                $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

                for ($i=2; $i <= $numRows ; $i++) {
                    $soldto        = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                    $sucursal      = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                    $customerGroup = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                    $zona          = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                    $canal         = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();

                    $cas = cascanalessucursales::where('casnombre', $canal)->first();

                    if($cas){
                        $zon = zonzonas::where('zonnombre', $zona)->first();
                        if($zon){

                            $tre = tretiposrebates::where('trenombre', $customerGroup)->first();
                            $treid = 0;
                            if($tre){
                                $treid = $tre->treid;
                            }else{
                                $tren = new tretiposrebates;
                                $tren->trenombre = $customerGroup;
                                $tren->save();

                                $treid = $tren->treid;
                            }

                            $suc = sucsucursales::where('sucsoldto', $soldto)->first();
                            if($suc){
                                $sucnombre = $suc->sucnombre;
                                if($suc->sucnombre != $sucursal){
                                    $logs["EDITAR_SUCNOMBRE"][] = "SOLDTO: ".$soldto." SUCNOMBRE: ".$sucnombre." ANTERIOR: ".$suc->sucnombre." NUEVA: ".$sucursal;
                                    $suc->sucnombre = $sucursal;
                                }

                                if($suc->treid != $treid){
                                    $logs["EDITAR_TREID"][] = "SOLDTO: ".$soldto." SUCNOMBRE: ".$sucnombre." ANTERIOR: ".$suc->treid." NUEVA: ".$treid;
                                    $suc->treid = $treid;
                                }

                                if($suc->zonid != $zon->zonid){
                                    $logs["EDITAR_ZONID"][] = "SOLDTO: ".$soldto." SUCNOMBRE: ".$sucnombre." ANTERIOR: ".$suc->zonid." NUEVA: ".$zon->zonid;
                                    $suc->zonid = $zon->zonid;
                                }

                                if($suc->casid != $cas->casid){
                                    $logs["EDITAR_CASID"][] = "SOLDTO: ".$soldto." SUCNOMBRE: ".$sucnombre." ANTERIOR: ".$suc->casid." NUEVA: ".$cas->casid;
                                    $suc->casid = $cas->casid;
                                }
                                
                                $suc->update();
                            }else{
                                $sucn = new sucsucursales;
                                $sucn->sucsoldto = $soldto;
                                $sucn->sucnombre = $sucursal;
                                $sucn->treid = $treid;
                                $sucn->zonid = $zon->zonid;
                                $sucn->casid = $cas->casid;
                                $sucn->save();

                                $logs["NUEVAS_SUCURSALES"][] = "SUCID: ".$sucn->sucid." SOLDTO: ".$soldto." SUCURSAL: ".$sucursal;
                            }

                        }else{
                            $logs["NO_EXISTE_ZONA"][] = $zona;
                            $respuesta = false;
                            $mensaje = "NO SE ENCONTRO UNA ZONA";
                        }
                    }else{
                        $logs["NO_EXISTE_CANAL"][] = $canal;
                        $respuesta = false;
                        $mensaje = "NO SE ENCONTRO UN CANAL";
                    }
                }

            }else{
                $logs["NO_SE_SUBIO_ARCHIVO"] = "No se pudo subir el archivo";
                $respuesta = false;
                $mensaje = "NO SE PUDO GUARDAR EL FILE";
            }

        } catch (Exception $e) {
            $logs["ERRORES"] = $e->getMessage();
            $mensajedev = $e->getMessage();
            $respuesta = false;
            $mensaje = "ERROR EN EL SERVIDOR";
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "mensajedev"     => $mensajedev,
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR DATA DE SUCURSALES AL SISTEMA ',
            'IMPORTAR',
            '/cargarArchivo/sucursales', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;


    }
}
