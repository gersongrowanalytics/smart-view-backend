<?php //Promociones Set 2021.xlsx

namespace App\Http\Controllers\Sistema\CargarArchivo\Promociones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditoriaController;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Illuminate\Support\Str;
use App\zonzonas;
use App\fecfechas;
use App\tputiposusuarios;
use App\perpersonas;
use App\usuusuarios;
use App\catcategorias;
use App\proproductos;
use App\tprtipospromociones;
use App\cancanales;
use App\ussusuariossucursales;
use App\sucsucursales;
use App\scasucursalescategorias;
use App\csccanalessucursalescategorias;
use App\prmpromociones;
use App\cspcanalessucursalespromociones;
use App\prbpromocionesbonificaciones;
use App\prppromocionesproductos;
use App\carcargasarchivos;
use App\tuptiposusuariospermisos;
use \DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CargarArchivoPromocionesController extends Controller
{
    public function CargarArchivos(Request $request)
    {
        echo "no existe";
    }

    
}
