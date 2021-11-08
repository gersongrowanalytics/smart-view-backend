<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\proproductos;
use App\prbpromocionesbonificaciones;
use App\prppromocionesproductos;
use App\ussusuariossucursales;
use App\sucsucursales;
use App\scasucursalescategorias;
use App\tsutipospromocionessucursales;
use App\zonzonas;
use App\rscrbsscategorias;
use App\catcategorias;
use App\vsoventassso;

class salvacionController extends Controller
{
    public function salvacion()
    {

        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }
                }
            }
        }

        // $linkAntiguo = "http://backs.gavsistemas.com/";
        // $linkNuevo   = "http://backend.leadsmartview.com/";

        // $linkAntiguo = "http://backend.leadsmartview.com/";
        // $linkNuevo   = "https://pre-back.leadsmartview.com/";


        // CAMBIAR IMAGEN DE PRODUCTOS
        // $pros = proproductos::where('proimagen', 'LIKE', '%'.$linkAntiguo.'%')->get();

        // foreach($pros as $pro){
        //     $proe = proproductos::find($pro->proid);
        //     $ruta = explode($linkAntiguo, $pro->proimagen);

        //     if(sizeof($ruta) > 0){
        //         $proe->proimagen = $linkNuevo.$ruta[1];
        //         $proe->update();
        //     }else{
        //         echo "<br>no tiene: ".$pro->proid.' y nombre: '.$pro->proimagen.'<br>';

        //     }
            
        // }


        // CAMBIAR IMAGEN DE CATEGORIAS
        // $cats = catcategorias::all();

        // foreach($cats as $cat){
        //     $cate = catcategorias::find($cat->catid);
        //     $ruta1 = explode($linkAntiguo, $cat->catimagenfondo);

        //     if(sizeof($ruta1) > 1){
        //         $cate->catimagenfondo = $linkNuevo.$ruta1[1];
        //     }else{
        //         echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->catimagenfondo.'<br>';
        //     }

        //     $ruta2 = explode($linkAntiguo, $cat->caticono);

        //     if(sizeof($ruta2) > 1){
        //         $cate->caticono = $linkNuevo.$ruta2[1];
        //     }else{
        //         echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->caticono.'<br>';
        //     }   

        //     $ruta3 = explode($linkAntiguo, $cat->caticonoseleccionado);

        //     if(sizeof($ruta3) > 1){
        //         $cate->caticonoseleccionado = $linkNuevo.$ruta3[1];
        //     }else{
        //         echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->caticonoseleccionado.'<br>';
        //     }

        //     $ruta4 = explode($linkAntiguo, $cat->caticonohover);

        //     if(sizeof($ruta4) > 1){
        //         $cate->caticonohover = $linkNuevo.$ruta4[1];
        //     }else{
        //         echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->caticonohover.'<br>';
        //     }

        //     $ruta5 = explode($linkAntiguo, $cat->catimagenfondoseleccionado);
        //     if(sizeof($ruta5) > 1){
        //         $cate->catimagenfondoseleccionado = $linkNuevo.$ruta5[1];
        //     }else{
        //         echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->catimagenfondoseleccionado.'<br>';
        //     }

        //     $ruta6 = explode($linkAntiguo, $cat->catimagenfondoopaco);
        //     if(sizeof($ruta6) > 1){
        //         $cate->catimagenfondoopaco = $linkNuevo.$ruta6[1];
        //     }else{
                
        //     }

        //     $ruta7 = explode($linkAntiguo, $cat->caticononaranja);
        //     if(sizeof($ruta7) > 1){
        //         $cate->caticononaranja = $linkNuevo.$ruta7[1];
        //     }else{
                
        //     }

        //     $cate->update();
        // }

        // // CAMBIAR IMAGENES DE PRODUCTOS EN PROMOCIONES
        // $prbs = prbpromocionesbonificaciones::where('prbimagen', 'LIKE', '%'.$linkAntiguo.'%')->get();

        // foreach($prbs as $prb){
        //     $prbe = prbpromocionesbonificaciones::find($prb->prbid);
        //     $ruta = explode($linkAntiguo, $prb->prbimagen);

        //     if(sizeof($ruta) > 1){
        //         $prbe->prbimagen = $linkNuevo.$ruta[1];
        //         $prbe->update();
        //     }else{
        //         echo "<br>no tiene: ".$prbe->prbid.' y nombre: '.$prb->prbimagen.'<br>';

        //     }
            

        // }

        // $prps = prppromocionesproductos::where('prpimagen', 'LIKE', '%'.$linkAntiguo.'%')->get();

        // foreach($prps as $prp){
        //     $prpe = prppromocionesproductos::find($prp->prpid);
        //     $ruta = explode($linkAntiguo, $prp->prpimagen);

        //     if(sizeof($ruta) > 1){
        //         $prpe->prpimagen = $linkNuevo.$ruta[1];
        //         $prpe->update();
        //     }else{
        //         echo "<br>no tiene: ".$prp->prpid.' y nombre: '.$prp->prpimagen.'<br>';

        //     }
        // }
        
        // CAMBIAR SCAS
        // $scas = scasucursalescategorias::where('scaiconocategoria', 'LIKE', '%'.$linkAntiguo.'%')->get();

        // foreach($scas as $sca){
        //     $scae = scasucursalescategorias::find($sca->scaid);

        //     $ruta = explode($linkAntiguo, $sca->scaiconocategoria);

        //     if(sizeof($ruta) > 1){
        //         $scae->scaiconocategoria = $linkNuevo.$ruta[1];
                
        //     }else{
        //         echo "<br>no tiene: ".$sca->scaid.' y nombre: '.$sca->scaiconocategoria.'<br>';

        //     }

        //     $scae->update();
        // }


    }

    public function asignarzonassucursales()
    {
        $array = [];

        $usss = ussusuariossucursales::join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                    ->join('zonzonas as zon', 'zon.zonid', 'usu.zonid')
                                    ->get([
                                        'ussusuariossucursales.usuid',
                                        'ussusuariossucursales.sucid',
                                        'zon.zonnombre',
                                        'usu.zonid'
                                    ]);

        foreach($usss as $posicion => $uss){
            $suce = sucsucursales::find($uss->sucid);
            $suce->zonid = $uss->zonid;
            if($suce->update()){
                $array[] = $uss->zonnombre." - ".$suce->sucnombre;
            }
        }
        
        dd($array);
    }

    public function cambiarEstadoSucursales()
    {
        $ussusuariossucursales = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                                    ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                                    ->join('zonzonas as zon', 'zon.zonid', 'usu.zonid')
                                                    ->where('usu.estid', 1)
                                                    ->get([
                                                        'ussusuariossucursales.ussid',
                                                        'zon.zonid',
                                                        'zon.zonnombre',
                                                        'suc.sucid',
                                                        'suc.sucnombre'
                                                    ]);


        foreach($ussusuariossucursales as $ussusuariossucursale){
            $suce = sucsucursales::find($ussusuariossucursale->sucid);
            $suce->sucestado = 1;
            $suce->update();
        }
    }

    public function CambiarImagenSellOut()
    {
        $log = [];

        $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                        ->join('catcategorias as cat', 'cat.catid', 'scasucursalescategorias.catid')
                                        ->where('tsu.tprid', 2)
                                        ->where('scasucursalescategorias.fecid', 3)
                                        ->get([
                                            'scasucursalescategorias.scaid',
                                            'scasucursalescategorias.scaiconocategoria',
                                            'cat.catnombre',
                                            'tsu.tprid'
                                        ]);


        foreach($scas as $sca){
            $scae = scasucursalescategorias::find($sca->scaid);
            
            if($sca->tprid == 1){
                $scae->scaiconocategoria = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$sca->catnombre.'-Sell In.png';
            }else{
                $scae->scaiconocategoria = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$sca->catnombre.'-Sell Out.png';
            }

            if($scae->update()){
                $log['CORRECTO'][] = "Se actualizo correctamente la imagen sell: ".$sca->scaid;
            }else{
                $log['INCORRECTO'][] = "No se pudo actualizar la imagen sell: ".$sca->scaid;
            }
        }


        dd($log);
    }

    public function CalcularRebateBonus()
    {
            
    }

    public function ActualizarToGo($fecid)
    {
        $tsus = tsutipospromocionessucursales::where('fecid', $fecid)
                                            ->get();

        foreach($tsus as $tsu){
            $tsu = tsutipospromocionessucursales::find($tsu->tsuid);
            $tsu->tsuvalorizadotogo = $tsu->tsuvalorizadoobjetivo - $tsu->tsuvalorizadoreal;
            $tsu->update();
        }





        // 

        $scas = scasucursalescategorias::join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')   
                                            ->where('tsu.tprid', 2)
                                            ->where('scasucursalescategorias.fecid', $fecid)
                                            ->get([
                                                'scasucursalescategorias.scaid',
                                                'scasucursalescategorias.catid',
                                                'scasucursalescategorias.sucid',
                                                'scasucursalescategorias.scavalorizadoobjetivo',
                                            ]);

        foreach ($scas as $key => $sca) {
            
            $sumvso = vsoventassso::join('proproductos as pro', 'pro.proid', 'vsoventassso.proid')
                                    ->where('fecid', $fecid)
                                    ->where('pro.catid', $sca->catid)
                                    ->where('sucid', $sca->sucid)
                                    ->sum('vsovalorizado');

            $sumvsoniv = vsoventassso::join('proproductos as pro', 'pro.proid', 'vsoventassso.proid')
                                    ->where('fecid', $fecid)
                                    ->where('pro.catid', $sca->catid)
                                    ->where('sucid', $sca->sucid)
                                    ->sum('vsovalorizadoniv');

            $scae = scasucursalescategorias::find($sca->scaid);
            $scae->scavalorizadoreal = $sumvso;
            $scae->scavalorizadorealniv = $sumvsoniv;

            if(intval(round($scae->scavalorizadoobjetivo)) <= 0){
                $scae->scavalorizadotogo = 0;
                $scae->scavalorizadotogoniv = 0;
            }else{
                $scae->scavalorizadotogo = $scae->scavalorizadoobjetivo - $sumvso;
                $scae->scavalorizadotogoniv = $scae->scavalorizadoobjetivo - $sumvsoniv;
            }

            $scae->update();

        }

        // 

        $tsus = tsutipospromocionessucursales::where('fecid', $fecid)
                                            ->where('tprid', 2)
                                            ->get();

                                            // tsuvalorizadorealniv
                                            // tsuvalorizadotogoniv
                                            // tsuporcentajecumplimientoniv
                                            
        foreach ($tsus as $key => $tsu) {
            // $sumscaniv = scasucursalescategorias::where('tsuid', $tsu->tsuid)
            //                                 ->sum('scavalorizadorealniv');

            $sumscaniv = vsoventassso::join('proproductos as pro', 'pro.proid', 'vsoventassso.proid')
                                    ->where('fecid', $fecid)
                                    // ->where('pro.catid', $sca->catid)
                                    ->where('sucid', $tsu->sucid)
                                    ->sum('vsovalorizadoniv');

            $tsue = tsutipospromocionessucursales::find($tsu->tsuid);

            if(intval(round($tsue->tsuvalorizadoobjetivo)) <= 0){
                $porcentajeCumplimiento = $sumscaniv;
                $togo = 0;
            }else{
                $porcentajeCumplimiento = (100*$sumscaniv)/$tsue->tsuvalorizadoobjetivo;
                $togo = $tsue->tsuvalorizadoobjetivo - $sumscaniv;
            }
            
            $tsue->tsuvalorizadorealniv         = $sumscaniv;
            
            $tsue->tsuvalorizadotogoniv         = $togo;
            $tsue->tsuporcentajecumplimientoniv = $porcentajeCumplimiento;
            $tsue->update();
        }

    }

    public function ActualizarSucursales()
    {

        $sucs = sucsucursales::all();

        foreach($sucs as $suc){

            $uss = ussusuariossucursales::join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                    ->where('ussusuariossucursales.sucid', $suc->sucid)
                                    ->first([
                                        'usu.ususoldto',
                                        'ussusuariossucursales.sucid',
                                        'usu.estid'
                                    ]);

            if($uss){

                $suce = sucsucursales::find($suc->sucid);
                $suce->sucsoldto = $uss->ususoldto;
                $suce->sucestado = $uss->estid;
                $suce->update();

                $zone = zonzonas::find($suce->zonid);
                if($zone){
                    $zone->zonestado = $uss->estid;
                    $zone->update();
                }   
            }
        }


    }

    public function AsignarSi($fecid)
    {
        $sucs = sucsucursales::where('sucestado', 1)->get();
        $cats = catcategorias::all();

        foreach ($sucs as $key => $suc) {
            $tsu = tsutipospromocionessucursales::where('fecid', $fecid)
                                                ->where('tprid', 1)
                                                ->where('sucid', $suc->sucid)
                                                ->first();

            if($tsu){

                $scas = scasucursalescategorias::where('tsuid', $tsu->tsuid)
                                                ->where('fecid', $fecid)
                                                ->where('sucid', $suc->sucid)
                                                ->get();

                if(sizeof($scas) > 0){

                }else{
                    foreach ($cats as $key => $cat) {
                        $nuevosca = new scasucursalescategorias;
                        $nuevosca->sucid                 = $suc->sucid;
                        $nuevosca->catid                 = $cat->catid;
                        $nuevosca->fecid                 = $fecid;
                        $nuevosca->tsuid                 = $tsu->tsuid;
                        $nuevosca->scavalorizadoobjetivo = 0;
                        $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$cat->catnombre.'-Sell In.png';
                        $nuevosca->scavalorizadoreal     = 0;
                        $nuevosca->scavalorizadotogo     = 0;
                        $nuevosca->save();                        
                    }
                }

            }else{

                $nuevotsu = new tsutipospromocionessucursales;
                $nuevotsu->fecid = $fecid;
                $nuevotsu->treid = $suc->treid;
                $nuevotsu->sucid = $suc->sucid;
                $nuevotsu->tprid = 1;
                $nuevotsu->tsuporcentajecumplimiento = 0;
                $nuevotsu->tsuvalorizadoobjetivo  = 0;
                $nuevotsu->tsuvalorizadoreal      = 0;
                $nuevotsu->tsuvalorizadorebate    = 0;
                $nuevotsu->tsuvalorizadotogo      = 0;
                if($nuevotsu->save()){

                    foreach ($cats as $key => $cat) {
                        $nuevosca = new scasucursalescategorias;
                        $nuevosca->sucid                 = $suc->sucid;
                        $nuevosca->catid                 = $cat->catid;
                        $nuevosca->fecid                 = $fecid;
                        $nuevosca->tsuid                 = $nuevotsu->tsuid;
                        $nuevosca->scavalorizadoobjetivo = 0;
                        $nuevosca->scaiconocategoria     = env('APP_URL').'/Sistema/categorias-tiposPromociones/img/iconos/'.$cat->catnombre.'-Sell In.png';
                        $nuevosca->scavalorizadoreal     = 0;
                        $nuevosca->scavalorizadotogo     = 0;
                        $nuevosca->save();                        
                    }

                }

            }

        }




    }

    public function QuitarDecimales($fecid)
    {
        $logs = [];

        $prbs = prbpromocionesbonificaciones::join('prmpromociones as prm', 'prm.prmid', 'prbpromocionesbonificaciones.prmid')
                                    ->where('prm.fecid', $fecid)
                                    ->get(['prbid', 'prbcomprappt']);

        foreach ($prbs as $prb) {
            
            if(is_numeric ( $prb->prbcomprappt )){
                $desc = "NORMAL";
                if(number_format($prb->prbcomprappt, 2) < 0.9){
                    $desc = "PORCENTAJE";
                }

                $prbe = prbpromocionesbonificaciones::find($prb->prbid);
                $prbe->prbcomprappt = number_format($prb->prbcomprappt, 2);
                $prbe->update();
                $logs[] = $desc." | ".$prb->prbcomprappt." - ".number_format($prb->prbcomprappt, 2);
            }else{
                
            }
        }

        return $logs;
    }
}



// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png";
// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell Out.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell Out.png";

// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Infant Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Infant Care-Sell In.png";
// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Infant Care-Sell Out.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Infant Care-Sell Out.png";

// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png";
// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png";



// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Adult Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Adult Care-Sell In.png";
// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Adult Care-Sell Out.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Adult Care-Sell Out.png";


// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png";
// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png";


// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Wipes-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Wipes-Sell In.png";
// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Wipes-Sell Out.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Wipes-Sell Out.png";

// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Fem Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Fem Care-Sell In.png";
// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Fem Care-Sell Out.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Fem Care-Sell Out.png";





// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png";
// UPDATE scasucursalescategorias SET scaiconocategoria  = "http://backend.leadsmartview.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png" WHERE scaiconocategoria  = "http://backs.gavsistemas.com/Sistema/categorias-tiposPromociones/img/iconos/Family Care-Sell In.png";
