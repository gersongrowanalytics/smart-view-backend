<?php

namespace App\Http\Controllers\Sistema\ListaPrecios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ltplistaprecios;
use App\ussusuariossucursales;
use App\usuusuarios;
use App\tretiposrebates;
use App\tuptiposusuariospermisos;
use App\Http\Controllers\Sistema\ListaPrecios\ArmarFiltrosListaPreciosController;


class ArmarExcelListapreciosController extends Controller
{
    public function ObtenerGruposPermitidos(Request $request)
    {
        $usutoken = $request->header('api_token');

        $tres = ussusuariossucursales::join('usuusuarios as usu', 'usu.usuid', 'ussusuariossucursales.usuid')
                                    ->join('sucsucursales as suc', 'suc.sucid', 'ussusuariossucursales.sucid')
                                    ->join('tretiposrebates as tre', 'suc.treid', 'tre.treid')
                                    ->where('usu.usutoken', $usutoken)
                                    ->distinct('suc.treid')
                                    ->get([
                                        'suc.treid',
                                        'usu.tpuid',
                                        'trenombre'
                                    ]);

        $usu = usuusuarios::where('usutoken', $usutoken)->first();

        if($usu){

            if($usu->tpuid == 1){
                $tres = tretiposrebates::orwhere('trenombre', 'ZA')
                                        ->orwhere('trenombre', 'ZB')
                                        ->orwhere('trenombre', 'ZC')
                                        ->get();

            }else{

                $tup = tuptiposusuariospermisos::join('pempermisos as pem', 'pem.pemid', 'tuptiposusuariospermisos.pemid')
                                                ->where('tpuid', $usu->tpuid)
                                                ->where('pemslug', 'listaprecios.mostrar.todos.grupostres')
                                                ->first();

                if($tup){
                    $tres = tretiposrebates::orwhere('trenombre', 'ZA')
                                        ->orwhere('trenombre', 'ZB')
                                        ->orwhere('trenombre', 'ZC')
                                        ->get();
                }

            }

        }

        return response()->json([
            'data' => $tres
        ]);

                                    
    }
    
