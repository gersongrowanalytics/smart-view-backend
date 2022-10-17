<?php

namespace App\Http\Controllers\Bolivia\CuboSO;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\spider_view_1;

class MetObtenerSOController extends Controller
{
    public function ObtenerSO(Request $request)
    {

        $spider = spider_view_1::limit(10);

        return $spider;

    }
}
