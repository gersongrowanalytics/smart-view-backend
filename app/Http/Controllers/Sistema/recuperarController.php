<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\usuusuarios;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

class recuperarController extends Controller
{
    public function recuperarCuenta(Request $request)
    {
        $correo     = $request['correo'];
        $usu        = usuusuarios::where('usucorreo', $correo)->first(['usuusuario']);
        $mensaje    = "";
        $respuesta  = false;

        if($usu){
            $data = ['correo' => $correo, 'usuario' => $usu->usuusuario];

            Mail::to($correo)->send(new TestMail($data));
            $respuesta  = true;
            $mensaje = "El correo de recuperación fue enviando satisfactoriamente";
        }else{
            $mensaje = "Lo sentimos ese usuario no esta registrado";
            $respuesta  = false;
        }

        return response()->json([
            'respuesta'      => $respuesta,
            'mensaje'        => $mensaje,
            'datos'          => [],
        ]);
    }
}