    public function ArmarExcelListaprecios(Request $request)
    {

        $re_treid = $request['treid'];
        $re_tresid = $request['tresid'];
        // $re_treid = 26;
        $usutoken = $request->header('api_token');
        $re_anio = $request['anio'];
        $re_mes  = $request['mes'];
        $re_dia  = $request['dia'];

        $re_duplicados = $request['duplicados'];
        $re_duplicados_productos = $request['duplicadosProductos'];

        $re_columnas  = $request['columnas'];

        if($re_duplicados == true){

            $ltps = ltplistaprecios::join('fecfechas as fec', 'fec.fecid', 'ltplistaprecios.fecid')
                                ->join('proproductos as pro', 'pro.proid', 'ltplistaprecios.proid')
                                ->join('catcategorias as cat', 'cat.catid', 'pro.catid')
                                ->join('tretiposrebates as tre', 'tre.treid', 'ltplistaprecios.treid')
                                ->where('fecano', $re_anio)
                                ->where('fecmes', $re_mes)
                                ->where('fecdia', $re_dia)
                                ->where(function ($query) use($re_duplicados_productos) {

                                    if($re_duplicados_productos == true) {
                                        $query->where('ltplistaprecios.ltptienezona', true);
                                    }

                                })
                                ->where(function ($query) use($re_duplicados) {

                                    if($re_duplicados == true) {
                                        $query->where('ltplistaprecios.ltpduplicadocomplejo', true);
                                    }

                                })
                                ->where(function ($query) use($re_tresid, $re_treid) {
                                    if(isset($re_tresid)){
                                        if(sizeof($re_tresid) > 0){
                                            foreach($re_tresid as $tre){
    
                                                if(isset($tre['seleccionadoFiltro'])){
                                                    if($tre['seleccionadoFiltro'] == true){
                                                        $query->orwhere('tre.treid', $tre['treid']);
                                                    }
                                                }
                                            }
                                        }else{
                                            $query->where('tre.treid', $re_treid);
                                        }
                                    }else{
                                        $query->where('tre.treid', $re_treid);
                                    }
                                })
                                ->orderby('ltpcodigosap')
                                // ->where('treid', $re_treid)
                                // ->paginate(1000);
                                ->paginate(70);
                                // ->get([
                                //     'cat.catnombre',
                                //     'pronombre',
                                //     'proformato',
                                //     'ltpid',
                                //     'ltpcategoria',
                                //     'ltpsubcategoria',
                                //     'ltpcodigosap',
                                //     'ltpean',
                                //     'ltpdescripcionproducto',
                                //     'ltpunidadventa',
                                //     'ltppreciolistasinigv',
                                //     'ltpalza',
                                //     'ltpsdtpr',
                                //     'ltppreciolistaconigv',

                                //     'ltpmfrutamayorista',
                                //     'ltpreventamayorista',
                                //     'ltpmargenmayorista',
                                //     'ltpmarcajemayorista',

                                //     // MINORISTA
                                //     'ltpmfrutaminorista',
                                //     'ltpreventaminorista',
                                //     'ltpmargenminorista',
                                //     'ltpmarcajeminorista',

                                //     // BODEGA
                                //     'ltpmfrutahorizontal',
                                //     'ltpreventabodega',
                                //     'ltpmargenbodega',
                                //     'ltppvp',

                                //     'ltplistaprecios.treid',
                                //     'ltplistaprecios.fecid'
                                // ]);

        }else{

            $ltps = ltplistaprecios::join('fecfechas as fec', 'fec.fecid', 'ltplistaprecios.fecid')
                                ->join('proproductos as pro', 'pro.proid', 'ltplistaprecios.proid')
                                ->join('catcategorias as cat', 'cat.catid', 'pro.catid')
                                ->join('tretiposrebates as tre', 'tre.treid', 'ltplistaprecios.treid')
                                ->where('fecano', $re_anio)
                                ->where('fecmes', $re_mes)
                                ->where('fecdia', $re_dia)
                                ->where(function ($query) use($re_duplicados_productos) {

                                    if($re_duplicados_productos == true) {
                                        $query->where('ltplistaprecios.ltptienezona', true);
                                    }

                                })
                                ->where(function ($query) use($re_duplicados) {

                                    if($re_duplicados == true) {
                                        $query->where('ltplistaprecios.ltpduplicadocomplejo', true);
                                    }

                                })
                                ->where(function ($query) use($re_tresid, $re_treid) {
                                    if(isset($re_tresid)){
                                        if(sizeof($re_tresid) > 0){
                                            foreach($re_tresid as $tre){
                                                if(isset($tre['seleccionadoFiltro'])){
                                                    if($tre['seleccionadoFiltro'] == true){
                                                        $query->orwhere('tre.treid', $tre['treid']);
                                                    }
                                                }
                                            }
                                        }else{
                                            $query->where('tre.treid', $re_treid);
                                        }
                                    }else{
                                        $query->where('tre.treid', $re_treid);
                                    }
                                })
                                // ->where('treid', $re_treid)
                                // ->paginate(1000);
                                ->paginate(70);
                                // ->get([
                                //     'cat.catnombre',
                                //     'pronombre',
                                //     'proformato',
                                //     'ltpid',
                                //     'ltpcategoria',
                                //     'ltpsubcategoria',
                                //     'ltpcodigosap',
                                //     'ltpean',
                                //     'ltpdescripcionproducto',
                                //     'ltpunidadventa',
                                //     'ltppreciolistasinigv',
                                //     'ltpalza',
                                //     'ltpsdtpr',
                                //     'ltppreciolistaconigv',

                                //     'ltpmfrutamayorista',
                                //     'ltpreventamayorista',
                                //     'ltpmargenmayorista',
                                //     'ltpmarcajemayorista',

                                //     // MINORISTA
                                //     'ltpmfrutaminorista',
                                //     'ltpreventaminorista',
                                //     'ltpmargenminorista',
                                //     'ltpmarcajeminorista',

                                //     // BODEGA
                                //     'ltpmfrutahorizontal',
                                //     'ltpreventabodega',
                                //     'ltpmargenbodega',
                                //     'ltppvp',

                                //     'ltplistaprecios.treid',
                                //     'ltplistaprecios.fecid'
                                // ]);

        }

        

        // QUERY PARA EL EXCEL
        $ltpsExcel = ltplistaprecios::join('fecfechas as fec', 'fec.fecid', 'ltplistaprecios.fecid')
                                ->join('proproductos as pro', 'pro.proid', 'ltplistaprecios.proid')
                                ->join('catcategorias as cat', 'cat.catid', 'pro.catid')
                                ->join('tretiposrebates as tre', 'tre.treid', 'ltplistaprecios.treid')
                                ->where('fecano', $re_anio)
                                ->where('fecmes', $re_mes)
                                ->where('fecdia', $re_dia)
                                ->where(function ($query) use($re_duplicados) {

                                    if($re_duplicados == true) {
                                        $query->where('ltplistaprecios.ltpduplicadocomplejo', true);
                                    }

                                })
                                ->where(function ($query) use($re_tresid, $re_treid) {
                                    if(isset($re_tresid)){
                                        if(sizeof($re_tresid) > 0){
                                            foreach($re_tresid as $tre){
    
                                                if(isset($tre['seleccionadoFiltro'])){
                                                    if($tre['seleccionadoFiltro'] == true){
                                                        $query->orwhere('tre.treid', $tre['treid']);
                                                    }
                                                }
                                            }
                                        }else{
                                            $query->where('tre.treid', $re_treid);
                                        }
                                    }else{
                                        $query->where('tre.treid', $re_treid);
                                    }
                                })
                                ->orderby('ltpcodigosap')
                                ->paginate(500);




        $dataReal = $ltps;

        $nuevoArray = array(
            array(
                "columns" => [],
                "data"    => []
            )
        );

        $tituloHojaExcel = "";

        if($re_treid == 26){
            $tituloHojaExcel = "A - ESTRATEGICO";
        }else if($re_treid == 15){
            $tituloHojaExcel = "B - TÁCTICO";
        }else if($re_treid == 24){
            $tituloHojaExcel = "C - BROKER";
        }


        $cabeceras = [
            "CAMBIO",
            "Categoría",
            "Subcategoría",
            "Código SAP",
            "EAN",
            "Descripción de producto",
            // "Unidad de venta",
            "Und. Venta",
            // "Precio Lista Sin IGV",
            "Precio Lista S/IGV",
            "% Alza",
            "SD/TPR",
            "Precio Lista C/IGV",
            "MF Ruta Mayorista",
            "Reventa Mayorista",
            "Margen Mayorista",
            "Marcaje Mayorista",
            "",
            "MF Ruta Minorista",
            "Reventa Minorista",
            "Margen Minorista",
            "Marcaje Minorista",
            "",
            "MF Ruta Horizontal",
            "Reventa Bodega",
            "Margen Bodega",
            "PVP"
        ];

        foreach($ltps as $posicionLtp => $ltp){
            $ltps[$posicionLtp]['mostrar'] = true;
        }

        foreach($ltpsExcel as $posicionLtp => $ltp){

            // $ltps[$posicionLtp]['mostrar'] = true;

            if($posicionLtp == 0){


                $arrayFilaExcel = array( // FILA 2
                    array(),
                    array(
                        "value" => $tituloHojaExcel,
                        "style" => array(
                            "font" => array(
                                "sz" => "20",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            )
                            
                        )
                    ),
                );

                $nuevoArray[0]['data'][] = $arrayFilaExcel;

                $arrayFilaExcel = array(); //FILA 3
                $arrayFilaExcel[] = array();

                if(isset($re_columnas)){

                    
                    $rptaArmar = $this->ArmarCabecerasFilaTres($re_columnas, $arrayFilaExcel);
                    
                    $arrayFilaExcel = $rptaArmar["arrayFilaExcel"];
                    $re_columnas   = $rptaArmar["nuevasColumnas"];


                }else{
                    $arrayFilaExcel = array( //FILA 3
                        array(),
                        array(),
                        array(),
                        array(),
                        array(),
                        array(),
                        array(),
                        array(),
                        array(),
                        array(),
                        array(),
                        array(
                            "value" => "",
                            "style" => array(
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                )
                            )
                        ),
                        array(
                            "value" => "MAYORISTA",
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                    "vertical" => "right",
                                    "horizontal" => "right",
                                    "readingOrder" => 3
                                )
                            )
                        ),
                        array(
                            "value" => "",
                            "style" => array(
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                )
                            )
                        ),
                        array(
                            "value" => "",
                            "style" => array(
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                )
                            )
                        ),
                        array(),
                        array(
                            "value" => "",
                            "style" => array(
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                )
                            )
                        ),
                        array(
                            "value" => "MINORISTA",
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                    "vertical" => "right",
                                    "horizontal" => "right",
                                    "readingOrder" => 3
                                )
                                
                            )
                        ),
    
                        array(
                            "value" => "",
                            "style" => array(
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                )
                            )
                        ),
                        array(
                            "value" => "",
                            "style" => array(
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                )
                            )
                        ),
                        array(),
                        array(
                            "value" => "",
                            "style" => array(
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                )
                            )
                        ),
                        array(
                            "value" => "BODEGA",
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                    "vertical" => "right",
                                    "horizontal" => "right",
                                    "readingOrder" => 3
                                )
                            )
                        ),
                        array(
                            "value" => "",
                            "style" => array(
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                )
                            )
                        ),
                        array(
                            "value" => "",
                            "style" => array(
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "alignment" => array(
                                    "wrapText" => true,
                                )
                            )
                        ),
    
                    );
                }

                $nuevoArray[0]['data'][] = $arrayFilaExcel;







                $arrayFilaExcel = array( // FILA 4
                );
                $nuevoArray[0]['data'][] = $arrayFilaExcel;


                $arrayFilaExcel = array();// FILA 5


                if(isset($re_columnas)){

                    $coloLetra = "FFFFFFFF";
                    $colorFondo = "FF44546A";

                    $arrayFilaExcel[] = array(
                        "value" => "CAMBIO",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => $coloLetra
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => $colorFondo
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                            
                        )
                    );


                    foreach($re_columnas as $re_columna){

                        if($re_columna['columna'] == "MF Ruta Mayorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($re_columna['columna'] == "Reventa Mayorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($re_columna['columna'] == "Margen Mayorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($re_columna['columna'] == "Marcaje Mayorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($re_columna['columna'] == "MF Ruta Minorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($re_columna['columna'] == "Reventa Minorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($re_columna['columna'] == "Margen Minorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($re_columna['columna'] == "Marcaje Minorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($re_columna['columna'] == "MF Ruta Horizontal"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($re_columna['columna'] == "Reventa Bodega"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($re_columna['columna'] == "Margen Bodega"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($re_columna['columna'] == "PVP"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else{
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF44546A";
                        }

                        if($re_columna['columna'] != "ESPACIO"){
                            $arrayFilaExcel[] = array(
                                "value" => $re_columna['columna'],
                                "style" => array(
                                    "font" => array(
                                        "sz" => "11",
                                        "bold" => true,
                                        "color" => array(
                                            "rgb" => $coloLetra
                                        )
                                    ),
                                    "fill" => array(
                                        "patternType" => 'solid',
                                        "fgColor" => array(
                                            "rgb" => $colorFondo
                                        )
                                    ),
                                    "alignment" => array(
                                        "vertical" => "center",
                                        "horizontal" => "center"
                                    )
                                    
                                )
                            );
                        }else{
                            $arrayFilaExcel[] = array(
                                "value" => "",
                            );
                        }

                    }



                }else{
                    
                    foreach($cabeceras as $cabecera){

                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF44546A";
    
                        if($cabecera == "MF Ruta Mayorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($cabecera == "Reventa Mayorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($cabecera == "Margen Mayorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($cabecera == "Marcaje Mayorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($cabecera == "MF Ruta Minorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($cabecera == "Reventa Minorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($cabecera == "Margen Minorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($cabecera == "Marcaje Minorista"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($cabecera == "MF Ruta Horizontal"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($cabecera == "Reventa Bodega"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF70AD47";
                        }else if($cabecera == "Margen Bodega"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }else if($cabecera == "PVP"){
                            $coloLetra = "FFFFFFFF";
                            $colorFondo = "FF4472C4";
                        }
    
                        $arrayFilaExcel[] = array(
                            "value" => $cabecera,
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => $coloLetra
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => $colorFondo
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                )
                                
                            )
                        );
    
                    }

                }

                $nuevoArray[0]['data'][] = $arrayFilaExcel;


                // TITULOS

                if(isset($re_columnas)){

                    $arrayTitulos = array();
                    
                    $arrayTitulos[] = array("title" => "", "width" => array("wpx" => 0));

                    foreach($re_columnas as $re_columna){
                        if($re_columna['columna'] == "ESPACIO"){
                            $arrayTitulos[] = array("title" => "", "width" => array("wpx" => 20));
                        }else{
                            $arrayTitulos[] = array("title" => "", "width" => array("wpx" => 120));
                        }
                    }

                }else{
                    $arrayTitulos = array(
                        array("title" => "", "width" => array("wpx" => 0)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 40)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 40)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
                        array("title" => "", "width" => array("wpx" => 110)),
    
                    );
                }

                $nuevoArray[0]['columns'] = $arrayTitulos;

            }

            $arrayFilaExcel = array();

            if(isset($re_columnas)){

                $arrayFilaExcel[] = array(
                    "value" => "-",
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                );

                foreach($re_columnas as $re_columna){
                    if($re_columna['columna'] == "Categoría" ){
    
    
                        $arrayFilaExcel[] = array(
                            "value" => $ltp->ltpcategoria,
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                )
                                
                            )
                        );
    
    
                    }else if($re_columna['columna'] == "Subcategoría" ){
    
    
                        $arrayFilaExcel[] = array(
                            "value" => $ltp->ltpsubcategoria,
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                )
                            )
                        );
    
    
                    }else if($re_columna['columna'] == "Código SAP" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => $ltp->ltpcodigosap,
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                )
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "EAN" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => $ltp->ltpean,
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                )
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Descripción de producto" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => $ltp->ltpdescripcionproducto,
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                )
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Und. Venta" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpunidadventa),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Precio Lista S/IGV" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltppreciolistasinigv),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "% Alza" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpalza),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "SD/TPR" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpsdtpr),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Precio Lista C/IGV" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltppreciolistaconigv),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => false,
                                    "color" => array(
                                        "rgb" => "FF000000"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "MF Ruta Mayorista" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpmfrutamayorista),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF70AD47"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
    
                    }else if($re_columna['columna'] == "Reventa Mayorista" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpreventamayorista),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF70AD47"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFE2EFDA"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Margen Mayorista" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpmargenmayorista),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF4472C4"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Marcaje Mayorista" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpmarcajemayorista),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF4472C4"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFD9E1F2"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "MF Ruta Minorista" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpmfrutaminorista),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF70AD47"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Reventa Minorista" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpreventaminorista),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF70AD47"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFE2EFDA"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Margen Minorista" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpmargenminorista),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF4472C4"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Marcaje Minorista" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpmarcajeminorista),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF4472C4"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFD9E1F2"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "MF Ruta Horizontal" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpmfrutahorizontal),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF70AD47"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Reventa Bodega" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpreventabodega),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF70AD47"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFE2EFDA"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "Margen Bodega" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltpmargenbodega),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF4472C4"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFFFFFFF"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "PVP" ){
    
                        $arrayFilaExcel[] = array(
                            "value" => floatval($ltp->ltppvp),
                            "style" => array(
                                "font" => array(
                                    "sz" => "11",
                                    "bold" => true,
                                    "color" => array(
                                        "rgb" => "FF4472C4"
                                    )
                                ),
                                "fill" => array(
                                    "patternType" => 'solid',
                                    "fgColor" => array(
                                        "rgb" => "FFD9E1F2"
                                    )
                                ),
                                "alignment" => array(
                                    "vertical" => "center",
                                    "horizontal" => "center"
                                ),
                                "numFmt" => "#,##0.00"
                                
                            )
                        );
    
                    }else if($re_columna['columna'] == "ESPACIO" ){
                        $arrayFilaExcel[] = array(
                            "value" => "",
                        );
                    }
                }

            }else{
                $arrayFilaExcel = array(
                    array(
                        "value" => "-",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                            
                        )
                    ),
                    array(
                        "value" => $ltp->ltpcategoria,
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                            
                        )
                    ),
                    array(
                        "value" => $ltp->ltpsubcategoria,
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                        )
                    ),
                    array(
                        "value" => $ltp->ltpcodigosap,
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                            
                        )
                    ),
                    array(
                        "value" => $ltp->ltpean,
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                            
                        )
                    ),
                    array(
                        "value" => $ltp->ltpdescripcionproducto,
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpunidadventa),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltppreciolistasinigv),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpalza),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpsdtpr),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltppreciolistaconigv),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => false,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpmfrutamayorista),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF70AD47"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpreventamayorista),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF70AD47"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFE2EFDA"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpmargenmayorista),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF4472C4"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpmarcajemayorista),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF4472C4"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFD9E1F2"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => "-",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpmfrutaminorista),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF70AD47"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpreventaminorista),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF70AD47"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFE2EFDA"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpmargenminorista),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF4472C4"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpmarcajeminorista),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF4472C4"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFD9E1F2"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => "-",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpmfrutahorizontal),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF70AD47"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpreventabodega),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF70AD47"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFE2EFDA"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltpmargenbodega),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF4472C4"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                    array(
                        "value" => floatval($ltp->ltppvp),
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF4472C4"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFD9E1F2"
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            ),
                            "numFmt" => "#,##0.00"
                            
                        )
                    ),
                );
            }

            $nuevoArray[0]['data'][] = $arrayFilaExcel;

        }

        $armarFiltrosListaPreciosController = new ArmarFiltrosListaPreciosController;
        $armarFiltros  = $armarFiltrosListaPreciosController->ArmarFiltrosListaPrecios($ltps);

        

        return response()->json([
            'excel' => $nuevoArray,
            'data' => $ltps,
            'dataReal' => $dataReal,
            'arr_filtro_customer_group_lp' => $armarFiltros['arr_filtro_customer_group_lp'],
            'arr_filtro_categorias_lp'     => $armarFiltros['arr_filtro_categorias_lp'],
            'arr_filtro_subcategorias_lp'  => $armarFiltros['arr_filtro_subcategorias_lp'],
            'arr_filtro_formato_lp'        => $armarFiltros['arr_filtro_formato_lp'],
            'arr_filtro_codsap_lp'         => $armarFiltros['arr_filtro_codsap_lp'],
            'arr_filtro_materiales_lp'     => $armarFiltros['arr_filtro_materiales_lp'],
        ]);

    }


    public function ArmarExcelListapreciosBK(Request $request)
    {

        $re_treid = $request['treid'];
        // $re_treid = 26;
        $usutoken = $request->header('api_token');
        $re_anio = $request['anio'];
        $re_mes  = $request['mes'];
        $re_dia  = $request['dia'];

        $ltps = ltplistaprecios::join('fecfechas as fec', 'fec.fecid', 'ltplistaprecios.fecid')
                                ->join('proproductos as pro', 'pro.proid', 'ltplistaprecios.proid')
                                ->join('catcategorias as cat', 'cat.catid', 'pro.catid')
                                ->where('fecano', $re_anio)
                                ->where('fecmes', $re_mes)
                                ->where('fecdia', $re_dia)
                                ->where('treid', $re_treid)
                                ->get([
                                    'cat.catnombre',
                                    'pronombre',
                                    'ltpid',
                                    'ltpcategoria',
                                    'ltpsubcategoria',
                                    'ltpcodigosap',
                                    'ltpean',
                                    'ltpdescripcionproducto',
                                    'ltpunidadventa',
                                    'ltppreciolistasinigv',
                                    'ltpalza',
                                    'ltpsdtpr',
                                    'ltppreciolistaconigv',

                                    'ltpmfrutamayorista',
                                    'ltpreventamayorista',
                                    'ltpmargenmayorista',
                                    'ltpmarcajemayorista',

                                    // MINORISTA
                                    'ltpmfrutaminorista',
                                    'ltpreventaminorista',
                                    'ltpmargenminorista',
                                    'ltpmarcajeminorista',

                                    // BODEGA
                                    'ltpmfrutahorizontal',
                                    'ltpreventabodega',
                                    'ltpmargenbodega',
                                    'ltppvp',

                                    'ltplistaprecios.treid',
                                    'ltplistaprecios.fecid'
                                ]);

        $nuevoArray = array(
            array(
                "columns" => [],
                "data"    => []
            )
        );

        $tituloHojaExcel = "";

        if($re_treid == 26){
            $tituloHojaExcel = "A - ESTRATEGICO";
        }else if($re_treid == 15){
            $tituloHojaExcel = "B - TÁCTICO";
        }else if($re_treid == 24){
            $tituloHojaExcel = "C - BROKER";
        }


        $cabeceras = [
            "CAMBIO",
            "Categoría",
            "Subcategoría",
            "Código SAP",
            "EAN",
            "Descripción de producto",
            "Unidad de venta",
            "Precio Lista Sin IGV",
            "% Alza",
            "SD / TPR",
            "Precio Lista con IGV",
            "MF Ruta Mayorista",
            "Reventa Mayorista",
            "Margen Mayorista",
            "Marcaje Mayorista",
            "",
            "MF Ruta Minorista",
            "Reventa Minorista",
            "Margen Minorista",
            "Marcaje Minorista",
            "",
            "MF Ruta Horizontal",
            "Reventa Bodega",
            "Margen Bodega",
            "PVP"
        ];

        foreach($ltps as $posicionLtp => $ltp){

            if($posicionLtp == 0){

                $arrayTitulos = array(
                    array("title" => "", "width" => array("wpx" => 0)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 40)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 40)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),
                    array("title" => "", "width" => array("wpx" => 110)),

                );

                $nuevoArray[0]['columns'] = $arrayTitulos;

                $arrayFilaExcel = array( // FILA 2
                    array(),
                    array(
                        "value" => $tituloHojaExcel,
                        "style" => array(
                            "font" => array(
                                "sz" => "20",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            )
                            
                        )
                    ),
                );

                $nuevoArray[0]['data'][] = $arrayFilaExcel;

                $arrayFilaExcel = array( //FILA 3
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(),
                    array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    ),
                    array(
                        "value" => "MAYORISTA",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                                "vertical" => "right",
                                "horizontal" => "right",
                                "readingOrder" => 3
                            )
                        )
                    ),
                    array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    ),
                    array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    ),
                    array(),
                    array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    ),
                    array(
                        "value" => "MINORISTA",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                                "vertical" => "right",
                                "horizontal" => "right",
                                "readingOrder" => 3
                            )
                            
                        )
                    ),

                    array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    ),
                    array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    ),
                    array(),
                    array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    ),
                    array(
                        "value" => "BODEGA",
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                                "vertical" => "right",
                                "horizontal" => "right",
                                "readingOrder" => 3
                            )
                        )
                    ),
                    array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    ),
                    array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    ),

                );

                $nuevoArray[0]['data'][] = $arrayFilaExcel;

                $arrayFilaExcel = array( // FILA 4
                );
                $nuevoArray[0]['data'][] = $arrayFilaExcel;


                $arrayFilaExcel = array(); // FILA 5

                foreach($cabeceras as $cabecera){

                    $coloLetra = "FFFFFFFF";
                    $colorFondo = "FF44546A";
                    
                    if($cabecera == "MF Ruta Mayorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Reventa Mayorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Margen Mayorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "Marcaje Mayorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "MF Ruta Minorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Reventa Minorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Margen Minorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "Marcaje Minorista"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "MF Ruta Horizontal"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Reventa Bodega"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF70AD47";
                    }else if($cabecera == "Margen Bodega"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }else if($cabecera == "PVP"){
                        $coloLetra = "FFFFFFFF";
                        $colorFondo = "FF4472C4";
                    }

                    $arrayFilaExcel[] = array(
                        "value" => $cabecera,
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => $coloLetra
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => $colorFondo
                                )
                            ),
                            "alignment" => array(
                                "vertical" => "center",
                                "horizontal" => "center"
                            )
                            
                        )
                    );
                }
                $nuevoArray[0]['data'][] = $arrayFilaExcel;
            }

            $arrayFilaExcel = array(
                array(
                    "value" => "-",
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => $ltp->ltpcategoria,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => $ltp->ltpsubcategoria,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                    )
                ),
                array(
                    "value" => $ltp->ltpcodigosap,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => $ltp->ltpean,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => $ltp->ltpdescripcionproducto,
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpunidadventa),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltppreciolistasinigv),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpalza),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpsdtpr),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltppreciolistaconigv),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => false,
                            "color" => array(
                                "rgb" => "FF000000"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmfrutamayorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpreventamayorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFE2EFDA"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmargenmayorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmarcajemayorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFD9E1F2"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => "-",
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmfrutaminorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpreventaminorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFE2EFDA"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmargenminorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmarcajeminorista),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFD9E1F2"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => "-",
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        )
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmfrutahorizontal),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpreventabodega),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF70AD47"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFE2EFDA"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltpmargenbodega),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFFFFFFF"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
                array(
                    "value" => floatval($ltp->ltppvp),
                    "style" => array(
                        "font" => array(
                            "sz" => "11",
                            "bold" => true,
                            "color" => array(
                                "rgb" => "FF4472C4"
                            )
                        ),
                        "fill" => array(
                            "patternType" => 'solid',
                            "fgColor" => array(
                                "rgb" => "FFD9E1F2"
                            )
                        ),
                        "alignment" => array(
                            "vertical" => "center",
                            "horizontal" => "center"
                        ),
                        "numFmt" => "#,##0.00"
                        
                    )
                ),
            );

            $nuevoArray[0]['data'][] = $arrayFilaExcel;

        }

        return response()->json([
            'excel' => $nuevoArray,
            'data' => $ltps
        ]);

    }

    private function ArmarCabecerasFilaTres($re_columnas, $arrayFilaExcel)
    {
        $nuevoArrayCabecera = array();
        $contMayorista = 0;
        $contMinorista = 0;
        $contBodega    = 0;

        $camposMayorista = array();
        $camposMinorista = array();
        $camposBodega    = array();
        $camposBlancos   = array();

        $esMayorista = false;
        $esMinorista = false;
        $esBodega    = false;

        $nuevasColumnas = array();

        foreach($re_columnas as $re_columna){

            if($re_columna['agrupacion'] == "mayorista"){
                
                $esMayorista   = true;
                $contMayorista = $contMayorista + 1;
                $camposMayorista[] = $re_columna;

                if($esBodega == true){
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "BODEGA",
                        "cont"   => $contBodega,
                        "column" => $camposBodega
                    );
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "ESPACIO",
                        "cont"   => 1
                    );

                }else if($esMinorista == true){
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "MINORISTA",
                        "cont"   => $contMinorista,
                        "column" => $camposMinorista
                    );
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "ESPACIO",
                        "cont"   => 1
                    );
                }

                $contMinorista = 0;
                $contBodega    = 0;
                $esMinorista = false;
                $esBodega    = false;

                $camposMinorista = array();
                $camposBodega    = array();
                $camposBlancos   = array();


            }else if($re_columna['agrupacion'] == "minorista"){

                $esMinorista = true;
                $contMinorista = $contMinorista + 1;
                $camposMinorista[] = $re_columna;

                if($esMayorista == true){

                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "MAYORISTA",
                        "cont"   => $contMayorista,
                        "column" => $camposMayorista
                    );
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "ESPACIO",
                        "cont"   => 1
                    );

                }else if($esBodega == true){
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "BODEGA",
                        "cont"   => $contBodega,
                        "column" => $camposBodega
                    );
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "ESPACIO",
                        "cont"   => 1
                    );
                }

                $contMayorista = 0;
                $contBodega    = 0;
                $esMayorista = false;
                $esBodega    = false;

                $camposMayorista = array();
                $camposBodega    = array();
                $camposBlancos   = array();

            }else if($re_columna['agrupacion'] == "bodega"){
                
                $esBodega = true;
                $contBodega = $contBodega + 1;
                $camposBodega[] = $re_columna;

                if($esMayorista == true){
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "MAYORISTA",
                        "cont"   => $contMayorista,
                        "column" => $camposMayorista
                    );
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "ESPACIO",
                        "cont"   => 1
                    );
                }else if($esMinorista == true){
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "MINORISTA",
                        "cont"   => $contMinorista,
                        "column" => $camposMinorista
                    );
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "ESPACIO",
                        "cont"   => 1
                    );
                }

                $contMayorista = 0;
                $contMinorista = 0;
                $esMayorista = false;
                $esMinorista = false;

                $camposMayorista = array();
                $camposMinorista = array();
                $camposBlancos   = array();

            }else{



                if($esMayorista == true){
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "MAYORISTA",
                        "cont"   => $contMayorista,
                        "column" => $camposMayorista
                    );
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "ESPACIO",
                        "cont"   => 1
                    );
                }else if($esMinorista == true){
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "MINORISTA",
                        "cont"   => $contMinorista,
                        "column" => $camposMinorista
                    );
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "ESPACIO",
                        "cont"   => 1
                    );
                }else if($esBodega == true){
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "BODEGA",
                        "cont"   => $contBodega,
                        "column" => $camposBodega
                    );
                    $nuevoArrayCabecera[] = array(
                        "tipo"   => "data",
                        "titulo" => "ESPACIO",
                        "cont"   => 1
                    );
                }

                $contMayorista = 0;
                $contMinorista = 0;
                $contBodega    = 0;
                $esMayorista = false;
                $esMinorista = false;
                $esBodega    = false;
                $camposMayorista = array();
                $camposMinorista = array();
                $camposBodega    = array();
                $camposBlancos   = array();

                $camposBlancos[] = $re_columna;

                $nuevoArrayCabecera[] = array(
                    "tipo"   => "blanco",
                    "titulo" => "",
                    "column" => $camposBlancos
                );
            }
        }

        if($esMayorista == true){
            $nuevoArrayCabecera[] = array(
                "tipo"   => "data",
                "titulo" => "MAYORISTA",
                "cont"   => $contMayorista,
                "column" => $camposMayorista
            );
            $nuevoArrayCabecera[] = array(
                "tipo"   => "data",
                "titulo" => "ESPACIO",
                "cont"   => 1
            );
        }else if($esMinorista == true){
            $nuevoArrayCabecera[] = array(
                "tipo"   => "data",
                "titulo" => "MINORISTA",
                "cont"   => $contMinorista,
                "column" => $camposMinorista
            );
            $nuevoArrayCabecera[] = array(
                "tipo"   => "data",
                "titulo" => "ESPACIO",
                "cont"   => 1
            );
        }else if($esBodega == true){
            $nuevoArrayCabecera[] = array(
                "tipo"   => "data",
                "titulo" => "BODEGA",
                "cont"   => $contBodega,
                "column" => $camposBodega
            );
            $nuevoArrayCabecera[] = array(
                "tipo"   => "data",
                "titulo" => "ESPACIO",
                "cont"   => 1
            );
        }

        $contMayorista = 0;
        $contMinorista = 0;
        $contBodega    = 0;
        $esMayorista = false;
        $esMinorista = false;
        $esBodega    = false;

        $camposMayorista = array();
        $camposMinorista = array();
        $camposBodega    = array();
        $camposBlancos   = array();

        $nuevoArrayCabecera[] = array(
            "tipo"   => "blanco",
            "titulo" => "",
            "column" => []
        );


        foreach($nuevoArrayCabecera as $nuevoArrayCabe){

            if($nuevoArrayCabe['tipo'] == "blanco"){
                $arrayFilaExcel[] = array();
            }else if($nuevoArrayCabe['titulo'] == "ESPACIO"){
                $arrayFilaExcel[] = array();
            }else{
                if($nuevoArrayCabe['cont'] == 1){
                    $arrayFilaExcel[] = array(
                        "value" => $nuevoArrayCabe['titulo'],
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                                "vertical" => "center",
                                "horizontal" => "center",
                            )
                        )
                    );
                }else if($nuevoArrayCabe['cont'] == 2){
                    $arrayFilaExcel[] = array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )    
                    );

                    $arrayFilaExcel[] = array(
                        "value" => $nuevoArrayCabe['titulo'],
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                                "vertical" => "left",
                                "horizontal" => "left",
                                "readingOrder" => 3
                            )
                        )
                    );
                }else if($nuevoArrayCabe['cont'] == 3){

                    $arrayFilaExcel[] = array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )    
                    );

                    $arrayFilaExcel[] = array(
                        "value" => $nuevoArrayCabe['titulo'],
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                                "vertical" => "center",
                                "horizontal" => "center",
                                "readingOrder" => 3
                            )
                        )
                    );

                    $arrayFilaExcel[] = array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )    
                    );

                }else if($nuevoArrayCabe['cont'] == 4){

                    $arrayFilaExcel[] = array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    );
                    
                    $arrayFilaExcel[] = array(
                        "value" => $nuevoArrayCabe['titulo'],
                        "style" => array(
                            "font" => array(
                                "sz" => "11",
                                "bold" => true,
                                "color" => array(
                                    "rgb" => "FFFFFFFF"
                                )
                            ),
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                                "vertical" => "right",
                                "horizontal" => "right",
                                "readingOrder" => 3
                            )
                        )
                    );

                    $arrayFilaExcel[] = array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    );

                    $arrayFilaExcel[] = array(
                        "value" => "",
                        "style" => array(
                            "fill" => array(
                                "patternType" => 'solid',
                                "fgColor" => array(
                                    "rgb" => "FF000000"
                                )
                            ),
                            "alignment" => array(
                                "wrapText" => true,
                            )
                        )
                    );

                }
            }

        }

        // -------------------

        foreach($nuevoArrayCabecera as $nuevoArrayCabe){
            
            if($nuevoArrayCabe['tipo'] == "blanco"){
                
                foreach($nuevoArrayCabe['column'] as $columna_recibida){
                    $nuevasColumnas[] = $columna_recibida;
                }

            }else if($nuevoArrayCabe['titulo'] == "ESPACIO"){
                // $nuevasColumnas[] = array(
                //     "agrupacion"   => "ESPACIO",
                //     "columna"      => "ESPACIO",
                //     "orden"        => 0,
                //     "seleccionado" => true
                // );
                // $nuevasColumnas[] = $re_columna;
            }else{

                foreach($nuevoArrayCabe['column'] as $columna_recibida){
                    $nuevasColumnas[] = $columna_recibida;
                }

                $nuevasColumnas[] = array(
                    "agrupacion"   => "ESPACIO",
                    "columna"      => "ESPACIO",
                    "orden"        => 0,
                    "seleccionado" => true
                );

            }
            
        }

        return array(
            "arrayFilaExcel" => $arrayFilaExcel,
            "nuevasColumnas" => $nuevasColumnas
        );

    }

}
















