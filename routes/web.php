<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});



$router->post('/login', 'Sistema\loginController@login');

$router->group(['middleware' => ['permisos']], function() use($router) {
    
    $router->post('/ventas/mostrar', 'Sistema\Ventas\Mostrar\VentasMostrarController@mostrarVentas');
    $router->post('/ventas/mostrar/porzona', 'Sistema\Ventas\Mostrar\VentasMostrarController@mostrarVentasXZona');
    $router->post('/promociones/mostrar/categorias', 'Sistema\Promociones\Mostrar\CategoriasController@mostrarCategorias');
    $router->post('/promociones/mostrar/promociones', 'Sistema\Promociones\Mostrar\PromocionesMostrarController@mostrarPromociones');
    $router->post('/promociones/promocion/editar', 'Sistema\Promociones\Mostrar\PromocionesMostrarController@mostrarPromociones');
    $router->post('/usuario/mostrar/sucursales', 'Sistema\Usuario\Sucursales\Mostrar\SucursalesMostrarController@mostrarSucursales');

    
    $router->post('/configuracion/usuarios/editarUsuario', 'Sistema\Configuracion\Usuarios\Editar\UsuariosEditarController@editarUsuario');
    $router->post('/configuracion/usuarios/mostrarUsuarios', 'Sistema\Configuracion\Usuarios\Mostrar\UsuariosMostrarController@mostrarUsuarios');
    $router->post('/configuracion/usuarios/mostrar/TiposUsuarios', 'Sistema\Configuracion\Usuarios\Mostrar\TiposUsuariosController@mostrarTiposUsuarios');

    $router->post('/configuracion/usuarios/mostrar/permisos/tipoUsuario', 'Sistema\Configuracion\Usuarios\Mostrar\PermisosController@MostrarPermisosTipoUsuario');

    $router->post('/configuracion/rebate/crearRebate', 'Sistema\Configuracion\Rebate\Crear\RebateCrearController@CrearRebate');
    $router->post('/configuracion/rebate/crear/GrupoRebate', 'Sistema\Configuracion\Rebate\Crear\GrupoRebateCrearController@CrearGrupoRebate');
    $router->post('/configuracion/rebate/mostrarRebate', 'Sistema\Configuracion\Rebate\Mostrar\RebateMostrarController@RebateMostrar');
    $router->post('/configuracion/rebate/mostrar/GrupoRebate', 'Sistema\Configuracion\Rebate\Mostrar\GrupoRebateMostrarController@GrupoRebateMostrar');
    $router->post('/configuracion/rebate/editar/Rebate', 'Sistema\Configuracion\Rebate\Editar\RebateEditarController@RebateEditar');
    
    // $router->post('/promociones/descargar', 'Sistema\Promociones\Mostrar\CategoriasPromocionesMostrarController@mostrarCategoriasPromociones');
    $router->post('/promociones/descargar', 'Sistema\Promociones\Mostrar\CategoriasPromocionesMostrarController@mostrarCategoriasPromocionesExcel');

    $router->post('/promociones/editar', 'Sistema\Promociones\Editar\PromocionEditarController@editarPromocion');



    $router->post('/cargarArchivo/promociones', 'Sistema\CargarArchivo\Promociones\CargarArchivoController@CargarArchivo');
    $router->post('/cargarArchivo/ventas/obejtivos', 'Sistema\CargarArchivo\Ventas\ObjetivoCargarController@CargarObjetivo');
    $router->post('/cargarArchivo/ventas/obejtivossellout', 'Sistema\CargarArchivo\Ventas\ObjetivoCargarController@CargarObjetivoSellOut');
    $router->post('/cargarArchivo/ventas/sellin', 'Sistema\CargarArchivo\Ventas\CargarArchivoController@CargarArchivo');
    $router->post('/cargarArchivo/ventas/sellout', 'Sistema\CargarArchivo\Ventas\CargarArchivoController@cargarVentasSellOut');
    $router->post('/cargarArchivo/clientes', 'Sistema\CargarArchivo\Clientes\ClientesCargarController@CargarClientes');
    $router->post('/cargarArchivo/clientes/acutalizarzonas', 'Sistema\CargarArchivo\Clientes\ClientesCargarController@ActualizarZonaClientes');
    $router->post('/cargarArchivo/productos', 'Sistema\CargarArchivo\Productos\ProductosCargarController@CargarProductos');
    $router->post('/fechas/mostrar/fechas', 'Sistema\Fechas\Mostrar\FechasMostrarController@mostrarFechas');
});



