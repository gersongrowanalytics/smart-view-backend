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
    $router->post('/promociones/mostrar/categorias', 'Sistema\Promociones\Mostrar\CategoriasController@mostrarCategorias');
    $router->post('/promociones/mostrar/promociones', 'Sistema\Promociones\Mostrar\PromocionesMostrarController@mostrarPromociones');

});


