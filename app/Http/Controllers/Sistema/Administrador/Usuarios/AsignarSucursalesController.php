<?php

namespace App\Http\Controllers\Sistema\Administrador\Usuarios;

use App\Http\Controllers\Controller;
use App\ussusuariossucursales;
use Illuminate\Http\Request;

class AsignarSucursalesController extends Controller
{
    public function AsignarSucursales(Request $request)
    {
        $respuesta = true;
        $mensaje   = 'Se agrego correctamente la sucursal al usuario';

        $usuid      = $request['usuid'];
        $sucursales = $request['sucursales'];
        
        foreach ($sucursales as $sucursal) {
            
            if ($sucursal['seleccionado'] == true) {
                $usu_suc =  ussusuariossucursales::where('usuid', $usuid)   
                                                ->where('sucid', $sucursal['sucid'])
                                                ->first(['ussid']);
        
                if (!$usu_suc) {
                    $usuSucN = new ussusuariossucursales;
                    $usuSucN->usuid = $usuid;
                    $usuSucN->sucid = $sucursal['sucid'];
                    if ($usuSucN->save()) {
                        $respuesta = true;
                        $mensaje   = 'Se agrego correctamente la sucursal al usuario';
                    }else{
                        $respuesta = false;
                        $mensaje   = 'Lo sentimos no se pudo agregar la sucursal al usuario';
                    }
                }
            }else if ($sucursal['seleccionado'] == false) {
                $usu_suc =  ussusuariossucursales::where('usuid', $usuid)   
                                                ->where('sucid', $sucursal['sucid'])
                                                ->first(['ussid']);
                $ussid = 0;
                if ($usu_suc) {
                    $ussid = $usu_suc->ussid;
                    ussusuariossucursales::where('ussid', $ussid)->delete();
                }
            }
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje
        ]);

        return $requestsalida;
    }
}
