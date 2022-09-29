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
        $usu        = usuusuarios::where('usucorreo', $correo)->first(['usuid', 'usuusuario', 'usutoken']);
        $mensaje    = "";
        $respuesta  = false;

        if($usu){
            
            $nuevoToken    = Str::random(60);

            $usue = usuusuarios::find($usu->usuid);
            $usue->usutoken = $nuevoToken;
            if($usue->update()){
                $respuesta = true;
                $mensaje   = "El correo fue enviado satisfactoriamente";

                $data = ['token' => $nuevoToken];
                Mail::to($correo)->send(new MailRecuperarContrasenaNuevo($data));

            }else{
                $respuesta = false;
                $mensaje   = "Lo sentimos, no se pudo actualizar el token del usuario";
            }

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

        $nuevoToken = "";
        $usuario = "";

        $usu = usuusuarios::where('usutoken', $token)->first();
        if($usu){

            $nuevoToken = Str::random(60);

            $usu->usucontrasena = Hash::make($nuevaContrasenia);
            $usu->usutoken      = $nuevoToken;
            $usu->update();

            $usuario = $usu->usuusuario;

        }else{
            $respuesta = false;
            $mensaje   = "Lo sentimos, el codigo ingresado ha expirado, porfavor vuelva a solicitar otra recuperación de este";
        }
        
        return response()->json([
            'respuesta'  => $respuesta,
            'mensaje'    => $mensaje,
            'nuevoToken' => $nuevoToken,
            'usuario'    => $usuario
        ]);

    }

    public function EnviarCorreoVista(Request $request)
    {
        
        return view('CorreoRecupearContrasenaNuevo', ['token' => 'http://localhost:3000/cambiar-contrasenia/Yv8FvjoV1sfIzzNaZsTiWmCUt5RLy2MJT1y1HlyJucOfQMbXYsTRHeJwWvAj']);

    }
}
