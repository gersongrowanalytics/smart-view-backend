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

$router->post('/usuarios/obtener-usuario', 'Sistema\Administrador\Usuarios\ObtenerUsuarioController@ObtenerUsuario');

$router->post('/usuarios/eliminar', 'Sistema\Administrador\Usuarios\EliminarUsuariosController@EliminarUsuarios');
$router->post('/usuarios/crear', 'Sistema\Administrador\Usuarios\CrearUsuariosController@CrearUsuarios');

$router->post('/usuarios/obtener-usuario', 'Sistema\Administrador\Usuarios\ObtenerUsuarioController@ObtenerUsuario');


$router->post('/usuarios/editar', 'Sistema\Administrador\Usuarios\EditarUsuariosController@EditarUsuarios');
$router->post('/usuarios/mostrar', 'Sistema\Administrador\Usuarios\MostrarUsuariosController@MostrarUsuarios');
$router->post('/asignarsucursales', 'Sistema\Administrador\Usuarios\AsignarSucursalesController@AsignarSucursales');
$router->post('/tipos-usuarios/permisos/mostrar', 'Sistema\Administrador\TiposUsuarios\MostrarPermisosTiposUsuariosController@MostrarPermisosTiposUsuarios');
$router->post('/tipos-usuarios/permisos/editar', 'Sistema\Administrador\TiposUsuarios\EditarPermisosTiposUsuariosController@EditarPermisosTiposUsuarios');

$router->get('/permisos/mostrar', 'Sistema\Administrador\Permisos\MostrarPermisosController@MostrarPermisos');
$router->post('/permisos-editar', 'Sistema\Administrador\Permisos\EditarPermisosController@EditarPermisos');
$router->post('/permisos-eliminar', 'Sistema\Administrador\Permisos\EditarPermisosController@EditarEliminarPermiso');

$router->post('/permisos/crear', 'Sistema\Administrador\Permisos\CrearPermisosController@CrearPermisos');
$router->post('/permisos/editar', 'Sistema\Administrador\Permisos\EditarPermisosController@EditarPermisos');
$router->post('/tipo-permiso/crear', 'Sistema\Administrador\Permisos\CrearTipoPermisoController@CrearTipoPermiso');


$router->get('/actualizar', 'salvacionController@salvacion');
$router->get('/asignarsuc', 'salvacionController@asignarzonassucursales');
$router->get('/actualizarestadosucursales', 'salvacionController@cambiarEstadoSucursales');
$router->get('/actualizarimagenessellinout', 'salvacionController@CambiarImagenSellOut');
$router->get('/asignar-sku-bonif-prm/{fecid}', 'salvacionController@AsignarSkuBonifPrm');

$router->get('/mail', 'MailController@getMail');
$router->post('/recuperar/contrasena', 'MailController@recuperarContrasena');

$router->post('/recuperar/contrasena/nuevo', 'Sistema\recuperarController@EnviarCorreoRecuperar');
$router->post('/cambiar/contrasenia/nuevo', 'Sistema\recuperarController@CambiarContraseniaRecuperar');

$router->post('/login', 'Sistema\loginController@login');
$router->post('/cerrar-session', 'Sistema\loginController@MetCerrarSession');
$router->get('/correo-recuperar', 'Sistema\recuperarController@EnviarCorreoVista');

// ACEPTAR TERMINOS Y CONDICIONES
$router->post('/aceptar-terminos-condiciones', 'Sistema\TerminosCondiciones\AceptarTerminosController@AceptarTerminos');

