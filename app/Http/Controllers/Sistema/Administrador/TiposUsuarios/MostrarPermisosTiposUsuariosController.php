<?php

namespace App\Http\Controllers\Sistema\Administrador\TiposUsuarios;

use App\Http\Controllers\Controller;
use App\pempermisos;
use App\tuptiposusuariospermisos;
use Illuminate\Http\Request;

class MostrarPermisosTiposUsuariosController extends Controller
{
    public function MostrarPermisosTiposUsuarios(Request $request)
    {
        $respuesta = false;
        $mensaje   = '';
        $arrayPermisosTipoUsuario = [];

        $tpuid = $request['tipo_usuario'];

        $permisosTipoUsuario = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                        ->where('tpuid', $tpuid)
                                        ->get([
                                            'pem.pemid'
                                        ]);
        
        $permisos = pempermisos::get([
                                    'pemid',
                                    'pemslug'
                                ]);

        foreach($permisos as $key => $permiso)
        {
            $arrayPermisosTipoUsuario[$key]['pemid'] = $permiso['pemid'];
            $arrayPermisosTipoUsuario[$key]['pemslug'] = $permiso['pemslug'];
            $arrayPermisosTipoUsuario[$key]['seleccionado'] = false;             
            
            foreach ($permisosTipoUsuario as $permisoTU) {
                if ($permiso['pemid'] == $permisoTU['pemid']) {
                    $arrayPermisosTipoUsuario[$key]['seleccionado'] = true;
                }
            }
        }

        if(sizeof($arrayPermisosTipoUsuario) > 0){
            $respuesta      = true;
            $mensaje        = 'Los permisos del tipo de usuario se cargaron satisfactoriamente';
        }else{
            $respuesta      = false;
            $mensaje        = 'Los permisos del tipo de usuario no se cargaron satisfactoriamente';
        }

        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "permisos"  => $arrayPermisosTipoUsuario
        ]);

        return $requestsalida;
    }
}
