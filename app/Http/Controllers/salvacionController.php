<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\proproductos;
use App\prbpromocionesbonificaciones;
use App\prppromocionesproductos;

class salvacionController extends Controller
{
    public function salvacion()
    {
        // $pros = proproductos::where('proimagen', 'LIKE', '%http://backs.gavsistemas.co%')->get();

        // foreach($pros as $pro){
        //     $proe = proproductos::find($pro->proid);
        //     // http://backs.gavsistemas.com/Sistema/promociones/IMAGENES/PRODUCTOS/7-493-1253-HTP%20talla%20M-31%20a%20mas%20planchas.png
        //     $ruta = explode("http://backs.gavsistemas.com/", $pro->proimagen);

        //     if(sizeof($ruta) > 0){
        //         $proe->proimagen = "http://backend.leadsmartview.com/".$ruta[1];
        //         $proe->update();
        //     }else{
        //         echo "<br>no tiene: ".$pro->proid.' y nombre: '.$pro->proimagen.'<br>';

        //     }
            

        // }


        $prbs = prbpromocionesbonificaciones::where('prbimagen', 'LIKE', '%http://backs.gavsistemas.co%')->get();

        foreach($prbs as $prb){
            $prbe = prbpromocionesbonificaciones::find($prb->prbid);
            $ruta = explode("http://backs.gavsistemas.com/", $prb->prbimagen);

            if(sizeof($ruta) > 0){
                $prbe->prbimagen = "http://backend.leadsmartview.com/".$ruta[1];
                $prbe->update();
            }else{
                echo "<br>no tiene: ".$prbe->prbid.' y nombre: '.$prb->prbimagen.'<br>';

            }
            

        }

        $prps = prppromocionesproductos::where('prpimagen', 'LIKE', '%http://backs.gavsistemas.co%')->get();

            foreach($prps as $prp){
                $prpe = prppromocionesproductos::find($prp->prpid);
                $ruta = explode("http://backs.gavsistemas.com/", $prp->prpimagen);
    
                if(sizeof($ruta) > 0){
                    $prpe->prpimagen = "http://backend.leadsmartview.com/".$ruta[1];
                    $prpe->update();
                }else{
                    echo "<br>no tiene: ".$prp->prpid.' y nombre: '.$prp->prpimagen.'<br>';
    
                }
                
    
            }
    }
}
