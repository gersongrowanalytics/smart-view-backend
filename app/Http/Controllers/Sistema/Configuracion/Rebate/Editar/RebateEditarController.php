<?php

namespace App\Http\Controllers\Sistema\Configuracion\Rebate\Editar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\fecfechas;
use App\rtprebatetipospromociones;
use App\trrtiposrebatesrebates;
use Illuminate\Support\Facades\DB;

class RebateEditarController extends Controller
{
    public function RebateEditar(Request $request)
    {
        $rtpid  = $request['rtpid'];
        $anio   = $request['fecano'];
        $mes    = $request['fecmes'];
        $dia    = '01';
        $treid  = $request['treid'];
        $trrid  = $request['trrid'];
        $grupo  = $request['trenombre'];
        $tprid  = $request['tprid'];
        $desde  = $request['rtpporcentajedesde'];
        $hasta  = $request['rtpporcentajehasta'];
        $rebate = $request['rtpporcentajerebate'];

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        $logs           = [];
        $usutoken       = $request->header('api_token');

        DB::beginTransaction(); 

        try{
            
            $fecfecha = fecfechas::where('fecdia', $dia)
                            ->where('fecmes', $mes)
                            ->where('fecano', $anio)
                            ->first(['fecid']);

            $fecid = 0;
            if($fecfecha){
                $logs[] = "La fecha existe";
                $fecid = $fecfecha->fecid;
            }else{
                $logs[] = "No existe la fecha";
                $nuevaFecha = new fecfechas;
                $nuevaFecha->fecmes = $mes;
                $nuevaFecha->fecano = $anio;
                $nuevaFecha->fecdia = $dia;
                if($nuevaFecha->save()){
                    $logs[] = "La fecha se agrego";
                }else{
                    $logs[] = "La fecha no se pudo agregar";
                }
            }

            $rtp = rtprebatetipospromociones::find($rtpid);
            $rtp->fecid               = $fecid;
            $rtp->tprid               = $tprid;
            $rtp->rtpporcentajedesde  = $desde;
            $rtp->rtpporcentajehasta  = $hasta;
            $rtp->rtpporcentajerebate = $rebate;
            if($rtp->update()){
                $logs[] = "El rebate se actualizo";
            }else{
                $logs[] = "No se actualizo el rebate";
            }

            $trr = trrtiposrebatesrebates::find($trrid);
            $trr->treid = $treid;
            $trr->rtpid = $rtpid;
            if($trr->update()){
                $logs[] = "El grupo rebate se actualizo";
            }else{
                $logs[] = "No se actualizo el grupo del rebate";
            }

            





            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            $request['ip'],
            $request,
            $requestsalida,
            'EDITAR REBATE',
            'EDITAR',
            '', //ruta
            null
        );
        
        return $requestsalida;
    }
}
