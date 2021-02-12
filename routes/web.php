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

$router->get('/actualizar', 'salvacionController@salvacion');
$router->get('/asignarsuc', 'salvacionController@asignarzonassucursales');
$router->get('/actualizarestadosucursales', 'salvacionController@cambiarEstadoSucursales');
$router->get('/actualizarimagenessellinout', 'salvacionController@CambiarImagenSellOut');

$router->get('/mail', 'MailController@getMail');
$router->post('/recuperar/contrasena', 'MailController@recuperarContrasena');
$router->post('/login', 'Sistema\loginController@login');

$router->group(['middleware' => ['permisos']], function() use($router) {
    
    $router->post('/ventas/mostrar', 'Sistema\Ventas\Mostrar\VentasMostrarController@mostrarVentas');
    $router->post('/ventas/mostrar/porzona', 'Sistema\Ventas\Mostrar\VentasMostrarController@mostrarVentasXZona');
    $router->post('/promociones/mostrar/categorias', 'Sistema\Promociones\Mostrar\CategoriasController@mostrarCategorias');
    $router->post('/promociones/mostrar/categorias/xzona', 'Sistema\Promociones\Mostrar\CategoriasController@mostrarCategoriasXZona');
    $router->post('/promociones/mostrar/promociones', 'Sistema\Promociones\Mostrar\PromocionesMostrarController@mostrarPromociones');
    $router->post('/promociones/mostrar/promociones/xzona', 'Sistema\Promociones\Mostrar\PromocionesMostrarController@mostrarPromocionesXZona');
    $router->post('/promociones/promocion/editar', 'Sistema\Promociones\Mostrar\PromocionesMostrarController@mostrarPromociones');
    $router->post('/usuario/mostrar/sucursales', 'Sistema\Usuario\Sucursales\Mostrar\SucursalesMostrarController@mostrarSucursales');
    $router->post('/usuario/mostrar/permisos', 'Sistema\Usuario\Permisos\Mostrar\PermisosMostrarController@mostrarPermisosUsuario');
    
    $router->post('/configuracion/usuarios/crear/usuario', 'Sistema\Usu\Crear\CrearUsuarioController@CrearUsuario');
    $router->post('/configuracion/usuarios/mostrar/sucursales', 'Sistema\Usu\NuevoUsuario\MostrarSucursalesController@MostrarSucursales');

    $router->post('/configuracion/usuarios/editarUsuario', 'Sistema\Configuracion\Usuarios\Editar\UsuariosEditarController@editarUsuario');
    $router->post('/configuracion/usuarios/mostrarUsuarios', 'Sistema\Configuracion\Usuarios\Mostrar\UsuariosMostrarController@mostrarUsuarios');
    $router->post('/configuracion/usuarios/mostrar/ejecutivos', 'Sistema\Configuracion\Usuarios\Mostrar\EjecutivosMostrarController@mostrarEjecutivos');
    $router->post('/configuracion/usuarios/mostrar/TiposUsuarios', 'Sistema\Configuracion\Usuarios\Mostrar\TiposUsuariosController@mostrarTiposUsuarios');

    $router->post('/configuracion/usuarios/mostrar/permisos/tipoUsuario', 'Sistema\Configuracion\Usuarios\Mostrar\PermisosController@MostrarPermisosTipoUsuario');
    $router->post('/configuracion/usuarios/editar/permisos/tipoUsuario', 'Sistema\Configuracion\Usuarios\Editar\PermisosController@EditarPermisosTipoUsuario');

    $router->post('/configuracion/rebate/crearRebate', 'Sistema\Configuracion\Rebate\Crear\RebateCrearController@CrearRebate');
    $router->post('/configuracion/rebate/crear/GrupoRebate', 'Sistema\Configuracion\Rebate\Crear\GrupoRebateCrearController@CrearGrupoRebate');
    $router->post('/configuracion/rebate/mostrarRebate', 'Sistema\Configuracion\Rebate\Mostrar\RebateMostrarController@RebateMostrar');
    $router->post('/configuracion/rebate/mostrar/GrupoRebate', 'Sistema\Configuracion\Rebate\Mostrar\GrupoRebateMostrarController@GrupoRebateMostrar');
    $router->post('/configuracion/rebate/editar/Rebate', 'Sistema\Configuracion\Rebate\Editar\RebateEditarController@RebateEditar');
    $router->post('/configuracion/rebate/actualizar/rebate', 'Sistema\Configuracion\Rebate\Actualizar\RebateActualizarController@ActualizarValorizadoRebateFecha');
    
    // $router->post('/promociones/descargar', 'Sistema\Promociones\Mostrar\CategoriasPromocionesMostrarController@mostrarCategoriasPromociones');
    $router->post('/promociones/descargar', 'Sistema\Promociones\Mostrar\CategoriasPromocionesMostrarController@mostrarCategoriasPromocionesExcel');
    $router->post('/promociones/descargar/especificos', 'Sistema\Promociones\Mostrar\CategoriasPromocionesMostrarController@MostrarSucursalesDescargarPromocionesExcel');

    $router->post('/promociones/editar', 'Sistema\Promociones\Editar\PromocionEditarController@editarPromocion');
    $router->post('/promociones/editar/imagenes', 'Sistema\Promociones\Editar\PromocionEditarImagenesController@EditarImagenesPromocion');

    $router->post('/mostrar/tpus', 'Sistema\Tpu\Mostrar\TpusMostrarController@MostrarTpus');
    $router->post('/mostrar/tdis', 'Sistema\Tdi\Mostrar\TdisMostrarController@MostrarTdis');
    $router->post('/mostrar/zons', 'Sistema\Zon\Mostrar\ZonsMostrarController@MostrarZons');
    $router->post('/mostrar/cats', 'Sistema\Cat\Mostrar\CatsMostrarController@MostrarCats');
    $router->post('/mostrar/sucs/xzona', 'Sistema\Tablas\Suc\Mostrar\SucsMostrarController@MostrarSucsXZona');

    $router->post('/cargarArchivo/promociones', 'Sistema\CargarArchivo\Promociones\CargarArchivoController@CargarArchivo');
    $router->post('/cargarArchivo/promociones/actualizarNew', 'Sistema\CargarArchivo\Promociones\ActualizarNuevoController@ActualizarPromociones');
    $router->post('/cargarArchivo/promociones/planTrade', 'Sistema\CargarArchivo\Promociones\CargarArchivoController@CargarPlanTrade');
    $router->post('/cargarArchivo/promociones/desactivar', 'Sistema\CargarArchivo\Promociones\EliminarPromocionesController@CargarArchivoEliminarPromociones');

    $router->post('/cargarArchivo/ventas/obejtivos', 'Sistema\CargarArchivo\Ventas\ObjetivoCargarController@CargarObjetivo');
    $router->post('/cargarArchivo/ventas/obejtivossellout', 'Sistema\CargarArchivo\Ventas\ObjetivoCargarController@CargarObjetivoSellOut');
    $router->post('/cargarArchivo/ventas/sellin', 'Sistema\CargarArchivo\Ventas\CargarArchivoController@CargarArchivo');
    $router->post('/cargarArchivo/ventas/sellout', 'Sistema\CargarArchivo\Ventas\CargarArchivoController@cargarVentasSellOut');
    $router->post('/cargarArchivo/clientes', 'Sistema\CargarArchivo\Clientes\ClientesCargarController@CargarClientes');
    
    $router->post('/cargarArchivo/clientes/acutalizarzonas', 'Sistema\CargarArchivo\Clientes\ClientesCargarController@ActualizarZonaClientes');
    $router->post('/cargarArchivo/clientes/actualizargruporebate', 'Sistema\CargarArchivo\Clientes\ClientesCargarController@ActualizarGrupoRebateOctubre');
    $router->post('/cargarArchivo/clientes/actualizarGrupoSucursal', 'Sistema\CargarArchivo\Clientes\ActualizarGrupoSucursalController@ActualizarGrupoSucursal');

    $router->post('/cargarArchivo/productos', 'Sistema\CargarArchivo\Productos\ProductosCargarController@CargarProductos');
    $router->post('/cargarArchivo/prueba/soldto', 'Sistema\CargarArchivo\Prueba\PruebaController@prueba');
    $router->post('/fechas/mostrar/fechas', 'Sistema\Fechas\Mostrar\FechasMostrarController@mostrarFechas');

    $router->post('/mostrar/controlArchivos', 'Sistema\ControlArchivos\Mostrar\ControlArchivosMostrarController@MostrarControlArchivos');

    $router->post('/controlVentas/estadistica/xzona', 'Sistema\Modulos\ControlVentas\VentasXZonasController@VentasXZonas');

    $router->post('/control/promociones/lista', 'Sistema\Modulos\Control\ControlPromociones\TablaPromocionesController@MostrarPromociones');

    $router->post('/configuracion/crear/rebateTrimestral', 'Sistema\Trimestre\CrearTrimestreController@CrearTrimestre');
    $router->post('/configuracion/actualizar/rebateTrimestral', 'Sistema\Trimestre\ActualizarTrimestreController@ActualizarTrimestre');

    $router->post('/perfil/editar/editarPerfil', 'Sistema\Perfil\Editar\EditarPerfilController@EditarPerfil');
    $router->post('/perfil/mostrar/novedades', 'Sistema\Perfil\Mostrar\MostrarNovedadesController@MostrarNovedades');

});


