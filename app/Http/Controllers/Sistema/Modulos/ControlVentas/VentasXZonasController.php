<?php

namespace App\Http\Controllers\Sistema\Modulos\ControlVentas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\sucsucursales;
use App\tsutipospromocionessucursales;
use App\scasucursalescategorias;
use App\ussusuariossucursales;
use App\catcategorias;

class VentasXZonasController extends Controller
{
    public function VentasXZonas(Request $request)
    {

        $anio           = $request['anio'];
        $mes            = $request['mes'];
        $regiones       = $request['regiones'];
        $zonas          = $request['zonas'];
        $grupos         = $request['grupos'];
        $distribuidoras = $request['distribuidoras'];
        $categorias     = $request['categorias'];

        $datos = array(
            array(
                "zona" => "",
                "real" => "",
                "objetivo" => "",

            )
        );

        $zonas = ussusuariossucursales::join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                    ->join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                    ->join('zonzonas as zon', 'zon.zonid', 'usu.zonid')
                                    ->where('usu.estid', 1)
                                    ->distinct('zon.zonid')
                                    ->get([
                                        'zon.zonid',
                                        'zon.zonnombre',
                                    ]);
        
        $contador = 0;

        foreach($zonas as $posicion => $zona){
            $tsusReal = tsutipospromocionessucursales::join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                ->where('suc.zonid', $zona->zonid)
                                                ->get(['tsutipospromocionessucursales.tsuvalorizadoreal']);

            $real = 0;
            foreach($tsusReal as $tsuReal){
                $real = $real + $tsuReal->tsuvalorizadoreal;
            }

            $tsusObje = tsutipospromocionessucursales::join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                ->where('suc.zonid', $zona->zonid)
                                                ->get(['tsuvalorizadoobjetivo']);

            $obj = 0;
            foreach($tsusObje as $tsuObje){
                $obj = $obj + $tsuObje->tsuvalorizadoobjetivo;
            }

                    
            $datos[$contador]['zona'] = $zona->zonnombre;
            $datos[$contador]['real'] = sprintf("%.2f",$real);
            $datos[$contador]['objetivo'] = sprintf("%.2f",$obj);

            $contador = $contador+1;

        }

        return response()->json([
            "datos"     => $datos,
            "respuesta" => true,
        ]);
    }

