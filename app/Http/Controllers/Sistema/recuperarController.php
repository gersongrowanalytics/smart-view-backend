<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\usuusuarios;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailRecuperarContrasenaNuevo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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

    public function EnviarCorreoRecuperar(Request $request)
    {

        $correo     = $request['correo'];
        $usu        = usuusuarios::where('usucorreo', $correo)->first(['usuusuario', 'usutoken']);
        $mensaje    = "";
        $respuesta  = false;

        if($usu){

            $nuevoToken    = Str::random(60);
            $usu->usutoken = $nuevoToken;
            $usu->update();

            // $data = ['correo' => $correo, 'usuario' => $usu->usuusuario];
            // Mail::to($correo)->send(new TestMail($data));

            $data = ['token' => $nuevoToken];
            Mail::to($correo)->send(new MailRecuperarContrasenaNuevo($data));

            $respuesta = true;
            $mensaje   = "El correo fue enviado satisfactoriamente";

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

    public function CambiarContraseniaRecuperar(Request $request)
    {

        $respuesta = true;
        $mensaje   = "La contraseña del usuario se actualizo correctamente";

        $nuevaContrasenia = $request['nuevaContrasenia'];
        $token = $request['token'];

        $usu = usuusuarios::where('usutoken', $token)->first();
        if($usu){
            $usu->usucontrasena = Hash::make($nuevaContrasenia);
            $usu->usutoken      = Str::random(60);
            $usu->update();
        }else{
            $respuesta = false;
            $mensaje   = "Lo sentimos, el codigo ingresado ha expirado, porfavor vuelva a solicitar otra recuperación de este";
        }
        
        return response()->json([
            'respuesta' => $respuesta,
            'mensaje'   => $mensaje,
        ]);

    }
}