$router->get('/armarRebateBonus/{fecid}', 'Sistema\RebatesBonus\AsignarSucursalesController@AsiganarSucursales');
$router->get('/armarCategoriasRebateBonus/{fecid}/{rbbid}', 'Sistema\RebatesBonus\AsignarSucursalesController@ActualizarCategoriasBonus');

$router->get('/mostrar/tdis', 'Sistema\Tdi\Mostrar\TdisMostrarController@MostrarTdis');
$router->get('/consumirApi', 'Sistema\SellOut\CargarSellOutController@CargarSellOutTodo');

$router->get('/obtenerSellOutTodo', 'Sistema\SellOut\CargarSellOutController@CargarSellOutTodo');
$router->get('/obtenerSellOutDiario', 'Sistema\SellOut\CargarSellOutController@CargarSellOutDiario');
$router->get('/obtenerSellOutEspecifico/{anioSelec}/{mesSelec}/{diaSelec}', 'Sistema\SellOut\CargarSellOutController@CargarSellOutEspecifico');
$router->get('/obtenerSellOutEspecificoWeb/{anioSelec}/{mesSelec}/{diaSelec}', 'Sistema\SellOut\CargarSellOutController@CargarSellOutEspecificoWeb');


$router->get('/actualizarTogo/{fecid}', 'salvacionController@ActualizarToGo');
$router->get('/ActualizarSucursales', 'salvacionController@ActualizarSucursales');





// SELL OUT
$router->get('/ws/obtenerSellOutEspecifico/{anio}/{mes}/{dia}', 'Sistema\SellOut\ObtenerSellOutController@ObtenerSellOutEspecifico');
$router->get('/ws/obtenerSellOutDiario', 'Sistema\SellOut\ObtenerSellOutController@ObtenerSellOutActual');
$router->get('/ws/obtenerSellOutTodo', 'Sistema\SellOut\ObtenerSellOutController@ObtenerTodoSellOut');