<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\usuusuarios;
use App\perpersonas;

class ObtenerUsuarioController extends Controller
{
    public function ObtenerUsuario(Request $request)
    {
        $req_usuusuario = $request->usucorreo;

        $usu = usuusuarios::join('perpersonas as per', 'usuusuarios.perid', 'per.perid')
                            ->where('usuusuario', $req_usuusuario)
                            ->get([
                                'per.pernombrecompleto',
                                'per.pernombre',
                                'per.perapellidopaterno',
                                'per.perapellidopaterno',
                                'usuusuarios.usuusuario',
                            ])->first();

        if($usu){

            $codigo     = 200;
            $mensaje    = 'Usuario obtenido con éxito';
            
            return response()->json([
                'dato'      => $usu,
                'mensaje'   => $mensaje,
                'response'  => true,
            ],$codigo);

        }else{

            $codigo     = 500;
            $mensaje    = 'Ha ocurrido un error al obtener el usuario';

            return response()->json([
                'mensaje'   => $mensaje,
                'response'  => false
            ],$codigo);
            
        }
    }
}