// $router->group(['middleware' => ['permisos']], function() use($router) {
    
    $router->post('/ventas/mostrar', 'Sistema\Ventas\Mostrar\VentasMostrarController@mostrarVentas');
    $router->post('/ventas/mostrar/acumulado', 'Sistema\Ventas\Mostrar\VentasMostrarAcumuladoController@mostrarVentasAcumuladas');
    $router->post('/ventas/mostrar/porzona', 'Sistema\Ventas\Mostrar\VentasMostrarController@mostrarVentasXZona');
    $router->post('/ventas/mostrar/porzona/prueba', 'Sistema\Ventas\Mostrar\VentasMostrarController@mostrarVentasXZonaPruebaSO');

    // DESCARGAR SI Y SO
    $router->post('/ventas/descargar/especificos/si', 'Sistema\Ventas\Mostrar\MostrarDescargaSiSoController@MostrarSucursalesDescargarVentasSiExcel');
    $router->post('/ventas/descargar/especificos/so', 'Sistema\Ventas\Mostrar\MostrarDescargaSiSoController@MostrarSucursalesDescargarVentasSoExcel');

    // PROMOCIONES
    $router->post('/promociones/mail/enviar-correo-promociones-activas', 'Sistema\Promociones\Mail\EnviarPromocionesActivasController@EnviarPromocionesActivas');
    $router->post('/promociones/mail/enviar-correo-promociones-nuevas', 'Sistema\Promociones\Mail\EnviarPromocionesNuevasController@EnviarPromocionesNuevas');

    $router->post('/promociones/mostrar/categorias', 'Sistema\Promociones\Mostrar\CategoriasController@mostrarCategorias');
    $router->post('/promociones/mostrar/categorias/xzona', 'Sistema\Promociones\Mostrar\CategoriasController@mostrarCategoriasXZona');
    $router->post('/promociones/mostrar/categorias/acumulado', 'Sistema\Promociones\Mostrar\CategoriasAcumuladoController@CategoriasAcumulado');
    

    $router->post('/promociones/mostrar/promociones', 'Sistema\Promociones\Mostrar\PromocionesMostrarController@mostrarPromociones');
    $router->post('/promociones/mostrar/promociones/xzona', 'Sistema\Promociones\Mostrar\PromocionesMostrarController@mostrarPromocionesXZona');
    $router->post('/promociones/mostrar/promociones/promos-acumuladas', 'Sistema\Promociones\Mostrar\PromocionesMostrarAcumuladoController@PromocionesMostrarAcumulado');

    $router->post('/promociones/mostrar/pdf-generar', 'Sistema\Promociones\Mostrar\PdfPromocionesController@MostrarPdfPromociones');

    $router->post('/promociones/promocion/editar', 'Sistema\Promociones\Mostrar\PromocionesMostrarController@mostrarPromociones');
    $router->post('/usuario/mostrar/sucursales', 'Sistema\Usuario\Sucursales\Mostrar\SucursalesMostrarController@mostrarSucursales');
    $router->post('/usuario/mostrar/sucursales-moderno', 'Sistema\Usuario\Sucursales\Mostrar\SucursalesCanalModernoController@SucursalesCanalModerno');
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
    // $router->post('/configuracion/rebate/mostrarRebate', 'Sistema\Configuracion\Rebate\Mostrar\RebateMostrarController@RebateMostrar');
    $router->post('/configuracion/rebate/mostrarRebate', 'Sistema\Configuracion\Rebate\Mostrar\RebateMostrarController@MostrarRebateOrdenado');
    $router->post('/configuracion/rebate/mostrar/GrupoRebate', 'Sistema\Configuracion\Rebate\Mostrar\GrupoRebateMostrarController@GrupoRebateMostrar');
    $router->post('/configuracion/rebate/editar/Rebate', 'Sistema\Configuracion\Rebate\Editar\RebateEditarController@RebateEditar');
    $router->post('/configuracion/rebate/actualizar/rebate', 'Sistema\Configuracion\Rebate\Actualizar\RebateActualizarController@ActualizarValorizadoRebateFecha');
    
    // $router->post('/promociones/descargar', 'Sistema\Promociones\Mostrar\CategoriasPromocionesMostrarController@mostrarCategoriasPromociones');
    $router->post('/promociones/descargar', 'Sistema\Promociones\Mostrar\CategoriasPromocionesMostrarController@mostrarCategoriasPromocionesExcel');
    $router->post('/promociones/descargar/especificos', 'Sistema\Promociones\Mostrar\CategoriasPromocionesMostrarController@MostrarSucursalesDescargarPromocionesExcelbk');

    $router->post('/promociones/descargar/reporte-pagos', 'Sistema\Promociones\ReportePagos\MostrarReportePagosController@MostrarReportePagos');
    $router->post('/promociones/descargar/reporte-pagos-fecha', 'Sistema\Promociones\ReportePagos\MostrarReportePagosController@MostrarReportePagosXFechaIncioFechaFin');

    $router->post('/promociones/descargar/reporte-pagos-unicamente-fecha', 'Sistema\Promociones\ReportePagos\MostrarReportePagosController@MostrarReportePagosXFecha');
    $router->post('/promociones/descargar/reporte-promociones-liquidadas-fecha', 'Sistema\Promociones\ReportePagos\MostrarReportePagosController@MostrarReporteLiquidacionXFecha');

    $router->post('/promociones/editar', 'Sistema\Promociones\Editar\PromocionEditarController@editarPromocion');
    $router->post('/promociones/editar/imagenes', 'Sistema\Promociones\Editar\PromocionEditarImagenesController@EditarImagenesPromocion');

    $router->post('/mostrar/tpus', 'Sistema\Tpu\Mostrar\TpusMostrarController@MostrarTpus');
    $router->post('/mostrar/tdis', 'Sistema\Tdi\Mostrar\TdisMostrarController@MostrarTdis');
    $router->post('/mostrar/zons', 'Sistema\Zon\Mostrar\ZonsMostrarController@MostrarZons');
    $router->post('/mostrar/cats', 'Sistema\Cat\Mostrar\CatsMostrarController@MostrarCats');
    $router->post('/mostrar/pais', 'Sistema\Pai\Mostrar\PaisMostrarController@MostrarPais');
    $router->post('/mostrar/sucs/xzona', 'Sistema\Tablas\Suc\Mostrar\SucsMostrarController@MostrarSucsXZona');
    $router->post('/mostrar/tcas', 'Sistema\Tca\Mostrar\TcasMostrarController@MostrarTcas');

    // $router->post('/cargarArchivo/promociones', 'Sistema\CargarArchivo\Promociones\CargarArchivoController@CargarArchivo');
    $router->post('/cargarArchivo/promociones', 'Sistema\CargarArchivo\Promociones\CargarArchivoPromocionesController@CargarArchivo');
    $router->post('/cargarArchivo/promociones/nuevaspromociones', 'Sistema\CargarArchivo\Promociones\NuevaCargaPromocionesController@NuevaCargaPromociones');
    $router->post('/cargarArchivo/lista-precios', 'Sistema\CargarArchivo\ListaPrecios\CargarListaPreciosController@CargarListaPrecios');
    

    $router->post('/cargarArchivo/promociones/actualizarNew', 'Sistema\CargarArchivo\Promociones\ActualizarNuevoController@ActualizarPromociones');
    $router->post('/cargarArchivo/promociones/planTrade', 'Sistema\CargarArchivo\Promociones\CargarArchivoController@CargarPlanTrade');
    $router->post('/cargarArchivo/promociones/desactivar', 'Sistema\CargarArchivo\Promociones\EliminarPromocionesController@CargarArchivoEliminarPromociones');

    $router->post('/cargarArchivo/ventas/obejtivos', 'Sistema\CargarArchivo\Ventas\ObjetivoCargarController@CargarObjetivo');
    $router->post('/cargarArchivo/ventas/obejtivossellout', 'Sistema\CargarArchivo\Ventas\ObjetivoCargarController@CargarObSO');
    $router->post('/cargarArchivo/ventas/rebate', 'Sistema\CargarArchivo\Ventas\ObjetivoCargarController@CargarRebate');
    $router->post('/cargarArchivo/ventas/sellin', 'Sistema\CargarArchivo\Ventas\CargarArchivoController@CargarArchivo');
    $router->post('/cargarArchivo/ventas/sellout', 'Sistema\CargarArchivo\Ventas\CargarArchivoController@cargarVentasSellOut');
    $router->post('/cargarArchivo/clientes', 'Sistema\CargarArchivo\Clientes\ClientesCargarController@CargarClientes'); //
    // $router->post('/cargarArchivo/sucursales', 'Sistema\CargarArchivo\Sucursales\ActualizarSucursalesController@ActualizarSucursales');
    $router->post('/cargarArchivo/sucursales', 'Sistema\CargarArchivo\Sucursales\ActualizarSucursalesController@ActualizarNombres');
    
    $router->post('/cargarArchivo/clientes/acutalizarzonas', 'Sistema\CargarArchivo\Clientes\ClientesCargarController@ActualizarZonaClientes');
    $router->post('/cargarArchivo/clientes/actualizargruporebate', 'Sistema\CargarArchivo\Clientes\ClientesCargarController@ActualizarGrupoRebateOctubre');
    $router->post('/cargarArchivo/clientes/actualizarGrupoSucursal', 'Sistema\CargarArchivo\Clientes\ActualizarGrupoSucursalController@ActualizarGrupoSucursal');

    // $router->post('/cargarArchivo/reconocimiento-pagos', 'Sistema\CargarArchivo\ReportePagos\ReconocimientoPagosController@CargarReconocimiento');
    // $router->post('/cargarArchivo/promociones-liquidadas', 'Sistema\CargarArchivo\ReportePagos\PromocionesLiquidadasController@PromocionesLiquidadas');

    $router->post('/cargarArchivo/productos', 'Sistema\CargarArchivo\Productos\ProductosCargarController@ActualiazarCargarProductos');
    $router->post('/cargarArchivo/prueba/soldto', 'Sistema\CargarArchivo\Prueba\PruebaController@prueba');
    $router->post('/asdasd', 'Sistema\CargarArchivo\Prueba\PruebaController@prueba');
    $router->post('/fechas/mostrar/fechas', 'Sistema\Fechas\Mostrar\FechasMostrarController@mostrarFechas');

    $router->post('/mostrar/controlArchivos', 'Sistema\ControlArchivos\Mostrar\ControlArchivosMostrarController@MostrarControlArchivos');
    $router->post('/mostrar/archivos-subidos', 'Sistema\ControlArchivos\Mostrar\ControlArchivosMostrarController@MostrarArchivosSubidos');
    $router->post('/eliminar/archivos-subidos', 'Sistema\ControlArchivos\Eliminar\EliminarControlArchivosController@EliminarControlArchivos');

    $router->post('/controlVentas/estadistica/xzona', 'Sistema\Modulos\ControlVentas\VentasXZonasController@VentasXZonas');
    $router->post('/controlVentas/estadistica/xgrafico', 'Sistema\Modulos\ControlVentas\VentasXZonasController@VentasXControl');

    $router->post('/control/promociones/lista', 'Sistema\Modulos\Control\ControlPromociones\TablaPromocionesController@MostrarPromociones');

    $router->post('/configuracion/crear/rebateTrimestral', 'Sistema\Trimestre\CrearTrimestreController@CrearTrimestre');
    $router->get('/configuracion/actualizar/rebateTrimestral/{fecid}', 'Sistema\Trimestre\ActualizarTrimestreController@ActualizarTrimestre');
    $router->post('/configuracion/actualizar/rebateTrimestral/todosmeses', 'Sistema\Trimestre\ActualizarTrimestreTodosMesesController@ActualizarTrimestreTodosMeses');

    $router->post('/perfil/editar/editarPerfil', 'Sistema\Perfil\Editar\EditarPerfilController@EditarPerfil');
    $router->post('/perfil/mostrar/novedades', 'Sistema\Perfil\Mostrar\MostrarNovedadesController@MostrarNovedades');
    $router->post('/perfil/editar', 'Sistema\Perfil\Editar\EditarPerfilController@EditarPerfilNuevo');

    // CONTROL SELL OUT
    $router->post('/obtenerSellOutExcelMesAcutal', 'Sistema\SellOut\CargarExcelMesActualController@CargarExcelMesActual');
    $router->get('/obtenerSellOutMesEspecificoWeb/{anioSelec}/{mesSelec}', 'Sistema\SellOut\CargarSellOutController@CargarSellOutMesEspecificoWeb');

    // PROMOCIONES
    $router->get('/quitarGratisPromociones/{fecid}', 'Sistema\Promociones\Editar\PromocionEditarGratisController@QuitarGratisPromociones');

    // LISTA DE PRECIOS
    $router->post('/obtener-grupos-disponibles', 'Sistema\ListaPrecios\ArmarExcelListapreciosController@ObtenerGruposPermitidos');
    $router->post('/exportar-excel-lista-precios', 'Sistema\ListaPrecios\ArmarExcelListapreciosController@ArmarExcelListaprecios');
    $router->post('/eliminar-lista-precio', 'Sistema\ListaPrecios\EliminarListaPreciosController@EliminarListaPrecios');
    $router->post('/editar-lista-precio', 'Sistema\ListaPrecios\EditarListaPreciosController@EditarListaPrecios');

    // DESCARGAS ENVIAR ARCHIVO CORREO
    $router->post('/descargas-enviar-correo', 'Sistema\Descargas\ConvertirExcelController@ConvertirExcel');
    $router->post('/enviar-correo-adjunto', 'Sistema\Descargas\ConvertirExcelController@EnviarCorreo');


    // SMART VIEW V2
    // REBATE
    $router->post('/mostrar-rebates', 'Sistema\Modulos\Rebate\Mostrar\MostrarRebateController@MostrarRebate');
    $router->post('/crear-varios-rebate', 'Sistema\Modulos\Rebate\Crear\CrearRebateController@CrearRebate');
    $router->post('/eliminar-rebate-mensual', 'Sistema\Modulos\Rebate\Eliminar\EliminarRebateController@EliminarRebate');

    // NOTIFICACIONES
    $router->post('/mostrar-notificaciones-usuario', 'Sistema\Notificaciones\MostrarNotificacionesUsuarioController@MostrarNotificacionesUsuario');
    $router->post('/guardar-leido-notificaciones-usuario', 'Sistema\Notificaciones\VerNotificacionesUsuarioController@VerNotificacionesUsuario');

    // ELEMENTOS ENVIADOS
    $router->post('/mostrar-elementos-enviados', 'Sistema\ElementrosEnviados\MostrarElementosEnviadosController@MostrarElementosEnviados');
    $router->post('/eliminar-elementos-enviados', 'Sistema\ElementrosEnviados\EliminarElementosEnviadosController@EliminarElementosEnviados');
    $router->post('/mostrar-tipos-elementos-enviados', 'Sistema\ElementrosEnviados\MostrarTiposElementosEnviadosController@MostrarTiposElementosEnviados');

    // OBTENER DATA DE REBATE BONUS
    $router->post('/mostrar-rebate-bonus', 'Sistema\Ventas\Mostrar\MostrarRebateBonusController@MostrarRebateBonus');
    $router->post('/crear-rebate-bonus', 'Sistema\Ventas\Crear\CrearRebateBonusController@CrearRebateBonus');

    // OBTENER DATA DE REBATE TRIMESTRAL
    $router->post('/mostrar-rebate-trimestral', 'Sistema\Ventas\Mostrar\MostrarRebateTrismestralController@MostrarRebateTrismestral');
    $router->post('/crear-varios-rebate-trimestral', 'Sistema\Ventas\Crear\CrearRebateTrimestralController@CrearRebateTrimestral');
    $router->post('/eliminar-rebate-trimestral', 'Sistema\Ventas\Eliminar\EliminarRebateTrimestralController@EliminarTrimiestreRebate');

    // STATUS
    $router->post('/status/obtener', 'Sistema\Status\MetObtenerStatusController@MetObtenerStatus');
    $router->get('/status/v2/obtener', 'Sistema\Status\MetObtenerStatusController@MetObtenerStatusV2');
    $router->post('/status/areas', 'Sistema\Status\MetObtenerAreasController@MetObtenerAreas');
    //ENVIO CORREO DE LA VISTA ADMINISTRADOR
    $router->post('/correo-usuarios-nuevos', 'Sistema\Administrador\Usuarios\Mail\MetCorreoUsuarioController@MetCorreoUsuario');
    //CREAR REGISTROS COACONTROLARCHIVOS
    $router->post('/crear-registros-coa', 'Sistema\ControlArchivos\Complementos\MetCrearRegistrosControlArchivosController@MetCrearRegistrosControlArchivos');
    $router->post('/obtener-detalles-promocional', 'Sistema\Status\MetObtenerDetallesMecanicaPromocionalController@MetObtenerDetallesMecanicaPromocional');