// $esMayorista = false;
// $contMayorista = 0;

// foreach($re_columnas as $re_columna){

//     if( $re_columna['agrupacion'] == "mayorista" ){

//         $esMayorista = true;
//         $contMayorista = $contMayorista + 1;

//     }else{
//         if($esMayorista == true){
            

//             if($contMayorista == 1){
//                 $arrayFilaExcel[] = array(
//                     "value" => "MAYORISTA",
//                     "style" => array(
//                         "font" => array(
//                             "sz" => "11",
//                             "bold" => true,
//                             "color" => array(
//                                 "rgb" => "FFFFFFFF"
//                             )
//                         ),
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                             "vertical" => "center",
//                             "horizontal" => "center",
//                         )
//                     )
//                 );
//             }else if($contMayorista == 2){
                
//                 $arrayFilaExcel[] = array(
//                     "value" => "",
//                     "style" => array(
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                         )
//                     )    
//                 );

//                 $arrayFilaExcel[] = array(
//                     "value" => "MAYORISTA",
//                     "style" => array(
//                         "font" => array(
//                             "sz" => "11",
//                             "bold" => true,
//                             "color" => array(
//                                 "rgb" => "FFFFFFFF"
//                             )
//                         ),
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                             "vertical" => "left",
//                             "horizontal" => "left",
//                             "readingOrder" => 3
//                         )
//                     )
//                 );
                
