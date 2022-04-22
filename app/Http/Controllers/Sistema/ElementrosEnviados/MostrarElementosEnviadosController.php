<?php

namespace App\Http\Controllers\Sistema\ElementrosEnviados;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\uceusuarioscorreosenviados;
use App\dcedestinatarioscorreosenviados;

class MostrarElementosEnviadosController extends Controller
{
    public function MostrarElementosEnviados(Request $request)
    {
        $re_tiposEnvio = $request['$re_tiposEnvio'];

        $uces = uceusuarioscorreosenviados::join('dcedestinatarioscorreosenviados as dce', 'dce.uceid', 'uceusuarioscorreosenviados.uceid')
                                            ->where(function ($query) use($re_tiposEnvio) {
                                                foreach($re_tiposEnvio as $te){
                                                    if(isset($te['seleccionado'])){
                                                        if($te['seleccionado'] == true){
                                                            $query->orwhere('uceusuarioscorreosenviados.ucetipo', $te['tnotipo']);
                                                        }
                                                    }
                                                }
                                            })
                                            ->orderBy('uceusuarioscorreosenviados.created_at', 'DESC')
                                            ->paginate(20);

        $requestsalida = response()->json([
            "respuesta" => true,
            "mensaje"   => "",
            "datos"     => $uces,
        ]);
        
        return $requestsalida;
    }
}
