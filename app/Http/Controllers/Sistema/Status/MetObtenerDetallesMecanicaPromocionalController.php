<?php

namespace App\Http\Controllers\Sistema\Status;

use App\dmpdetallemecanicaspromocional;
use App\Http\Controllers\Controller;
use App\zonzonas;
use Illuminate\Http\Request;

class MetObtenerDetallesMecanicaPromocionalController extends Controller
{
    public function MetObtenerDetallesMecanicaPromocional(Request $request)
    {   
        $respuesta = true;
        $mensaje   = "Se obtuvieron con éxito los registros";
        $cantidad_lima = 0;
        $cantidad_sur    = 0;           
        $cantidad_norte  = 0;
        $datos_norte     = [];
        $datos_sur       = [];
        $datos_lima      = [];

        $req_anio = $request['req_anio'];
        $req_mes  = $request['req_mes'];
        $req_zona = $request['req_zona'];

        date_default_timezone_set("America/Lima");
        $mesAbrev = array("ENE","FEBR","MAR","ABR","MAY","JUN","JUL","AGO","SET","OCT","NOV","DIC");
        $mes_numero = array_search($req_mes, $mesAbrev) + 1 ;

        $fecha = "01-".$mes_numero."-".$req_anio;

        $zons_norte = zonzonas::where('zonnombre','Norte')->get(['zonid', 'zonnombre']);
        $zons_sur = zonzonas::where('zonnombre','Sur')->get(['zonid', 'zonnombre']);
        $zons_lima = zonzonas::where('zonnombre','like','%lima%')->get(['zonid', 'zonnombre']);

        // if ($req_zona == 'TODOS') {
            $dmps = dmpdetallemecanicaspromocional::join('carcargasarchivos as car', 'car.carid', 'dmpdetallemecanicaspromocional.carid')
                                                    ->join('fecfechas as fec', 'fec.fecid', 'car.fecid')
                                                    ->join('usuusuarios as usu', 'usu.usuid', 'car.usuid')
                                                    ->join('sucsucursales as suc', 'suc.sucid', 'dmpdetallemecanicaspromocional.sucid')
                                                    ->join('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                                    ->join('badbasedatos as bad', 'bad.badid', 'dmpdetallemecanicaspromocional.badid')
                                                    ->where('fec.fecmes', $req_mes)
                                                    ->where('fec.fecano', $req_anio)
                                                    ->get([
                                                        'bad.badid',
                                                        'bad.badnombre',
                                                        'suc.sucid',
                                                        'suc.sucnombre',
                                                        'suc.zonid',
                                                        'zon.zonnombre',
                                                        'usu.usuusuario',
                                                        'car.created_at'
                                                    ]);
        
        if (sizeof($dmps) > 0) {

            foreach ($dmps as $key => $dmp) {
                $dmp['fecha_creacion'] = $this->MetFormatearFecha($dmp->created_at);
                $dmp['fecha_limite']   = $this->MetFormatearFecha(date("Y-m-t", strtotime($fecha)));
            }  

            //OBTENER CANTIDAD DE REGISTROS POR ZONA NORTE

            foreach ($zons_norte as $key => $norte) {
                if ($req_zona == 'NORTE') {
                    $data_norte = dmpdetallemecanicaspromocional::join('carcargasarchivos as car', 'car.carid', 'dmpdetallemecanicaspromocional.carid')
                                                                    ->join('fecfechas as fec', 'fec.fecid', 'car.fecid')
                                                                    ->join('usuusuarios as usu', 'usu.usuid', 'car.usuid')
                                                                    ->join('sucsucursales as suc', 'suc.sucid', 'dmpdetallemecanicaspromocional.sucid')
                                                                    ->join('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                                                    ->join('badbasedatos as bad', 'bad.badid', 'dmpdetallemecanicaspromocional.badid')
                                                                    ->where('fec.fecmes', $req_mes)
                                                                    ->where('fec.fecano', $req_anio)
                                                                    ->where('zon.zonid', $norte->zonid)
                                                                    ->get([
                                                                        'bad.badid',
                                                                        'bad.badnombre',
                                                                        'suc.sucid',
                                                                        'suc.sucnombre',
                                                                        'suc.zonid',
                                                                        'zon.zonnombre',
                                                                        'usu.usuusuario',
                                                                        'car.created_at'
                                                                    ]);
                    
                    foreach ($data_norte as $key => $norte) {
                        $datos_norte [] = $norte;
                    }
                }
                
                foreach ($dmps as $key => $dmp) {
                    if ($norte->zonid == $dmp->zonid) {
                        $cantidad_norte++;
                    }
                }
            }

            if (sizeof($datos_norte) > 0) {
                foreach ($datos_norte as $key => $norte) {
                    $norte['fecha_creacion'] = $this->MetFormatearFecha($norte->created_at);
                    $norte['fecha_limite']   = $this->MetFormatearFecha(date("Y-m-t", strtotime($fecha)));
                }  
            }

            //OBTENER CANTIDAD DE REGISTROS POR ZONA SUR

            foreach ($zons_sur as $key => $sur) {

                if ($req_zona == 'SUR') {
                    $data_sur = dmpdetallemecanicaspromocional::join('carcargasarchivos as car', 'car.carid', 'dmpdetallemecanicaspromocional.carid')
                                                                    ->join('fecfechas as fec', 'fec.fecid', 'car.fecid')
                                                                    ->join('usuusuarios as usu', 'usu.usuid', 'car.usuid')
                                                                    ->join('sucsucursales as suc', 'suc.sucid', 'dmpdetallemecanicaspromocional.sucid')
                                                                    ->join('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                                                    ->join('badbasedatos as bad', 'bad.badid', 'dmpdetallemecanicaspromocional.badid')
                                                                    ->where('fec.fecmes', $req_mes)
                                                                    ->where('fec.fecano', $req_anio)
                                                                    ->where('zon.zonid', $sur->zonid)
                                                                    ->get([
                                                                        'bad.badid',
                                                                        'bad.badnombre',
                                                                        'suc.sucid',
                                                                        'suc.sucnombre',
                                                                        'suc.zonid',
                                                                        'zon.zonnombre',
                                                                        'usu.usuusuario',
                                                                        'car.created_at'
                                                                    ]);

                    foreach ($data_sur as $key => $sur) {
                        $datos_sur [] = $sur;
                    }
                    
                }

                foreach ($dmps as $key => $dmp) {
                    if ($sur->zonid == $dmp->zonid) {
                        $cantidad_sur++;
                    }
                }
            }
            
            if (sizeof($datos_sur) > 0) {
                foreach ($datos_sur as $key => $sur) {
                    $sur['fecha_creacion'] = $this->MetFormatearFecha($sur->created_at);
                    $sur['fecha_limite']   = $this->MetFormatearFecha(date("Y-m-t", strtotime($fecha)));
                }  
            }

            //OBTENER CANTIDAD DE REGISTROS POR ZONA LIMA

            foreach ($zons_lima as $key => $lima) {

                if ($req_zona == 'LIMA') {
                    $data_lima = dmpdetallemecanicaspromocional::join('carcargasarchivos as car', 'car.carid', 'dmpdetallemecanicaspromocional.carid')
                                                                    ->join('fecfechas as fec', 'fec.fecid', 'car.fecid')
                                                                    ->join('usuusuarios as usu', 'usu.usuid', 'car.usuid')
                                                                    ->join('sucsucursales as suc', 'suc.sucid', 'dmpdetallemecanicaspromocional.sucid')
                                                                    ->join('zonzonas as zon', 'zon.zonid', 'suc.zonid')
                                                                    ->join('badbasedatos as bad', 'bad.badid', 'dmpdetallemecanicaspromocional.badid')
                                                                    ->where('fec.fecmes', $req_mes)
                                                                    ->where('fec.fecano', $req_anio)
                                                                    ->where('zon.zonid', $lima->zonid)
                                                                    ->get([
                                                                        'bad.badid',
                                                                        'bad.badnombre',
                                                                        'suc.sucid',
                                                                        'suc.sucnombre',
                                                                        'suc.zonid',
                                                                        'zon.zonnombre',
                                                                        'usu.usuusuario',
                                                                        'car.created_at'
                                                                    ]);
                    

                    foreach ($data_lima as $key => $lima) {
                        $datos_lima [] = $lima;
                    }
                }

                foreach ($dmps as $key => $dmp) {
                    if ($lima->zonid == $dmp->zonid) {
                        $cantidad_lima++;
                    }
                }
            }

            if (sizeof($datos_lima) > 0) {
                foreach ($datos_lima as $key => $lima) {
                    $lima['fecha_creacion'] = $this->MetFormatearFecha($lima->created_at);
                    $lima['fecha_limite']   = $this->MetFormatearFecha(date("Y-m-t", strtotime($fecha)));
                }  
            }
                        
        }else{
            $respuesta = false;
            $mensaje   = "Lo siento, no se encontraron registros";
            $datos     = [];
        }

        if ($req_zona == 'NORTE') {
            $datos = $datos_norte;
        }else if($req_zona == 'SUR'){
            $datos = $datos_sur;
        }else if($req_zona == 'LIMA'){
            $datos = $datos_lima;
        }else{
            $datos = $dmps;
        }


        return response()->json([
            "respuesta"       => $respuesta,
            "mensaje"         => $mensaje,
            "datos"           => $datos,
            "total_mecanicas" => $cantidad_norte + $cantidad_lima + $cantidad_sur,
            "cantidad_norte"  => $cantidad_norte,
            "cantidad_lima"   => $cantidad_lima,
            "cantidad_sur"    => $cantidad_sur
        ]);
    }

    public function MetFormatearFecha($fecha)
    {
        $meses = array("Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Set","Oct","Nov","Dic");
        $anioActualizacion = date("Y", strtotime($fecha));
        $mesActualizacion = $meses[date('n', strtotime($fecha))-1];
        $diaActualizacion = date("j", strtotime($fecha));
        $fechaFormateada = $diaActualizacion." ".$mesActualizacion." ".$anioActualizacion;
        return $fechaFormateada;
    }
}