//             }else if($contMayorista == 3){
//                 $arrayFilaExcel[] = array(
//                     "value" => "",
//                     "style" => array(
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                         )
//                     )    
//                 );

//                 $arrayFilaExcel[] = array(
//                     "value" => "MAYORISTA",
//                     "style" => array(
//                         "font" => array(
//                             "sz" => "11",
//                             "bold" => true,
//                             "color" => array(
//                                 "rgb" => "FFFFFFFF"
//                             )
//                         ),
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                             "vertical" => "center",
//                             "horizontal" => "center",
//                             "readingOrder" => 3
//                         )
//                     )
//                 );

//                 $arrayFilaExcel[] = array(
//                     "value" => "",
//                     "style" => array(
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                         )
//                     )    
//                 );
//             }else if($contMayorista == 4){

//                 $arrayFilaExcel[] = array(
//                     "value" => "",
//                     "style" => array(
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                         )
//                     )
//                 );
                
//                 $arrayFilaExcel[] = array(
//                     "value" => "MAYORISTA",
//                     "style" => array(
//                         "font" => array(
//                             "sz" => "11",
//                             "bold" => true,
//                             "color" => array(
//                                 "rgb" => "FFFFFFFF"
//                             )
//                         ),
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                             "vertical" => "right",
//                             "horizontal" => "right",
//                             "readingOrder" => 3
//                         )
//                     )
//                 );

