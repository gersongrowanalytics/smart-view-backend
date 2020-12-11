<?php

namespace App\Http\Controllers\Sistema\Trimestre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuditoriaController;
use App\fecfechas;
use App\catcategorias;
use App\ttrtritre;

class CrearTrimestreController extends Controller
{
    public function CrearTrimestre(Request $request)
    {
        $usutoken   = $request->header('api_token');

        $pkid = 0;
        $log = array(
            "fechas" => [],
            "servidor" => []
        );
        $respuesta      = true;
        $mensaje        = "El rebate trimestral fue agregado satisfactoriamente";
        $datos          = [];
        $mensajeDetalle = "";
        $mensajedev     = "";

        
        $fecha  = $request['fecha'];
        $triid  = $request['triid'];
        $tresid = $request['tresid'];
        $catsid = $request['catsid'];
        $tprid  = $request['tprid'];
        $desde  = $request['desde'];
        $hasta  = $request['hasta'];
        $rebate = $request['rebate'];

        $todasCategorias = $request['todasCategorias'];

        DB::beginTransaction();
        try{
            $fecha = new \DateTime(date("Y-m-d", strtotime($fecha)));

            $fecfecha = fecfechas::where('fecfecha', $fecha)->first(['fecid']);
            $fecid = 0;
            if($fecfecha){
                $fecid = $fecfecha->fecid;
                $log["fechas"][] = "Existe la fecha";
            }else{
                $log["fechas"][] = "No existe la fecha";
                $nuevafecha = new fecfechas;
                $nuevafecha->fecfecha = $fecha;
                $nuevafecha->fecdia   = '';
                $nuevafecha->fecmes   = '';
                $nuevafecha->fecano   = '';
                if($nuevafecha->save()){
                    $fecid = $nuevafecha->fecid;

                    $pkid = "FEC-".$fecid." ";
                    $log["fechas"][] = "Se agrego la fecha";
                }else{
                    $pkid = "";
                    $log["fechas"][] = "No se pudo agregar la fecha";
                }
            }

            if($todasCategorias == true){

                $cats = catcategorias::where('catid', '!=', 6)
                                        ->get(['catid']);

                foreach($tresid as $treid){

                    foreach($cats as $cat){
                        $ttrn = new ttrtritre;
                        $ttrn->fecid = $fecid;
                        $ttrn->triid = $triid;
                        $ttrn->treid = $treid;
                        $ttrn->catid = $cat->catid;
                        $ttrn->tprid = $tprid;
                        $ttrn->ttrporcentajedesde  = $desde;
                        $ttrn->ttrporcentajehasta  = $hasta;
                        $ttrn->ttrporcentajerebate = $rebate;
                        $ttrn->save();
                    }
                }

            }else{

                foreach($tresid as $treid){

                    foreach($catsid as $catid){
                        $ttrn = new ttrtritre;
                        $ttrn->fecid = $fecid;
                        $ttrn->triid = $triid;
                        $ttrn->treid = $treid;
                        $ttrn->catid = $catid;
                        $ttrn->tprid = $tprid;
                        $ttrn->ttrporcentajedesde  = $desde;
                        $ttrn->ttrporcentajehasta  = $hasta;
                        $ttrn->ttrporcentajerebate = $rebate;
                        $ttrn->save();
                    }
                }


            }

            DB::commit();
            
        } catch (Exception $e) {
            DB::rollBack();
            $respuesta  = false;
            $mensajedev = $e->getMessage();
            $mensaje    = "Lo sentimos, ocurrio un error al momento de crear el rebate trimestral";
            $log["servidor"][] = "ERROR DE SERVIDOR: ".$mensajedev;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "fecid"          => $fecid
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            null,
            $request,
            $requestsalida,
            'Agregar un nuevo registro de rebate trimestral',
            'AGREGAR',
            '', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;
        
    }
}
