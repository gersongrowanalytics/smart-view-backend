<?php

namespace App\Http\Controllers\Sistema\Fechas\Mostrar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\fecfechas;
use App\usuusuarios;
use App\tuptiposusuariospermisos;

class FechasMostrarController extends Controller
{
    public function mostrarFechas(Request $request)
    {
        $usutoken = $request->header('api_token');

        $respuesta      = false;
        $mensaje        = '';
        $datos          = [];
        $linea          = __LINE__;
        $mensajeDetalle = '';
        $mensajedev     = null;
        
        try{

            $usuusuario = usuusuarios::join('tputiposusuarios as tpu', 'tpu.tpuid', 'usuusuarios.tpuid')
                                ->where('usuusuarios.usutoken', $request->header('api_token'))
                                ->first(['usuusuarios.usuid', 'tpu.tpuid', 'tpu.tpuprivilegio']);
            
            if($usuusuario->tpuprivilegio == 'todo'){
                $fecfechas = fecfechas::OrderBy('fecfecha', 'DESC')
                                    ->get([
                                        'fecid',
                                        'fecfecha',
                                        'fecdia',
                                        'fecmes',
                                        'fecano'
                                    ]);
            }else{

                $permisosTodosMeses = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                                            ->where('tuptiposusuariospermisos.tpuid', $usuusuario->tpuid )
                                                            ->where('pem.pemslug', 'fechas.mostrar.todas')
                                                            ->first([
                                                                'pem.pemslug',
                                                                'pem.pemruta'
                                                            ]);

                if($permisosTodosMeses){
                    $fecfechas = fecfechas::OrderBy('fecfecha', 'DESC')
                                    ->get([
                                        'fecid',
                                        'fecfecha',
                                        'fecdia',
                                        'fecmes',
                                        'fecano'
                                    ]);
                }else{
                    $tuptiposusuariospermisos = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                                                        ->where('tuptiposusuariospermisos.tpuid', $usuusuario->tpuid )
                                                                        ->where('pem.pemslug', 'LIKE', 'fechas.mostrar.%')
                                                                        ->get([
                                                                            'pem.pemslug',
                                                                            'pem.pemruta'
                                                                        ]);

                    $tupAnios = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                                            ->where('tuptiposusuariospermisos.tpuid', $usuusuario->tpuid )
                                                            ->where('pem.pemslug', 'LIKE', 'fechas.mostrar.anios.%')
                                                            ->get([
                                                                'pem.pemslug',
                                                                'pem.pemruta'
                                                            ]);

                    $fecfechas = fecfechas::OrderBy('fecfecha', 'DESC')
                                        ->where(function ($query) use($tuptiposusuariospermisos, $tupAnios) {

                                            if(sizeof($tuptiposusuariospermisos) > 0){
                                                foreach($tupAnios as $tupAnio){
                                                    $query->orwhere('fecano', $tupAnio->pemruta);
                                                }
                                            }else{
                                                $query->where('fecano', 'noacceso');
                                            }
                                            
                                        })
                                        ->where(function ($query) use($tuptiposusuariospermisos, $tupAnios) {
                                            if(sizeof($tuptiposusuariospermisos) > 0){
                                                foreach($tuptiposusuariospermisos as $contadorTup => $tuptiposusuariospermiso){
                                                    $query->orwhere('fecmes', $tuptiposusuariospermiso->pemruta);
                                                }
                                            }else{
                                                $query->where('fecmes', 'noacceso');
                                            }
                                        })
                                        ->get([
                                            'fecid',
                                            'fecfecha',
                                            'fecdia',
                                            'fecmes',
                                            'fecano'
                                        ]);
                }
                
            }


            
    
            if(sizeof($fecfechas) > 0){
                $fechas = array();
                
                foreach($fecfechas as $fecfecha){

                    if(sizeof($fechas) == 0){
                        $fechas['anos']  = array($fecfecha->fecano);
                        $fechas['meses'] = array($fecfecha->fecmes);
                        $fechas['dias']  = array($fecfecha->fecdia);
                        
                    }else{
                        for($contador = 0; $contador < sizeof($fechas['anos']); $contador++ ){
                            if($fechas['anos'][$contador] == $fecfecha->fecano ){
                                break;
                            }else if($contador+1 == sizeof($fechas['anos'])){
                                array_push($fechas['anos'], $fecfecha->fecano);
                            }
                        }

                        for($contador = 0; $contador < sizeof($fechas['meses']); $contador++ ){
                            if($fechas['meses'][$contador] == $fecfecha->fecmes ){
                                break;
                            }else if($contador+1 == sizeof($fechas['meses'])){
                                array_push($fechas['meses'], $fecfecha->fecmes);
                            }
                        }

                        for($contador = 0; $contador < sizeof($fechas['dias']); $contador++ ){
                            if($fechas['dias'][$contador] == $fecfecha->fecdia ){
                                break;
                            }else if($contador+1 == sizeof($fechas['dias'])){
                                array_push($fechas['dias'], $fecfecha->fecdia);
                            }
                        }
                    }
                }

                $respuesta      = true;
                $datos          = $fechas;
                $mensaje        = 'Las fechas se cargaron satisfactoriamente.';
                $mensajeDetalle = sizeof($fecfechas).' registros encontrados.';
                $linea          = __LINE__;    
            }else{
                $respuesta      = false;
                $mensaje        = 'Lo sentimos, no se econtraron fechas registradas';
                $mensajeDetalle = sizeof($fecfechas).' registros encontrados.';
                $linea          = __LINE__;    
            }
        } catch (Exception $e) {
            $mensajedev = $e->getMessage();
            $linea      = __LINE__;
        }

        $requestsalida = response()->json([
            "respuesta"      => $respuesta,
            "mensaje"        => $mensaje,
            "datos"          => $datos,
            "linea"          => $linea,
            "mensajeDetalle" => $mensajeDetalle,
            "mensajedev"     => $mensajedev
        ]);
        
        return $requestsalida;
    }
}