// });

// CONTROL DE PRODUCTOS
$router->post('/control-promociones/mostrar-productos', 'Sistema\ControlProductos\MostrarProductosController@MostrarProductos');
$router->get('/control-promociones/modificar-imagen-productos', 'Sistema\ControlProductos\MostrarProductosController@ModificarImagenProductos');
$router->post('/control-promociones/asignar-imagen-producto', 'Sistema\ControlProductos\MostrarProductosController@AsignarImagenProducto');
$router->post('/control-promociones/eliminar-imagenes-productos', 'Sistema\ControlProductos\MostrarProductosController@EliminarImagenProducto');
$router->post('/control-promociones/asignar-imagen-productos-prueba', 'Sistema\ControlProductos\MostrarProductosController@AisngarImagensColumnasPrueba');
$router->post('/control-promociones/asignar-sku-productos', 'Sistema\ControlProductos\AsignarSkuController@AsignarSku');
$router->get('/obtener-status-imagenes', 'Sistema\SinAsignar\MetEnviarStatusImagenesController@MetEnviarStatusImagenes');

$router->get('/cargarArchivo/leerpromociones/{nombreArchivo}', 'Sistema\CargarArchivo\Promociones\CargarArchivoPromocionesController@LeerCargarArchivo');

$router->get('/armarRebateBonus/{fecid}', 'Sistema\RebatesBonus\AsignarSucursalesController@AsiganarSucursales');
$router->get('/armarCategoriasRebateBonus/{fecid}/{rbbid}', 'Sistema\RebatesBonus\AsignarSucursalesController@ActualizarCategoriasBonus');

