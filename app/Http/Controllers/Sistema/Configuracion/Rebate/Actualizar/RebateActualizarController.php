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

class RebateActualizarController extends Controller
{
    /**
     * Actualiza el valorizado rebate en tsu(por tipo de promicion) y sca(por categoria)
     */
    public function ActualizarValorizadoRebateFecha(Request $request)
    {
        $fecid      = $request['fecha']; 
        $usutoken   = $request->header('api_token');

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $mensajeDetalle = '';
        $mensajedev     = null;
        $log           = [];

        

        try{
            $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                    ->where('tsu.fecid', $fecid)
                                    ->where('scasucursalescategorias.tsuid', '!=', null)
                                    ->get([
                                        'scasucursalescategorias.scaid',
                                        'scasucursalescategorias.sucid',
                                        'scasucursalescategorias.scavalorizadoobjetivo',
                                        'scasucursalescategorias.scavalorizadoreal', 
                                        'scasucursalescategorias.scavalorizadorebate',
                                        'scasucursalescategorias.scaporcentajecumplimiento',
                                        'scasucursalescategorias.catid',
                                        'tsu.treid',
                                        'tsu.tprid'
                                    ]);

            if(sizeof($scas) > 0){
                foreach($scas as $sca){

                    if($sca->scavalorizadoobjetivo == 0){
                        $cumplimientoSca = $sca->scavalorizadoreal;
                    }else{
                        $cumplimientoSca = (($sca->scavalorizadoreal*100)/$sca->scavalorizadoobjetivo);
                    }
    
                    if($cumplimientoSca != $sca->scaporcentajecumplimiento){
                        $scaf = scasucursalescategorias::find($sca->scaid);
                        $scaf->scaporcentajecumplimiento = $cumplimientoSca;
                        if($scaf->update()){
                            $log[] = "El porcentaje de cumplimiento del SCA-".$sca->scaid." se actualizo correctamente";
                        }else{
                            $log[] = "No se pudo actualizar el porcentaje de cumplimiento del SCA-".$sca->scaid;
                        }
                    }else{
                        $log[] = "El cumplimiento de sca es igual al nuevo porcentaje obtenido, por ende no se actualiza en la tabla sca";
                    }
    
                    $trr = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                            ->where('trrtiposrebatesrebates.treid', $sca->treid)
                                            ->where('rtp.fecid', $fecid)
                                            ->where('tprid', $sca->tprid)
                                            ->where('rtp.rtpporcentajedesde', '<=', round($cumplimientoSca))
                                            ->where('rtp.rtpporcentajehasta', '>=', round($cumplimientoSca))
                                            ->where('trrtiposrebatesrebates.catid', $sca->catid)
                                            ->first([
                                                'rtp.rtpporcentajedesde',
                                                'rtp.rtpporcentajehasta',
                                                'rtp.rtpporcentajerebate'
                                            ]);
    
                    $totalRebate = 0;
                    if($trr){
                        $totalRebate = $sca->scavalorizadoreal * $trr->rtpporcentajerebate;
                    }else{
                        $log[] = "El cumplimiento no entra en el rango, o no existe una rebate para esa categoria o grupo en dicha fecha asignada";
                    }
    
                    if($sca->scavalorizadorebate != $totalRebate){
                        $scaf = scasucursalescategorias::find($sca->scaid);
                        $scaf->scavalorizadorebate = $totalRebate;
                        if($scaf->update()){
                            $log[] = "Se actualizo correctamente el valorizado rebate: SCA-".$sca->scaid;
                        }else{
                            $log[] = "No se pudo actualizar el valorizado rebate: SCA-".$sca->scaid;
                        }
                    }else{
                        $log[] = "El nuevo valorizado rebate del sca es el mismo que se tenia anteriormente, por ende no se actualiza en la tabla sca";
                    }
                }

                $tsus = tsutipospromocionessucursales::
                where('fecid', $fecid)
                ->get([
                    'tsuid',
                    'fecid',
                    'sucid',
                    'tsuvalorizadorebate'
                ]);
                
                foreach($tsus as $tsu){
                    $scasum = scasucursalescategorias::where('tsuid', $tsu->tsuid)
                                                    ->sum('scavalorizadorebate');
    
                    if($scasum != $tsu->tsuvalorizadorebate){
                        $tsu = tsutipospromocionessucursales::find($tsu->tsuid);
                        $tsu->tsuvalorizadorebate = $scasum;
                        if($tsu->update()){
    
                        }else{
    
                        }
                    }else{
                        // $log[] = "La suma de valorizado rebate de los sca que tiene el tsu: TSU-".$tsu->tsuid." es el mismo que se tenia anteriormente en la tabla tsu por ende no se actualiza";
                    }
                }
            }else{
                $log[] = "No se encontraron scas registrados";
            }

            

            $respuesta = true;
            $mensaje = "El valorizado rebate se actualizo correctamente";

            

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
