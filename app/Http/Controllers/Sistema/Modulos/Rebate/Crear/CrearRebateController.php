<?php

namespace App\Http\Controllers\Sistema\Modulos\Rebate\Crear;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
use App\rtprebatetipospromociones;
use App\fecfechas;
use App\trrtiposrebatesrebates;
use App\catcategorias;

use Illuminate\Support\Facades\DB;
use App\scasucursalescategorias;
use App\tretiposrebates;
use App\tsutipospromocionessucursales;
use App\sucsucursales;

class CrearRebateController extends Controller
{
    public function CrearRebate(Request $request)
    {

        $respuesta = true;
        $mensaje   = "Los rebate fueron creados satisfactoriamente";
        $usutoken  = $request->header('api_token');
        $pkid = array();
        $log  = array();

        $re_datas = $request['data'];
        $re_mes   = $request['mes'];
        $re_anio  = $request['anio'];
        $re_reiniciar  = $request['reiniciar'];

        $cats = catcategorias::where('catid', '<', 6)
                            ->get();

        $fec = fecfechas::where('fecmes', $re_mes)
                        ->where('fecano', $re_anio)
                        ->where('fecdia', '01')
                        ->first();

        

        if($fec){

            // REINICIAR RTP Y TRR

            if($re_reiniciar == true){
                $rtps = rtprebatetipospromociones::where('fecid', $fec->fecid)->get();

                foreach($rtps as $rtp){
                    
                    trrtiposrebatesrebates::where('rtpid', $rtp->rtpid)->delete();
                    rtprebatetipospromociones::where('rtpid', $rtp->rtpid)->delete();

                }
            }

            foreach($re_datas as $re_data){

                foreach($re_data['data'] as $data){

                    if(isset($data['editando'])){
                        if($data['editando'] == true){ 
        
                            foreach($cats as $cat){

                                $rtp = rtprebatetipospromociones::where('fecid', $fec->fecid)
                                                                ->where('tprid', $data['tprideditando'])
                                                                ->where('rtpporcentajedesde', $data['desdeeditando'])
                                                                ->where('rtpporcentajehasta', $data['hastaeditando'])
                                                                ->where('rtpporcentajerebate', $data['cat-'.$cat->catid])
                                                                ->first();
                                
                                $rtpid = 0;
            
                                if($rtp){
                                    $rtpid = $rtp->rtpid;
                                }else{
                                    $rtpn = new rtprebatetipospromociones;
                                    $rtpn->fecid = $fec->fecid;
                                    $rtpn->tprid = $data['tprideditando'];
                                    $rtpn->rtpporcentajedesde  = $data['desdeeditando'];
                                    $rtpn->rtpporcentajehasta  = $data['hastaeditando'];
                                    $rtpn->rtpporcentajerebate = $data['cat-'.$cat->catid];
                                    if($rtpn->save()){
                                        $rtpid = $rtpn->rtpid;
                                    }else{
                                        $respuesta = false;
                                        $mensaje = "Lo sentimos algunos rebates no fueron agregados";
                                    }
                                }

        
                                $trr = trrtiposrebatesrebates::where('treid', $data['treideditando'] )
                                                            ->where('rtpid', $rtpid)
                                                            ->where('catid', $cat->catid)
                                                            ->first();
        
                                if($trr){
        
                                }else{
                                    $trrn = new trrtiposrebatesrebates;
                                    $trrn->treid = $data['treideditando'];
                                    $trrn->rtpid = $rtpid;
                                    $trrn->catid = $cat->catid;
                                    if($trrn->save()){
        
                                    }else{
                                        $respuesta = false;
                                        $mensaje = "Lo sentimos algunos rebates no fueron agregados";
                                    }
                                }
        
                            }
        
            
                        }
                    }
                }
    
            }

            $this->actualizarValorizadoRebateFechaGet($fec->fecid, $usutoken);
            
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos el mes seleccionado no ha sido aperturado";
        }



        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
        ]);

        $AuditoriaController = new AuditoriaController;
        $registrarAuditoria  = $AuditoriaController->registrarAuditoria(
            $usutoken,
            null,
            null,
            $request,
            $requestsalida,
            'Agregar varios rebates',
            'AGREGAR',
            '/crear-varios-rebate', //ruta
            $pkid,
            $log
        );

        if($registrarAuditoria == true){

        }else{
            
        }
        
        return $requestsalida;

    }


    // use App\Http\Controllers\Sistema\Configuracion\Rebate\Actualizar\RebateActualizarController;

    public function actualizarValorizadoRebateFechaGet($fecid, $usutoken)
    {
        // $fecid      = $request['fecha']; 
        // $usutoken   = $request->header('api_token');

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
                                                    'tsuvalorizadorebateniv' => 0
                                                ]);
            
            // SELL IN
            $tsus = tsutipospromocionessucursales::leftjoin('tretiposrebates as tre', 'tre.treid', 'tsutipospromocionessucursales.treid')
                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                            ->join('tprtipospromociones as tpr', 'tpr.tprid', 'tsutipospromocionessucursales.tprid')
                                            ->where('fecid', $fecid)
                                            ->where('tpr.tprid', 1)
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

            // SELL OUT
            $tsus = tsutipospromocionessucursales::leftjoin('tretiposrebates as tre', 'tre.treid', 'tsutipospromocionessucursales.treid')
                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                            ->join('tprtipospromociones as tpr', 'tpr.tprid', 'tsutipospromocionessucursales.tprid')
                                            ->where('fecid', $fecid)
                                            ->where('tpr.tprid', 2)
                                            ->get([
                                                'tsuid',
                                                'tre.treid',
                                                'tre.trenombre',
                                                'tpr.tprid',
                                                'tpr.tprnombre',
                                                'tsuporcentajecumplimiento',
                                                'tsuporcentajecumplimientoniv',
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
                $tsu->tsuporcentajecumplimientoniv = intval(round($tsu->tsuporcentajecumplimientoniv));

                // REAL
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
                            $sca = scasucursalescategorias::join('tsutipospromocionessucursales  as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                        // ->where('tsuid', $tsu->tsuid)
                                                        ->where('tsu.sucid', $tsu->sucid)
                                                        ->where('tsu.tprid', 1)
                                                        ->where('scasucursalescategorias.fecid', $fecid)
                                                        ->where('scasucursalescategorias.catid', $trr->catid)
                                                        ->first([
                                                            'scaid',
                                                            'scavalorizadoreal',

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

                // NIV

                $trrs = trrtiposrebatesrebates::join('rtprebatetipospromociones as rtp', 'rtp.rtpid', 'trrtiposrebatesrebates.rtpid')
                                                ->where('trrtiposrebatesrebates.treid', $treidSeleccionado)
                                                ->where('rtp.fecid', $fecid)
                                                ->where('rtp.tprid', $tsu->tprid)
                                                ->where('rtp.rtpporcentajedesde', '<=', $tsu->tsuporcentajecumplimientoniv)
                                                ->where('rtp.rtpporcentajehasta', '>=', $tsu->tsuporcentajecumplimientoniv)
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
                            $sca = scasucursalescategorias::join('tsutipospromocionessucursales  as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                        // ->where('tsuid', $tsu->tsuid)
                                                        ->where('tsu.sucid', $tsu->sucid)
                                                        ->where('tsu.tprid', 1) // SCA DE SELL IN
                                                        ->where('scasucursalescategorias.fecid', $fecid)
                                                        ->where('scasucursalescategorias.catid', $trr->catid)
                                                        ->first([
                                                            'scaid',
                                                            'scavalorizadoreal',

                                                        ]);

                            if($sca){
                                $nuevoRebate = ($sca->scavalorizadoreal*$trr->rtpporcentajerebate)/100;
                                $totalRebate = $totalRebate + $nuevoRebate;
                            }else{

                            }

                        }

                        $tsuu = tsutipospromocionessucursales::find($tsu->tsuid);
                        $tsuu->tsuvalorizadorebateniv = $totalRebate;
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
        
        return $requestsalida;   
    }
}
