<?php

namespace App\Http\Controllers\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\usuusuarios;

class loginController extends Controller
{
    public function login(Request $request)
    {

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;

        $usuario    = $request['usuario'];
        $contrasena = $request['contrasena'];

        echo $usuario;
        // try{
            
        //     $usuusaurio = usuusuarios::where('usuusuario', $usuario)
        //                                 ->first(['usucontrasena']);

        //     if($usuusaurio){

        //         if (Hash::check($contrasena, $usuarusuusaurioio->usucontrasena)) {
        //             $respuesta      = true;
        //             $mensaje        = 'Login Correcto';
        //             $mensajeDetalle = 'Bienvenido.';
        //             $linea          = __LINE__;
        //         }else{
        //             $respuesta      = false;
        //             $mensaje        = 'Login Incorrecto';
        //             $mensajeDetalle = 'La contraseña es incorrecta.';
        //             $linea          = __LINE__;
        //         }

        //     }else{
        //         $respuesta      = false;
        //         $mensaje        = 'Login Incorrecto';
        //         $mensajeDetalle = 'El usuario no esta registrado.';
        //         $linea          = __LINE__;

        //     }

        // } catch (Exception $e) {
        //     $mensajedev = $e->getMessage();
        //     $linea      = __LINE__;
        // }
        
        // return response()->json([
        //     $respuesta      => $respuesta,
        //     $mensaje        => $mensaje,
        //     $datos          => $datos,
        //     $linea          => $linea,
        //     $mensajeDetalle => $mensajeDetalle,
        //     $mensajedev     => $mensajedev
        // ]);
    }
}
