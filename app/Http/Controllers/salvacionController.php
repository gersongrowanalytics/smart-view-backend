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
use App\catcategorias;
use App\rscrbsscategorias;

class salvacionController extends Controller
{
    public function salvacion()
    {
        // $linkAnterior = "http://backs.gavsistemas.com/";
        // $linkNuevo    = "http://backend.leadsmartview.com/";

        $linkAnterior = "http://backend.leadsmartview.com/";
        $linkNuevo    = "https://pre-back.leadsmartview.com/";


        $cats = catcategorias::all();

        foreach($cats as $cat){
            $cate = catcategorias::find($cat->catid);

            $rutaUno    = explode($linkAnterior, $cat->catimagenfondo);
            $rutaDos    = explode($linkAnterior, $cat->caticono);
            $rutaTres   = explode($linkAnterior, $cat->caticonoseleccionado);
            $rutaCuatro = explode($linkAnterior, $cat->caticonohover);
            $rutaCinco  = explode($linkAnterior, $cat->catimagenfondoseleccionado);
            $rutaSeis   = explode($linkAnterior, $cat->catimagenfondoopaco);
            $rutaSiete  = explode($linkAnterior, $cat->caticononaranja);

            if(sizeof($rutaUno) > 0){
                $cate->catimagenfondo = $linkNuevo.$rutaUno[1];
            }
            if(sizeof($rutaDos) > 0){
                $cate->caticono = $linkNuevo.$rutaDos[1];
            }
            if(sizeof($rutaTres) > 0){
                $cate->caticonoseleccionado = $linkNuevo.$rutaTres[1];
            }
            if(sizeof($rutaCuatro) > 0){
                $cate->caticonohover = $linkNuevo.$rutaCuatro[1];
            }
            if(sizeof($rutaCinco) > 0){
                $cate->catimagenfondoseleccionado = $linkNuevo.$rutaCinco[1];
            }
            if(sizeof($rutaSeis) > 0){
                $cate->catimagenfondoopaco = $linkNuevo.$rutaSeis[1];
            }
            if(sizeof($rutaSiete) > 0){
                $cate->caticononaranja = $linkNuevo.$rutaSiete[1];
            }
            $cate->update();
        }

        $scas = scasucursalescategorias::where('scaiconocategoria', 'LIKE', '%'.$linkAnterior.'%')
                                        ->get();

        foreach($scas as $sca){
            $ruta = explode($linkAnterior, $sca->scaiconocategoria);

            if(sizeof($ruta) > 0){
                $scae = scasucursalescategorias::find($sca->scaid);
                $scae->scaiconocategoria = $linkNuevo.$ruta[1];
                $scae->update();
            }else{
                echo "<br>no tiene: ".$sca->scaid.' y nombre: '.$sca->scaiconocategoria.'<br>';

            }
        }

        $pros = proproductos::where('proimagen', 'LIKE', '%'.$linkAnterior.'%')->get();

        foreach($pros as $pro){
            $proe = proproductos::find($pro->proid);
            // http://backs.gavsistemas.com/Sistema/promociones/IMAGENES/PRODUCTOS/7-493-1253-HTP%20talla%20M-31%20a%20mas%20planchas.png
            $ruta = explode($linkAnterior, $pro->proimagen);

            if(sizeof($ruta) > 0){
                $proe->proimagen = $linkNuevo.$ruta[1];
                $proe->update();
            }else{
                echo "<br>no tiene: ".$pro->proid.' y nombre: '.$pro->proimagen.'<br>';

            }
        }

        $prbs = prbpromocionesbonificaciones::where('prbimagen', 'LIKE', '%'.$linkAnterior.'%')->get();

        foreach($prbs as $prb){
            $prbe = prbpromocionesbonificaciones::find($prb->prbid);
            $ruta = explode($linkAnterior, $prb->prbimagen);

            if(sizeof($ruta) > 0){
                $prbe->prbimagen = $linkNuevo.$ruta[1];
                $prbe->update();
            }else{
                echo "<br>no tiene: ".$prbe->prbid.' y nombre: '.$prb->prbimagen.'<br>';

            }
            

        }

        $prps = prppromocionesproductos::where('prpimagen', 'LIKE', '%'.$linkAnterior.'%')->get();

            foreach($prps as $prp){
                $prpe = prppromocionesproductos::find($prp->prpid);
                $ruta = explode($linkAnterior, $prp->prpimagen);
    
                if(sizeof($ruta) > 0){
                    $prpe->prpimagen = $linkNuevo.$ruta[1];
                    $prpe->update();
                }else{
                    echo "<br>no tiene: ".$prp->prpid.' y nombre: '.$prp->prpimagen.'<br>';
    
                }
                
    
            }
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