//                 $arrayFilaExcel[] = array(
//                     "value" => "",
//                     "style" => array(
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                         )
//                     )
//                 );

//                 $arrayFilaExcel[] = array(
//                     "value" => "",
//                     "style" => array(
//                         "fill" => array(
//                             "patternType" => 'solid',
//                             "fgColor" => array(
//                                 "rgb" => "FF000000"
//                             )
//                         ),
//                         "alignment" => array(
//                             "wrapText" => true,
//                         )
//                     )
//                 );

//             }

//             $arrayFilaExcel[] = array(
//             );

//             $contMayorista = 0;
//             $esMayorista = false;

//         }else{
//             $arrayFilaExcel[] = array(
//             );

//             $contMayorista = 0;
//             $esMayorista = false;
//         }
//     }
// }

// if($esMayorista == true){
            

//     if($contMayorista == 1){
//         $arrayFilaExcel[] = array(
//             "value" => "MAYORISTA",
//             "style" => array(
//                 "font" => array(
//                     "sz" => "11",
//                     "bold" => true,
//                     "color" => array(
//                         "rgb" => "FFFFFFFF"
//                     )
//                 ),
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                     "vertical" => "center",
//                     "horizontal" => "center",
//                 )
//             )
//         );
//     }else if($contMayorista == 2){
        