$router->get('/mostrar/tdis', 'Sistema\Tdi\Mostrar\TdisMostrarController@MostrarTdis');
$router->get('/consumirApi', 'Sistema\SellOut\CargarSellOutController@CargarSellOutTodo');

$router->get('/obtenerSellOutTodo', 'Sistema\SellOut\CargarSellOutController@CargarSellOutTodo');
$router->get('/obtenerSellOutDiario', 'Sistema\SellOut\CargarSellOutController@CargarSellOutDiario');
$router->get('/obtenerSellOutEspecifico/{anioSelec}/{mesSelec}/{diaSelec}', 'Sistema\SellOut\CargarSellOutController@CargarSellOutEspecifico');
$router->get('/obtenerSellOutEspecificoWeb/{anioSelec}/{mesSelec}/{diaSelec}', 'Sistema\SellOut\CargarSellOutController@CargarSellOutEspecificoWeb');

// SELL OUT CONSOLIDADO
$router->get('/obtenerSOXCategoria/{anioSelec}/{mesSelec}', 'Sistema\SellOut\CargarSOXCategoriaController@CargarSOXCategoria');
$router->get('/obtenerSOXSoldTo/{anioSelec}/{mesSelec}', 'Sistema\SellOut\CargarSOXSoldtoController@CargarSOXSoldTo');



