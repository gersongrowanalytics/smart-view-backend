<?php

namespace App\Http\Controllers\Sistema\Administrador\TiposUsuarios;

use App\Http\Controllers\Controller;
use App\pempermisos;
use App\tuptiposusuariospermisos;
use Illuminate\Http\Request;

class EditarPermisosTiposUsuariosController extends Controller
{
    public function EditarPermisosTiposUsuarios(Request $request)
    {
        $respuesta = true;
        $mensaje   = 'Se agrego correctamente todos los permisos al tipo de usuario';

        $tpuid    = $request['tipo_usuario'];
        $permisos_tu = $request['permisos'];

        $per = tuptiposusuariospermisos::where('tpuid', $tpuid)->delete();

        $permisos = pempermisos::get([
            'pemid',
            'pemslug'
        ]);

        if ($per >= 0) {
            foreach ($permisos as $permiso) {
                foreach ($permisos_tu as $permiso_tu) {
                    if ($permiso['pemid'] == $permiso_tu['pemid'] && $permiso_tu['seleccionado'] == true) {
                        $existencia_permiso = tuptiposusuariospermisos::where('tpuid', $tpuid)
                                                                        ->where('pemid', $permiso_tu['pemid'])
                                                                        ->first(['tupid']);
                        if (!$existencia_permiso) {
                            $tupn = new tuptiposusuariospermisos;
                            $tupn->pemid = $permiso_tu['pemid'];
                            $tupn->tpuid = $tpuid;
                            if ($tupn->save()) {
                                $respuesta = true;
                                $mensaje   = 'Se agregaron correctamente todos los permisos del tipo de usuario: '.$tpuid;
                            }else{
                                $respuesta = false;
                                $mensaje   = 'Lo sentimos, no se pudo agregar los permisos del tipo de usuario';
                            }
                        }
                    }
                }
            }
        }else{
            $respuesta = false;
            $mensaje   = 'Lo sentimos, no se pudo eliminar todos los registros de permisos del tipo de usuario';
        }
        
        $pem_tpuid = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                                ->where('tpuid', $tpuid)
                                                ->get([
                                                    'pem.pemid',
                                                    'pem.pemslug'
                                                ]);
        $requestsalida = response()->json([
            "respuesta" => $respuesta,
            "mensaje"   => $mensaje,
            "permisos"  => $pem_tpuid
        ]);

        return $requestsalida;
    }
}
