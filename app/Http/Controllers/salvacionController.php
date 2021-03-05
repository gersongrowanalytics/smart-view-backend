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

class salvacionController extends Controller
{
    public function salvacion()
    {

        // $linkAntiguo = "http://backs.gavsistemas.com/";
        // $linkNuevo   = "http://backend.leadsmartview.com/";

        $linkAntiguo = "http://backend.leadsmartview.com/";
        $linkNuevo   = "https://pre-back.leadsmartview.com/";


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
        $cats = catcategorias::all();

        foreach($cats as $cat){
            $cate = catcategorias::find($cat->catid);
            $ruta1 = explode($linkAntiguo, $cat->catimagenfondo);

            if(sizeof($ruta1) > 1){
                $cate->catimagenfondo = $linkNuevo.$ruta1[1];
            }else{
                echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->catimagenfondo.'<br>';
            }

            $ruta2 = explode($linkAntiguo, $cat->caticono);

            if(sizeof($ruta2) > 1){
                $cate->caticono = $linkNuevo.$ruta2[1];
            }else{
                echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->caticono.'<br>';
            }   

            $ruta3 = explode($linkAntiguo, $cat->caticonoseleccionado);

            if(sizeof($ruta3) > 1){
                $cate->caticonoseleccionado = $linkNuevo.$ruta3[1];
            }else{
                echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->caticonoseleccionado.'<br>';
            }

            $ruta4 = explode($linkAntiguo, $cat->caticonohover);

            if(sizeof($ruta4) > 1){
                $cate->caticonohover = $linkNuevo.$ruta4[1];
            }else{
                echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->caticonohover.'<br>';
            }

            $ruta5 = explode($linkAntiguo, $cat->catimagenfondoseleccionado);
            if(sizeof($ruta5) > 1){
                $cate->catimagenfondoseleccionado = $linkNuevo.$ruta5[1];
            }else{
                echo "<br>no tiene: ".$cat->catid.' y nombre: '.$cat->catimagenfondoseleccionado.'<br>';
            }

            $ruta6 = explode($linkAntiguo, $cat->catimagenfondoopaco);
            if(sizeof($ruta6) > 1){
                $cate->catimagenfondoopaco = $linkNuevo.$ruta6[1];
            }else{
                
            }

            $ruta7 = explode($linkAntiguo, $cat->caticononaranja);
            if(sizeof($ruta7) > 1){
                $cate->caticononaranja = $linkNuevo.$ruta7[1];
            }else{
                
            }

            $cate->update();
        }

        // // CAMBIAR IMAGENES DE PRODUCTOS EN PROMOCIONES
        $prbs = prbpromocionesbonificaciones::where('prbimagen', 'LIKE', '%'.$linkAntiguo.'%')->get();

        foreach($prbs as $prb){
            $prbe = prbpromocionesbonificaciones::find($prb->prbid);
            $ruta = explode($linkAntiguo, $prb->prbimagen);

            if(sizeof($ruta) > 1){
                $prbe->prbimagen = $linkNuevo.$ruta[1];
                $prbe->update();
            }else{
                echo "<br>no tiene: ".$prbe->prbid.' y nombre: '.$prb->prbimagen.'<br>';

            }
            

        }

        $prps = prppromocionesproductos::where('prpimagen', 'LIKE', '%'.$linkAntiguo.'%')->get();

        foreach($prps as $prp){
            $prpe = prppromocionesproductos::find($prp->prpid);
            $ruta = explode($linkAntiguo, $prp->prpimagen);

            if(sizeof($ruta) > 1){
                $prpe->prpimagen = $linkNuevo.$ruta[1];
                $prpe->update();
            }else{
                echo "<br>no tiene: ".$prp->prpid.' y nombre: '.$prp->prpimagen.'<br>';

            }
        }
        
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