$router->get('/actualizarTogo/{fecid}', 'salvacionController@ActualizarToGo');
$router->get('/ActualizarSucursales', 'salvacionController@ActualizarSucursales');
$router->get('/quitar-decimales/{fecid}', 'salvacionController@QuitarDecimales');
$router->post('/eliminar-promociones-zona', 'salvacionController@EliminarZonaPromociones');
$router->get('/mecanicas-unicas-promociones/{fecid}', 'salvacionController@MecanicasUnicas');





// SELL OUT
$router->get('/ws/obtenerSellOutEspecifico/{anio}/{mes}/{dia}', 'Sistema\SellOut\ObtenerSellOutController@ObtenerSellOutEspecifico');
$router->get('/ws/obtenerSellOutDiario', 'Sistema\SellOut\ObtenerSellOutController@ObtenerSellOutActual');
$router->get('/ws/obtenerSellOutTodo', 'Sistema\SellOut\ObtenerSellOutController@ObtenerTodoSellOut');



$router->post('/cargarArchivo/reconocimiento-pagos', 'Sistema\CargarArchivo\ReportePagos\ReconocimientoPagosController@CargarReconocimiento');
$router->post('/cargarArchivo/promociones-liquidadas', 'Sistema\CargarArchivo\ReportePagos\PromocionesLiquidadasController@PromocionesLiquidadas');


