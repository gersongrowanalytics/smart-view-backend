<?php

namespace App\Http\Controllers\Sistema\SinAsignar;

use App\Http\Controllers\Controller;
use App\Mail\MailInformarAsignacionImagenProductoController;
use App\Mail\MailInformarStatusImagenesController;
use App\proproductos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Exception;

class MetEnviarStatusImagenesController extends Controller
{
    public function MetEnviarStatusImagenes (Request $request)
    {
        $respuesta   = true;
        $mensaje     = "Se retornaron todos los registros con éxito";
        $mensajeserv = "";
        $correo      = "gerson.vilca@grow-analytics.com.pe";

        try {
            $pro = proproductos::where('proimagen', '/')
                                    ->where('proespromocion', 1)
                                    ->get();
            if ($pro) {
                $cantidadSinImagen = count($pro);
            
                $data = ['cantidad' => $cantidadSinImagen, 'registros' => $pro];
                Mail::to($correo)->send(new MailInformarStatusImagenesController($data));
            }else{
                $respuesta  = false;
                $mensaje    = "Lo siento, no se encontraron registros sin asignar imagen";
            }
        } catch (Exception $e) {
            $mensajeserv  = $e->getMessage();
            $respuesta    = false;
            $mensaje      = "Lo siento, surgió un error";
        }
        
        return response()->json([
            "respuesta"  => $respuesta,
            "mensaje"    => $mensaje,
            "mensajedev" => $mensajeserv
        ]);
    }
}
