<?php

namespace App\Http\Controllers\Sistema\RebatesBonus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\sucsucursales;
use App\rbbrebatesbonus;
use App\rbsrebatesbonussucursales;
use App\rscrbsscategorias;
use App\scasucursalescategorias;
use App\tsutipospromocionessucursales;

class AsignarSucursalesController extends Controller
{
    public function AsiganarSucursales($fecid)
    {


        // LIMPIAR REBATE BONNUS
        rbsrebatesbonussucursales::where('fecid', $fecid)
                                ->update([
                                    'rbsobjetivo'     => 0,
                                    'rbsreal'         => 0,
                                    'rbscumplimiento' => 0,
                                    'rbsrebate'       => 0,
                                ]);

        $logs = array(
            "oct" => [],
            "novydic" => []
        );

        $rbbs = rbbrebatesbonus::where('fecid', $fecid)->get();
        $sucs = sucsucursales::
                                // get(['sucid']);
                                // where('sucestado', 1)
                                get(['sucid']);

        foreach($rbbs as $rbb){

            foreach($sucs as $suc){

                $rscs = rscrbsscategorias::where('rbbid', $rbb->rbbid)
                                        ->where('sucid', $suc->sucid)
                                        ->where('rscestado', 1)
                                        ->get();

                $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                            ->where('scasucursalescategorias.fecid', $rbb->fecid )
                                            ->where('tsu.tprid', 1)
                                            ->where('scasucursalescategorias.sucid', $suc->sucid)
                                            ->where(function ($query) use($rscs) {

                                                foreach($rscs as $rsc){
                                                    $query->orwhere('catid', $rsc->catid);
                                                }

                                            })
                                            ->get();
                $rbsobjetivo = 0;
                $rbsreal = 0;
                $rbscumplimiento = 0;
                $rbsrebate = 0;

                foreach($scas as $sca){
                    $rbsobjetivo     = $rbsobjetivo + $sca->scavalorizadoobjetivo;
                    $rbsreal         = $rbsreal + $sca->scavalorizadoreal;
                }

                if($rbsobjetivo > 1){
                    $rbscumplimiento = (100*$rbsreal)/$rbsobjetivo;
                }

                if($rbscumplimiento >= $rbb->rbbcumplimiento){
                    $tsu = tsutipospromocionessucursales::where('tprid', 1)
                                                        ->where('sucid', $suc->sucid)
                                                        ->where('fecid', $rbb->fecid)
                                                        ->first();

                    if($tsu){
                        $rbsrebate = ($tsu->tsuvalorizadoreal*$rbb->rbbporcentaje)/100;
                        
                        $logs[$rbb->fecid][] = "Entra en el rango la sucursal: ".$suc->sucid." con un porcentaje de :".$rbscumplimiento." y un rebate de: ".$rbsrebate;
                        
                    }
                    
                }

                $rbs = rbsrebatesbonussucursales::where('sucid', $suc->sucid)
                                                ->where('rbbid', $rbb->rbbid)
                                                ->where('fecid', $rbb->fecid)
                                                ->first();

                if($rbs){

                    $rbs->rbsobjetivo     = $rbsobjetivo;
                    $rbs->rbsreal         = $rbsreal;
                    $rbs->rbscumplimiento = $rbscumplimiento;
                    $rbs->rbsrebate       = $rbsrebate;
                    $rbs->update();

                }else{
                    $rbsn = new rbsrebatesbonussucursales;
                    $rbsn->fecid           = $rbb->fecid;
                    $rbsn->rbbid           = $rbb->rbbid;
                    $rbsn->sucid           = $suc->sucid;
                    $rbsn->rbsobjetivo     = $rbsobjetivo;
                    $rbsn->rbsreal         = $rbsreal;
                    $rbsn->rbscumplimiento = $rbscumplimiento;
                    $rbsn->rbsrebate       = $rbsrebate;
                    $rbsn->save();
                }

            }

            
        }

        dd($logs);
    }

    public function ActualizarCategoriasBonus($fecid, $rbbid)
    {
 
        $log = array(
            "NO_SE_AGREGO_RSC_CATEGORIAS" => [],
            "NO_SE_ENCONTRO_RBS" => []
        );

        $cateogiras = [1];

        $sucs = sucsucursales::
                            // where('sucestado', 1)
                            // ->get();
                            get();

        foreach($sucs as $suc){
            $rbs = rbsrebatesbonussucursales::where('sucid', $suc->sucid)
                                            ->where('fecid', $fecid)
                                            ->where('rbbid', $rbbid)
                                            ->first();

            if($rbs){
                foreach($cateogiras as $catid){

                    $rsc = rscrbsscategorias::where('catid', $catid)
                                            ->where('fecid', $fecid)
                                            ->where('rbbid', $rbbid)
                                            ->where('sucid', $suc->sucid)
                                            ->where('rbsid', $rbs->rbsid)
                                            ->first();

                    if($rsc){
                        if($rsc->rscestado != 1){
                            $rsc->rscestado = 1;
                            $rsc->update();
                        }
                    }else{
                        $rscn = new rscrbsscategorias;
                        $rscn->fecid = $fecid;
                        $rscn->rbbid = $rbbid;
                        $rscn->sucid = $suc->sucid;
                        $rscn->rbsid = $rbs->rbsid;
                        $rscn->catid = $catid;
                        $rscn->rscestado = 1;
                        if($rscn->save()){

                        }else{
                            $log["NO_SE_AGREGO_RSC_CATEGORIAS"][] = "SUC: ".$suc->sucid." FEC: ".$fecid." RBB: ".$rbbid;
                        }
                    }

                    
                }
            }else{
                $rbsn = new rbsrebatesbonussucursales;
                $rbsn->fecid           = $fecid;
                $rbsn->rbbid           = $rbbid;
                $rbsn->sucid           = $suc->sucid;
                $rbsn->rbsobjetivo     = 0;
                $rbsn->rbsreal         = 0;
                $rbsn->rbscumplimiento = 0;
                $rbsn->rbsrebate       = 0;
                if($rbsn->save()){
                    foreach($cateogiras as $catid){
                        $rscn = new rscrbsscategorias;
                        $rscn->fecid = $fecid;
                        $rscn->rbbid = $rbbid;
                        $rscn->sucid = $suc->sucid;
                        $rscn->rbsid = $rbsn->rbsid;
                        $rscn->catid = $catid;
                        $rscn->rscestado = 1;
                        if($rscn->save()){
    
                        }else{
                            $log["NO_SE_AGREGO_RSC_CATEGORIAS"][] = "SUC: ".$suc->sucid." FEC: ".$fecid." RBB: ".$rbbid;
                        }
                    }
                }else{
                    $log["NO_SE_CREO_RBS"][] = "SUC: ".$suc->sucid." FEC: ".$fecid." RBB: ".$rbbid;    
                }


                $log["NO_SE_ENCONTRO_RBS"][] = "SUC: ".$suc->sucid." FEC: ".$fecid." RBB: ".$rbbid;
            }

        }

        dd($log);
    }
}