$router->get('/actualizar-si-distribuidoras-activas/{fecid}', 'salvacionController@AsignarSi');

$router->get('/mostrar-promociones-pdf', 'Sistema\Promociones\Mostrar\PdfPromocionesController@MostrarPdfPromociones');


// SALVACION
$router->get('/organizar-lista-precios-maestra/{fecid}', 'salvacionController@OrganizarListaPreciosMaestras');

$router->post('/reporte-auditoria-login','Sistema\Administrador\Usuarios\Reportes\GenerarExcelAuditoriaLoginController@GenerarReporteAuditoriaLogin');
$router->post('/reporte-usuarios','Sistema\Administrador\Usuarios\Reportes\GenerarExcelUsuariosController@GenerarExcelUsuario');
$router->post('/reporte-archivos-subidos', 'Sistema\ControlArchivos\Reportes\GenerarExcelArchivosSubidosController@GenerarExcelArchivosSubidos');
$router->post('/reportes-logicaLP','Sistema\CargarArchivo\ListaPrecios\CargarListaPreciosController@DividirCG');
$router->get('/mostrar-tipo-rebates','Sistema\CargarArchivo\ListaPrecios\TipoRebate\MostrarTipoRebateDescargaLPController@MostrarTipoRebate');
$router->get('/mostrar-canales-sucursales', 'Sistema\CargarArchivo\ListaPrecios\CanalesSucursales\MostrarCanalesSucursalesDescargaLPController@MostrarCanalesSucursales');

// $router->post('/prueba', 'MailController@prueba');


// BOLIVIA
// $router->get('/bo/mostrar-cubo-so/{anio}/{mes}/{dia}/{limit}', 'Bolivia\CuboSO\MetObtenerSOController@ObtenerSO');
$router->get('/bo/mostrar-cubo-so/{fecha}', 'Bolivia\CuboSO\MetObtenerSOController@ObtenerSO');

// $router->get('/bo/agregar-so-obtenido-cubo/{anio}/{mes}/{dia}/{limit}', 'Bolivia\CuboSO\MetAgregarSOController@MetAgregarSO');
$router->get('/bo/agregar-so-obtenido-cubo/{fecha}', 'Bolivia\CuboSO\MetAgregarSOController@MetAgregarSO');

$router->post('/obtener-nombre-archivo-seleccionado-descarga', 'Bolivia\CuboSO\MetObtenerArchivoSOController@MetObtenerArchivoSO');
$router->post('/obtener-filtros-empresas-region', 'Bolivia\CuboSO\MetObtenerArchivoSOController@MetObtenerFiltros');