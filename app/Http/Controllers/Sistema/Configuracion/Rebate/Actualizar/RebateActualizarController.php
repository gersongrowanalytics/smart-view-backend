<?php

namespace App\Http\Controllers\Sistema\Configuracion\Rebate\Actualizar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuditoriaController;
use App\scasucursalescategorias;
use App\trrtiposrebatesrebates;
use App\tretiposrebates;
use App\tsutipospromocionessucursales;
use App\sucsucursales;

class RebateActualizarController extends Controller
{
    /**
     * Actualiza el valorizado rebate en tsu(por tipo de promicion) y sca(por categoria)
     */

    public function ActualizarValorizadoRebateFecha(Request $request)
    {
        $fecid      = $request['fecha']; 
        $usutoken   = $request->header('api_token');

        $respuesta      = true;
        $mensaje        = '';
        $datos          = [];
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log            = ["escala" => ["entra" => [], "noentra" => [], "notienegrupoasignado" => [], "notieneescalas" => [] ]];

        try{

            // LIMPIAR REBATE 
            $tsuu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                ->update([
                                                    'tsuvalorizadorebate' => 0,
                                                ]);

            $tsus = tsutipospromocionessucursales::leftjoin('tretiposrebates as tre', 'tre.treid', 'tsutipospromocionessucursales.treid')
                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                            ->join('tprtipospromociones as tpr', 'tpr.tprid', 'tsutipospromocionessucursales.tprid')
                                            ->where('fecid', $fecid)
                                            ->get([
                                                'tsuid',
                                                'tre.treid',
                                                'tre.trenombre',
                                                'tpr.tprid',
                                                'tpr.tprnombre',
                                                'tsuporcentajecumplimiento',
                                                'suc.sucid',
                                                'suc.sucnombre'
                                            ]);

            foreach($tsus as $tsu){

                $treidSeleccionado = 0;
                $trenombre = "";

                if(!isset($tsu->treid)){
                    $suc = sucsucursales::leftjoin('tretiposrebates as tre', 'tre.treid', 'sucsucursales.treid')
                                        ->where('sucid', $tsu->sucid)
                                        ->first([
                                            'tre.treid',
                                            'tre.trenombre',
                                        ]);
                    $treid = 0;
                    if($suc){
                        $treid = $suc->treid;

                        $tsuu = tsutipospromocionessucursales::find($tsu->tsuid);
                        $tsuu->treid = $treid;
                        $tsuu->update();

                        $treidSeleccionado = $treid;
                        $trenombre = $suc->trenombre;
                    }

                }else{
                    $treidSeleccionado = $tsu->treid;
                    $trenombre = $tsu->trenombre;
                }

                $tsu->tsuporcentajecumplimiento = intval(round($tsu->tsuporcentajecumplimiento));

                $trrs = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                                ->where('trrtiposrebatesrebates.treid', $treidSeleccionado)
                                                ->where('rtp.fecid', $fecid)
                                                ->where('rtp.tprid', $tsu->tprid)
                                                ->where('rtp.rtpporcentajedesde', '<=', $tsu->tsuporcentajecumplimiento)
                                                ->where('rtp.rtpporcentajehasta', '>=', $tsu->tsuporcentajecumplimiento)
                                                ->get([
                                                    'trrtiposrebatesrebates.trrid',
                                                    'rtp.rtpporcentajedesde',
                                                    'rtp.rtpporcentajehasta',
                                                    'rtp.rtpporcentajerebate',
                                                    'trrtiposrebatesrebates.catid'
                                                ]);

                if(sizeof($trrs) > 0){
                    
                    if(sizeof($trrs) <= 5){
                        $totalRebate = 0;
                        foreach($trrs as $posicion => $trr){
                            if($posicion == 0){
                                $log['escala']['entra'][] = "Si entra en la escala rebate: ".$tsu->tsuid." de la sucursal: ".$tsu->sucid." con un cumplimiento de: ".round($tsu->tsuporcentajecumplimiento)." y escalas desde: ".$trr->rtpporcentajedesde." y hasta: ".$trr->rtpporcentajehasta;
                            }
                            $sca = scasucursalescategorias::where('tsuid', $tsu->tsuid)
                                                        ->where('fecid', $fecid)
                                                        ->where('catid', $trr->catid)
                                                        ->first([
                                                            'scaid',
                                                            'scavalorizadoreal'
                                                        ]);

                            if($sca){
                                $nuevoRebate = ($sca->scavalorizadoreal*$trr->rtpporcentajerebate)/100;
                                $totalRebate = $totalRebate + $nuevoRebate;
                            }else{

                            }

                        }

                        $tsuu = tsutipospromocionessucursales::find($tsu->tsuid);
                        $tsuu->tsuvalorizadorebate = $totalRebate;
                        $tsuu->update();


                    }else{
                        // echo "Hay mas de 5 datos: ";
                        foreach($trrs as $trr){
                            echo $trr->trrid;
                        }
                    }
                }else{
                    $log['escala']['noentra'][] = "No entra en la escala rebate: ".$tsu->tsuid." de la sucursal: ".$tsu->sucnombre."(".$tsu->sucid.") con el grupo: ".$trenombre;
                }

                $trr = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                                ->where('trrtiposrebatesrebates.treid', $treidSeleccionado)
                                                ->where('rtp.fecid', $fecid)
                                                ->where('rtp.tprid', $tsu->tprid)
                                                ->first([
                                                    'trrtiposrebatesrebates.trrid',
                                                    'rtp.rtpporcentajedesde',
                                                    'rtp.rtpporcentajehasta',
                                                    'rtp.rtpporcentajerebate',
                                                    'trrtiposrebatesrebates.catid'
                                                ]);

                if(!$trr){
                    $log['escala']['notieneescalas'][] = "No tiene escala asignada: ".$tsu->tsuid." de la sucursal: ".$tsu->sucnombre."(".$tsu->sucid.") con el grupo: ".$trenombre." en ".$tsu->tprnombre;
                }
            }

            $tsusnull = tsutipospromocionessucursales::leftjoin('tretiposrebates as tre', 'tre.treid', 'tsutipospromocionessucursales.treid')
                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                            ->where('fecid', $fecid)
                                            ->where('tsutipospromocionessucursales.treid', null)
                                            ->get([
                                                'tsuid',
                                                'tre.treid',
                                                'tre.trenombre',
                                                'tprid',
                                                'tsuporcentajecumplimiento',
                                                'suc.sucid',
                                                'suc.sucnombre'
                                            ]);

            foreach($tsusnull as $tsunull){

                $log['escala']['notienegrupoasignado'][] = "No tiene grupo asignado: ".$tsunull->tsuid." de la sucursal: ".$tsunull->sucnombre."(".$tsunull->sucid.") con el grupo: ".$tsunull->trenombre;
            }



        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev,
            "logs"           => $log
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            null,
            $request,
            $requestsalida,
            'ACTUALIZAR EL VALORIZADO REBATE EN LOS SCA Y TSU',
            'ACTUALIZAR',
            '/configuracion/rebate/actualizar/Rebate', //ruta
            '',
            $log
        );
        
        return $requestsalida;

        
    }

    
}
