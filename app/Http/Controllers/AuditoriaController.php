<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\audauditorias;
use App\usuusuarios;

class AuditoriaController extends Controller
{
    public function registrarAuditoria(
        $usutoken,
        $usuid,
        $audip,
        $audjsonentrada,
        $audjsonsalida,
        $auddescripcion,
        $audaccion,
        $audruta,
        $audpk
    )
    {
        $respuesta = false;

        if($usuid == null){
            $usuusuario = usuusuarios::where('usutoken', $usutoken)->first(['usuid']);
            if($usuusuario){
                $usuid = $usuusuario->usuid;
            }else{
                $usuid = null;
            }
        }

        $audauditorias = new audauditorias;
        $audauditorias->usuid           = $usuid;
        $audauditorias->audip           = $audip;
        $audauditorias->audjsonentrada  = $audjsonentrada;

        // if(sizeof($$audjsonsalida) < 100){
        //     $audauditorias->audjsonsalida   = $audjsonsalida;
        // }else{
        //     $audauditorias->audjsonsalida   = null;
        // }

        $audauditorias->audjsonsalida   = null;

        $audauditorias->auddescripcion  = $auddescripcion;
        $audauditorias->audaccion       = $audaccion;
        $audauditorias->audruta         = $audruta;
        $audauditorias->audpk           = $audpk;
        if($audauditorias->save()){
            $respuesta = true;
        }else{
            $respuesta = false;
        }

        return $respuesta;

    }
}
