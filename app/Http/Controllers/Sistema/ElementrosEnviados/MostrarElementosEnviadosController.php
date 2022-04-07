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

        $uces = uceusuarioscorreosenviados::join('dcedestinatarioscorreosenviados as dce', 'dce.uceid', 'uceusuarioscorreosenviados.uceid')
                                            ->orderBy('created_at', 'DESC')
                                            ->paginate(20);

        $requestsalida = response()->json([
            "respuesta" => true,
            "mensaje"   => "",
            "datos"     => $uces,
        ]);
        
        return $requestsalida;
    }
}