    public function VentasXControl(Request $request)
    {
        $tprid          = $request['tprid'];

        $anios          = $request['anios'];
        $meses          = $request['meses'];

        $filRegion      = $request['filRegion']; //bool true; false
        $regiones       = $request['regiones'];

        $filZona        = $request['filZona']; //bool true; false
        $zonas          = $request['zonas'];

        $filGrupo       = $request['filGrupo']; //bool true; false
        $grupos         = $request['grupos'];
        
        $filCategoria    = $request['filCategoria']; //bool true; false
        $todasCategorias = $request['todasCategorias'];
        $categorias      = $request['categorias'];

        $respuesta = true;
        $mensaje = "";

        $datos = array(
            array(
                "titulo"   => "",
                "real"     => "",
                "objetivo" => "",
            )
        );

        if($filRegion == true){
            $cass = sucsucursales::join('cascanalessucursales as cas', 'cas.casid', 'sucsucursales.casid')
                                ->where('sucestado', 1)
                                ->distinct('cas.casid')
                                ->get([
                                    'cas.casid',
                                    'casnombre'
                                ]);

            foreach($cass as $posicionCas => $cas){
                $datos[$posicionCas]['titulo'] = $cas->casnombre;

                if($todasCategorias == true ){
                    $sumReal = tsutipospromocionessucursales::join('fecfechas as fec', 'fec.fecid', 'tsutipospromocionessucursales.fecid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                            ->where('suc.casid', $cas->casid)
                                                            ->where('tprid', $tprid)
                                                            ->where(function ($query) use($anios, $meses) {
                                                                
                                                                foreach($anios as $anio){
                                                                    $query->orwhere('fecano', $anio);
                                                                }

                                                                foreach($meses as $mes){
                                                                    $query->orwhere('fecmes', $mes);
                                                                }

                                                            })
                                                            ->sum('tsuvalorizadoreal');

                    $sumObj = tsutipospromocionessucursales::join('fecfechas as fec', 'fec.fecid', 'tsutipospromocionessucursales.fecid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                            ->where('suc.casid', $cas->casid)
                                                            ->where('tprid', $tprid)
                                                            ->where(function ($query) use($anios, $meses) {
                                                                
                                                                foreach($anios as $anio){
                                                                    $query->orwhere('fecano', $anio);
                                                                }

                                                                foreach($meses as $mes){
                                                                    $query->orwhere('fecmes', $mes);
                                                                }

                                                            })
                                                            ->sum('tsuvalorizadoobjetivo');


                }else{
                    $sumReal = scasucursalescategorias::join('fecfechas as fec', 'fec.fecid', 'scasucursalescategorias.fecid')
                                                    ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
                                                    ->join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                    ->where('suc.casid', $cas->casid)
                                                    ->where('tsu.tprid', $tprid)
                                                    ->where(function ($query) use($anios, $meses, $categorias) {
                                                                
                                                        foreach($anios as $anio){
                                                            $query->orwhere('fecano', $anio);
                                                        }

                                                        foreach($meses as $mes){
                                                            $query->orwhere('fecmes', $mes);
                                                        }
                                                        
                                                        foreach($categorias as $categoria){
                                                            $query->orwhere('catid', $categoria);
                                                        }

                                                    })
                                                    ->sum('scavalorizadoreal');

                    $sumObj = scasucursalescategorias::join('fecfechas as fec', 'fec.fecid', 'scasucursalescategorias.fecid')
                                                    ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
                                                    ->join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                    ->where('suc.casid', $cas->casid)
                                                    ->where('tsu.tprid', $tprid)
                                                    ->where(function ($query) use($anios, $meses, $categorias) {
                                                                
                                                        foreach($anios as $anio){
                                                            $query->orwhere('fecano', $anio);
                                                        }

                                                        foreach($meses as $mes){
                                                            $query->orwhere('fecmes', $mes);
                                                        }
                                                        
                                                        foreach($categorias as $categoria){
                                                            $query->orwhere('catid', $categoria);
                                                        }

                                                    })
                                                    ->sum('scavalorizadoobjetivo');

                }

                $datos[$posicionCas]['real']     = $sumReal;
                $datos[$posicionCas]['objetivo'] = $sumObj;

            }
        }else if($filZona == true){
            $zons = sucsucursales::join('zonzonas as zon', 'zon.zonid', 'sucsucursales.zonid')
                                ->where('sucestado', 1)
                                ->distinct('zon.zonid')
                                ->get([
                                    'zon.zonid',
                                    'zonnombre'
                                ]);

            foreach($zons as $posicionZon => $zon){
                $datos[$posicionZon]['titulo'] = $zon->zonnombre;

                if($todasCategorias == true ){
                    $sumReal = tsutipospromocionessucursales::join('fecfechas as fec', 'fec.fecid', 'tsutipospromocionessucursales.fecid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                            ->where('suc.zonid', $zon->zonid)
                                                            ->where('tprid', $tprid)
                                                            ->where(function ($query) use($anios, $meses) {
                                                                
                                                                foreach($anios as $anio){
                                                                    $query->orwhere('fecano', $anio);
                                                                }

                                                                foreach($meses as $mes){
                                                                    $query->orwhere('fecmes', $mes);
                                                                }

                                                            })
                                                            ->sum('tsuvalorizadoreal');

                    $sumObj = tsutipospromocionessucursales::join('fecfechas as fec', 'fec.fecid', 'tsutipospromocionessucursales.fecid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                            ->where('suc.zonid', $zon->zonid)
                                                            ->where('tprid', $tprid)
                                                            ->where(function ($query) use($anios, $meses) {
                                                                
                                                                foreach($anios as $anio){
                                                                    $query->orwhere('fecano', $anio);
                                                                }

                                                                foreach($meses as $mes){
                                                                    $query->orwhere('fecmes', $mes);
                                                                }

                                                            })
                                                            ->sum('tsuvalorizadoobjetivo');


                }else{
                    $sumReal = scasucursalescategorias::join('fecfechas as fec', 'fec.fecid', 'scasucursalescategorias.fecid')
                                                    ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
                                                    ->join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                    ->where('suc.zonid', $zon->zonid)
                                                    ->where('tsu.tprid', $tprid)
                                                    ->where(function ($query) use($anios, $meses, $categorias) {
                                                                
                                                        foreach($anios as $anio){
                                                            $query->orwhere('fecano', $anio);
                                                        }

                                                        foreach($meses as $mes){
                                                            $query->orwhere('fecmes', $mes);
                                                        }
                                                        
                                                        foreach($categorias as $categoria){
                                                            $query->orwhere('catid', $categoria);
                                                        }

                                                    })
                                                    ->sum('scavalorizadoreal');

                    $sumObj = scasucursalescategorias::join('fecfechas as fec', 'fec.fecid', 'scasucursalescategorias.fecid')
                                                    ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
                                                    ->join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                    ->where('suc.zonid', $zon->zonid)
                                                    ->where('tsu.tprid', $tprid)
                                                    ->where(function ($query) use($anios, $meses, $categorias) {
                                                                
                                                        foreach($anios as $anio){
                                                            $query->orwhere('fecano', $anio);
                                                        }

                                                        foreach($meses as $mes){
                                                            $query->orwhere('fecmes', $mes);
                                                        }
                                                        
                                                        foreach($categorias as $categoria){
                                                            $query->orwhere('catid', $categoria);
                                                        }

                                                    })
                                                    ->sum('scavalorizadoobjetivo');

                }

                $datos[$posicionZon]['real']     = $sumReal;
                $datos[$posicionZon]['objetivo'] = $sumObj;
                
            }
        }else if($filGrupo == true){
            $gsus = sucsucursales::join('gsugrupossucursales as gsu', 'gsu.gsuid', 'sucsucursales.gsuid')
                                        ->where('sucestado', 1)
                                        ->distinct('gsu.gsuid')
                                        ->get([
                                            'gsu.gsuid',
                                            'gsunombre'
                                        ]);

            foreach($gsus as $posicionGsu => $gsu){
                $datos[$posicionGsu]['titulo'] = $gsu->gsunombre;

                if($todasCategorias == true ){
                    $sumReal = tsutipospromocionessucursales::join('fecfechas as fec', 'fec.fecid', 'tsutipospromocionessucursales.fecid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                            ->where('suc.gsuid', $gsu->gsuid)
                                                            ->where('tprid', $tprid)
                                                            ->where(function ($query) use($anios, $meses) {
                                                                
                                                                foreach($anios as $anio){
                                                                    $query->orwhere('fecano', $anio);
                                                                }

                                                                foreach($meses as $mes){
                                                                    $query->orwhere('fecmes', $mes);
                                                                }

                                                            })
                                                            ->sum('tsuvalorizadoreal');

                    $sumObj = tsutipospromocionessucursales::join('fecfechas as fec', 'fec.fecid', 'tsutipospromocionessucursales.fecid')
                                                            ->join('sucsucursales as suc', 'suc.sucid', 'tsutipospromocionessucursales.sucid')
                                                            ->where('suc.gsuid', $gsu->gsuid)
                                                            ->where('tprid', $tprid)
                                                            ->where(function ($query) use($anios, $meses) {
                                                                
                                                                foreach($anios as $anio){
                                                                    $query->orwhere('fecano', $anio);
                                                                }

                                                                foreach($meses as $mes){
                                                                    $query->orwhere('fecmes', $mes);
                                                                }

                                                            })
                                                            ->sum('tsuvalorizadoobjetivo');


                }else{
                    $sumReal = scasucursalescategorias::join('fecfechas as fec', 'fec.fecid', 'scasucursalescategorias.fecid')
                                                    ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
                                                    ->join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                    ->where('suc.gsuid', $gsu->gsuid)
                                                    ->where('tsu.tprid', $tprid)
                                                    ->where(function ($query) use($anios, $meses, $categorias) {
                                                                
                                                        foreach($anios as $anio){
                                                            $query->orwhere('fecano', $anio);
                                                        }

                                                        foreach($meses as $mes){
                                                            $query->orwhere('fecmes', $mes);
                                                        }
                                                        
                                                        foreach($categorias as $categoria){
                                                            $query->orwhere('catid', $categoria);
                                                        }

                                                    })
                                                    ->sum('scavalorizadoreal');

                    $sumObj = scasucursalescategorias::join('fecfechas as fec', 'fec.fecid', 'scasucursalescategorias.fecid')
                                                    ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
                                                    ->join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                    ->where('suc.gsuid', $gsu->gsuid)
                                                    ->where('tsu.tprid', $tprid)
                                                    ->where(function ($query) use($anios, $meses, $categorias) {
                                                                
                                                        foreach($anios as $anio){
                                                            $query->orwhere('fecano', $anio);
                                                        }

                                                        foreach($meses as $mes){
                                                            $query->orwhere('fecmes', $mes);
                                                        }
                                                        
                                                        foreach($categorias as $categoria){
                                                            $query->orwhere('catid', $categoria);
                                                        }

                                                    })
                                                    ->sum('scavalorizadoobjetivo');

                }

                $datos[$posicionGsu]['real']     = $sumReal;
                $datos[$posicionGsu]['objetivo'] = $sumObj;
                
            }
        }else if($filCategoria == true){
            $cats = catcategorias::where('catid', '!=', 6)
                                ->get();

            foreach($cats as $posicionCat => $cat){
                $datos[$posicionCat]['titulo'] = $cat->catnombre;

                $sumReal = scasucursalescategorias::join('fecfechas as fec', 'fec.fecid', 'scasucursalescategorias.fecid')
                                                    ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
                                                    ->join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                    ->where(function ($query) use($anios, $meses, $regiones, $zonas, $grupos) {
     
                                                        foreach($anios as $anio){
                                                            $query->orwhere('fecano', $anio)
                                                                ->where(function ($query) use($meses) {
                                                                    foreach($meses as $mes){
                                                                        $query->where('fecmes', $mes);
                                                                    }
                                                                })
                                                                ->where(function ($query) use($regiones) {
                                                                    foreach($regiones as $region){
                                                                        $query->where('suc.casid', $region);
                                                                    }
                                                                })
                                                                ->where(function ($query) use($zonas) {
                                                                    foreach($zonas as $zona){
                                                                        $query->where('suc.zonid', $zona);
                                                                    }
                                                                })
                                                                ->where(function ($query) use($grupos) {
                                                                    foreach($grupos as $grupo){
                                                                        $query->orwhere('suc.gsuid', $grupo);
                                                                    }
                                                                })
                                                                ->where('catid', $cat->catid)
                                                                ->where('tsu.tprid', $tprid);
                                                        }

                                                    })
                                                    ->sum('scavalorizadoreal');

                $sumObj = scasucursalescategorias::join('fecfechas as fec', 'fec.fecid', 'scasucursalescategorias.fecid')
                                                    ->join('sucsucursales as suc', 'suc.sucid', 'scasucursalescategorias.sucid')
                                                    ->join('tsutipospromocionessucursales as tsu', 'tsu.tsuid', 'scasucursalescategorias.tsuid')
                                                    ->where('catid', $cat->catid)
                                                    ->where('tsu.tprid', $tprid)
                                                    ->where(function ($query) use($anios, $meses, $regiones, $zonas, $grupos) {
                                                                
                                                        foreach($anios as $anio){
                                                            $query->orwhere('fecano', $anio);
                                                        }

                                                        foreach($meses as $mes){
                                                            $query->orwhere('fecmes', $mes);
                                                        }
                                                        
                                                        foreach($regiones as $region){
                                                            $query->orwhere('suc.casid', $region);
                                                        }

                                                        foreach($zonas as $zona){
                                                            $query->orwhere('suc.zonid', $zona);
                                                        }

                                                        foreach($grupos as $grupo){
                                                            $query->orwhere('suc.gsuid', $grupo);
                                                        }

                                                    })
                                                    ->sum('scavalorizadoobjetivo');

                $datos[$posicionCat]['real']     = $sumReal;
                $datos[$posicionCat]['objetivo'] = $sumObj;
            }
        }else{
            $respuesta = false;
            $mensaje = "Lo sentimos no encontramos información relacionada a este filtro";
        }

        return response()->json([
            "datos"     => $datos,
            "respuesta" => $respuesta,
            "mensaje" => $mensaje
        ]);
    }
}