//         $arrayFilaExcel[] = array(
//             "value" => "",
//             "style" => array(
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                 )
//             )    
//         );

//         $arrayFilaExcel[] = array(
//             "value" => "MAYORISTA",
//             "style" => array(
//                 "font" => array(
//                     "sz" => "11",
//                     "bold" => true,
//                     "color" => array(
//                         "rgb" => "FFFFFFFF"
//                     )
//                 ),
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                     "vertical" => "left",
//                     "horizontal" => "left",
//                     "readingOrder" => 3
//                 )
//             )
//         );
        
//     }else if($contMayorista == 3){
//         $arrayFilaExcel[] = array(
//             "value" => "",
//             "style" => array(
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                 )
//             )    
//         );

//         $arrayFilaExcel[] = array(
//             "value" => "MAYORISTA",
//             "style" => array(
//                 "font" => array(
//                     "sz" => "11",
//                     "bold" => true,
//                     "color" => array(
//                         "rgb" => "FFFFFFFF"
//                     )
//                 ),
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                     "vertical" => "center",
//                     "horizontal" => "center",
//                     "readingOrder" => 3
//                 )
//             )
//         );

//         $arrayFilaExcel[] = array(
//             "value" => "",
//             "style" => array(
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                 )
//             )    
//         );
//     }else if($contMayorista == 4){

//         $arrayFilaExcel[] = array(
//             "value" => "",
//             "style" => array(
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                 )
//             )
//         );
        
//         $arrayFilaExcel[] = array(
//             "value" => "MAYORISTA",
//             "style" => array(
//                 "font" => array(
//                     "sz" => "11",
//                     "bold" => true,
//                     "color" => array(
//                         "rgb" => "FFFFFFFF"
//                     )
//                 ),
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                     "vertical" => "right",
//                     "horizontal" => "right",
//                     "readingOrder" => 3
//                 )
//             )
//         );

//         $arrayFilaExcel[] = array(
//             "value" => "",
//             "style" => array(
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                 )
//             )
//         );

//         $arrayFilaExcel[] = array(
//             "value" => "",
//             "style" => array(
//                 "fill" => array(
//                     "patternType" => 'solid',
//                     "fgColor" => array(
//                         "rgb" => "FF000000"
//                     )
//                 ),
//                 "alignment" => array(
//                     "wrapText" => true,
//                 )
//             )
//         );

//     }

//     $contMayorista = 0;
//     $esMayorista = false;

// }else{
//     $arrayFilaExcel[] = array();
//     $contMayorista = 0;
//     $esMayorista = false;
// }