<?php

namespace App\Http\Controllers\Sistema\CargarArchivo\Clientes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\carcargasarchivos;
use App\usuusuarios;
use App\gsugrupossucursales;
use App\ussusuariossucursales;

class ActualizarGrupoSucursalController extends Controller
{
    public function ActualizarGrupoSucursal(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $fechaActual = date('Y-m-d H:i:s');

        $respuesta = true;
        $mensaje   = 'Los clientes se actualizaron correctamente!';
        $exito     = false;
        $log  = array(
            "NUEVO_GRUPO_CLIENTES" => [],
            "ACTUALIZACION_GRUPO_CLIENTES" => [],
            "NO_EXISTE_SUCURSAL" => [],
        );

        $archivo  = $_FILES['file']['name'];
        $usutoken = $request->header('api_token');

        $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid', 'usuusuario']);

        $fichero_subido = base_path().'/public/Sistema/cargaArchivos/clientes/actualizarGrupo/'.basename($usuusuario->usuid.'-'.$usuusuario->usuusuario.'-'.$fechaActual.'-'.$_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $fichero_subido)) {
            $objPHPExcel    = IOFactory::load($fichero_subido);
            $objPHPExcel->setActiveSheetIndex(0);
            $numRows        = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            $ultimaColumna  = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();

            for ($i=2; $i <= $numRows ; $i++) {
                $codSoldTo        = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                $grupoCliente     = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();

                $gsu = gsugrupossucursales::where('gsunombre', $grupoCliente)
                                        ->first();
                $gsuid = 0;
                if($gsu){
                    $gsuid = $gsu->gsuid;
                }else{
                    $gsun = new gsugrupossucursales;
                    $gsun->gsunombre = $grupoCliente;
                    if($gsun->save()){
                        $gsuid = $gsun->gsuid;
                        $log["NUEVO_GRUPO_CLIENTES"][] = $grupoCliente;
                    }
                }

                $uss = ussusuariossucursales::join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid' )
                                            ->join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                            ->where('ususoldto', $codSoldTo)
                                            ->first();

                if($uss){
                    if($uss->gsuid != $gsuid){
                        $suce = sucsucursales::find($uss->sucid);
                        $suce->gsuid = $gsuid;
                        $suce->update();
                        $log["ACTUALIZACION_GRUPO_CLIENTES"][] = $grupoCliente;
                    }
                }else{
                    $log["NO_EXISTE_SUCURSAL"][] = $codSoldTo;
                }

            }

        }else{
            $respuesta = false;
            $mensaje   = "Lo sentimos el archivo no se puede leer";
            $exito     = false;
        }

        $nuevoCargaArchivo = new carcargasarchivos;
        $nuevoCargaArchivo->tcaid            = 12; // Carga de actualizacion de grupos rebate para clientes
        $nuevoCargaArchivo->fecid            = null;
        $nuevoCargaArchivo->usuid            = $usuusuario->usuid;
        $nuevoCargaArchivo->carnombrearchivo = $archivo;
        $nuevoCargaArchivo->carubicacion     = $fichero_subido;
        $nuevoCargaArchivo->carexito         = $exito;
        if($nuevoCargaArchivo->save()){
            $pkid = "CAR-".$nuevoCargaArchivo->carid;
        }else{

        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "log"       => $log,
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            $usuusuario->usuid,
            null,
            $fichero_subido,
            $requestsalida,
            'CARGAR LA ACTUALIZACION DE GRUPOS DE CLIENTES (GRUPO DE CLIENTES) ',
            'IMPORTAR',
            '/cargarArchivo/clientes/actualizarGrupoSucursal', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
    }
}
