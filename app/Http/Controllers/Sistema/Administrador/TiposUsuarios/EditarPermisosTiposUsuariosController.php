<?php

namespace App\Http\Controllers\Sistema\Administrador\TiposUsuarios;

use App\Http\Controllers\Controller;
use App\pempermisos;
use App\tputiposusuarios;
use App\tuptiposusuariospermisos;
use Illuminate\Http\Request;

class EditarPermisosTiposUsuariosController extends Controller
{
    public function EditarPermisosTiposUsuarios (Request $request)
    {
        $respuesta = true;
        $mensaje = "";

        $re_tpuid    = $request['re_tpuid'];
        $re_permisos = $request['re_permisos'];
        $re_datosTipoUsuario = $request['re_datosTipoUsuario'];
        $re_editarPermisos = $request['re_editarPermisos'];
        $re_editarTipoUsuario = $request['re_editarTipoUsuario'];

        if($re_tpuid == 0){

            $tpun = new tputiposusuarios;
            $tpun->tpunombre         = $re_datosTipoUsuario['re_nombre'];
            $tpun->estid             = $re_datosTipoUsuario['re_estado'];
            $tpun->tpufechainicio    = $re_datosTipoUsuario['re_fechaInicio'];
            $tpun->tpufechafinal     = $re_datosTipoUsuario['re_fechaFinal'];
            $tpun->tpuimagen         = $re_datosTipoUsuario['re_imagen'];
            $tpun->tpuimagencircular = $re_datosTipoUsuario['re_imagencircular'];
            if ($tpun->save()) {
                $respuesta = true;
                $mensaje = "Los datos del tipo de usuario se crearon correctamente";

                foreach ($re_permisos as $tipoPermiso) {
                    foreach ($tipoPermiso['permisos'] as $permiso) {
                        if ($permiso['seleccionado'] == true) {
                            $tupn = new tuptiposusuariospermisos();
                            $tupn->pemid = $permiso['pemid'];
                            $tupn->tpuid = $tpun->tpuid;
                            if ($tupn->save()) {
                                $respuesta = true;
                                $mensaje = "El tipo de usuario fue creado exitosamente";
                            }else{
                                $respuesta = false;
                                $mensaje = "Error al momento de crear los permisos del tipo de usuario";
                            }
                        }
                    }
                }
            }else{
                $respuesta = false;
                $mensaje = "Lo sentimos no se pudo crear el tipo de usuario";
            }

        }else{

            if ($re_editarPermisos == true && $re_editarTipoUsuario == false) {
                $tupd = tuptiposusuariospermisos::where('tpuid', $re_tpuid)->delete();
                if ($tupd >= 0) {
                    foreach ($re_permisos as $tipoPermiso) {
                        foreach ($tipoPermiso['permisos'] as $permiso) {
                            if ($permiso['seleccionado'] == true) {
                                $tupn = new tuptiposusuariospermisos();
                                $tupn->pemid = $permiso['pemid'];
                                $tupn->tpuid = $re_tpuid;
                                if ($tupn->save()) {
                                    $respuesta = true;
                                    $mensaje = "Los permisos del tipo de usuario seleccionados fueron editados exitosamente";
                                }else{
                                    $respuesta = false;
                                    $mensaje = "Error al momento de editar los permisos del tipo de usuario";
                                }
                            }
                        }
                    }
                }
            }else if($re_editarTipoUsuario == true && $re_editarPermisos == false){
                $tpue = tputiposusuarios::find($re_tpuid);
                if ($tpue) {
                    $tpue->tpunombre         = $re_datosTipoUsuario['re_nombre'];
                    $tpue->estid             = $re_datosTipoUsuario['re_estado'];
                    $tpue->tpufechainicio    = $re_datosTipoUsuario['re_fechaInicio'];
                    $tpue->tpufechafinal     = $re_datosTipoUsuario['re_fechaFinal'];
                    $tpue->tpuimagen         = $re_datosTipoUsuario['re_imagen'];
                    $tpue->tpuimagencircular = $re_datosTipoUsuario['re_imagencircular'];
                    if ($tpue->update()) {
                        $respuesta = true;
                        $mensaje = "Los datos del tipo de usuario se editaron correctamente";
                    }else{
                        $respuesta = false;
                        $mensaje = "Lo sentimos no se pudo editar el tipo de usuario";
                    }
                }
            }

        }
        
        $requestsalida = response()->json([
            "respuesta"    => $respuesta,
            "mensaje"      => $mensaje
        ]);

        return $requestsalida;
    }


    // public function EditarPermisosTiposUsuarios(Request $request)
    // {
    //     $respuesta = true;
    //     $mensaje   = 'Se agrego correctamente todos los permisos al tipo de usuario';

    //     $tpuid    = $request['tipo_usuario'];
    //     $permisos_tu = $request['permisos'];

    //     $per = tuptiposusuariospermisos::where('tpuid', $tpuid)->delete();

    //     $permisos = pempermisos::get([
    //         'pemid',
    //         'pemslug'
    //     ]);

    //     if ($per >= 0) {
    //         foreach ($permisos as $permiso) {
    //             foreach ($permisos_tu as $permiso_tu) {
    //                 if ($permiso['pemid'] == $permiso_tu['pemid'] && $permiso_tu['seleccionado'] == true) {
    //                     $existencia_permiso = tuptiposusuariospermisos::where('tpuid', $tpuid)
    //                                                                     ->where('pemid', $permiso_tu['pemid'])
    //                                                                     ->first(['tupid']);
    //                     if (!$existencia_permiso) {
    //                         $tupn = new tuptiposusuariospermisos;
    //                         $tupn->pemid = $permiso_tu['pemid'];
    //                         $tupn->tpuid = $tpuid;
    //                         if ($tupn->save()) {
    //                             $respuesta = true;
    //                             $mensaje   = 'Se agregaron correctamente todos los permisos del tipo de usuario: '.$tpuid;
    //                         }else{
    //                             $respuesta = false;
    //                             $mensaje   = 'Lo sentimos, no se pudo agregar los permisos del tipo de usuario';
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }else{
    //         $respuesta = false;
    //         $mensaje   = 'Lo sentimos, no se pudo eliminar todos los registros de permisos del tipo de usuario';
    //     }
        
    //     $pem_tpuid = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
    //                                             ->where('tpuid', $tpuid)
    //                                             ->get([
    //                                                 'pem.pemid',
    //                                                 'pem.pemslug'
    //                                             ]);
    //     $requestsalida = response()->json([
    //         "respuesta" => $respuesta,
    //         "mensaje"   => $mensaje,
    //         "permisos"  => $pem_tpuid
    //     ]);

    //     return $requestsalida;
    // }
}
