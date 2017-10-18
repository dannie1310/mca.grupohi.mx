<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
// Rutas de Paginas
Route::get('/', 'PagesController@home')->name('home');
Route::get('index', 'PagesController@index')->name('index');
Route::get('proyectos', 'PagesController@proyectos')->name('proyectos');

Route::get('tickets/validar', 'TicketsController@show')->name('tickets.show');
Route::get('tickets/control', 'TicketsController@index')->name('tickets.index');

// Rutas de Autenticación
Route::get('auth/login', [
        'as' => 'auth.login',
        'uses' => 'Auth\AuthController@getLogin'
    ]);

    Route::post('auth/login', [
        'as' => 'auth.login',
        'uses' => 'Auth\AuthController@postLogin'
    ]);

    Route::get('auth/logout', [
        'as' => 'auth.logout',
        'uses' => 'Auth\AuthController@getLogout'
    ]);

// Rutas de contexto...
Route::get('/context/{databaseName}/{id}', 'ContextController@set')
    ->name('context.set')
    ->where(['databaseName' => '[aA-zZ0-9_-]+', 'id' => '[0-9]+']);

Route::get('origenes/{origenes}/tiros', 'OrigenesTirosController@index')->name('origenes.tiros.index');
Route::get('camiones/{camiones}/cubicacion', 'CamionesController@getCubicacion');

// Rutas de Catalogos
//Route::group(['middleware' => ['permission:control-catalogos']], function () {
    Route::get('origenes_usuarios', 'PagesController@origenes_usuarios')->name('origenes_usuarios.index');
    Route::resource('materiales', 'MaterialesController');
    Route::resource('marcas', 'MarcasController');
    Route::resource('sindicatos', 'SindicatosController');
    Route::resource('origenes', 'OrigenesController');
    Route::resource('tiros', 'TirosController');
    Route::resource('rutas', 'RutasController');
    Route::resource('ruta.archivos', 'RutaArchivosController');
    Route::resource('camiones', 'CamionesController');
    Route::resource('camion.imagenes', 'CamionImagenesController');
    Route::resource('tarifas_material', 'TarifasMaterialController');
    Route::resource('tarifas_peso', 'TarifasPesoController');
    Route::resource('tarifas_tiporuta', 'TarifasTipoRutaController');
    Route::resource('operadores', 'OperadoresController');
    Route::resource('empresas', 'EmpresasController');
    Route::resource('fda_material', 'FDAMaterialController');
    Route::resource('fda_banco_material', 'FDABancoMaterialController');
    Route::resource('etapas', 'EtapasController');
    Route::get('centroscostos', 'CentrosCostosController@index')->name('centroscostos.index');
    Route::get('centroscostos/{centroscosto}', 'CentrosCostosController@show')->name('centroscostos.show');
    Route::post('centroscostos', 'CentrosCostosController@store')->name('centroscostos.store');
    Route::get('centroscostos/create/{IdPadre}', 'CentrosCostosController@create')->name('centroscostos.create');
    Route::get('centroscostos/{centroscostos}/edit', 'CentrosCostosController@edit')->name('centroscostos.edit');
    Route::patch('centroscostos/{centroscostos}', 'CentrosCostosController@update')->name('centroscostos.update');
    Route::delete('centroscostos/{centroscostos}', 'CentrosCostosController@destroy')->name('centroscostos.destroy');
    Route::get('usuarios/{usuarios}/origenes', 'OrigenesUsuariosController@index');
    Route::post('usuarios/{usuarios}/origenes/{origenes}', 'OrigenesUsuariosController@store');
    Route::get('usuarios', 'UserController@index');
    Route::patch('usuarios/{usuarios}', 'UserController@update');
    Route::resource('telefonos', 'TelefonosController');
    Route::resource('impresoras', 'ImpresorasController');
//});


    //Rutas de Viajes Netos
Route::get('viajes_netos', 'ViajesNetosController@index')->name('viajes_netos.index');
Route::get('viajes_netos/create', 'ViajesNetosController@create')->name('viajes_netos.create');
Route::group(['prefix' => 'viajes_netos'], function() {
    Route::post('completa', 'ViajesNetosController@store');
    Route::post('manual', [
        'as' => 'viajes_netos.manual.store',
        'uses' => 'ViajesNetosController@store',
        'middleware' => ['permission:ingresar-viajes-manuales']
    ]);
});
Route::get('viajes_netos/edit' , 'ViajesNetosController@edit')->name('viajes_netos.edit');
Route::patch('viajes_netos', 'ViajesNetosController@update')->name('viajes_netos.update');
Route::group(['prefix' => 'viajes_netos'], function() {
    Route::patch('autorizar', 'ViajesNetosController@update')->name('viajes_netos.autorizar');
});
Route::get('viajes_netos/{viaje_neto}', 'ViajesNetosController@show')->name('viajes_netos.show');

//PDF Routes
Route::group(['prefix' => 'pdf'], function () {

    Route::get('conciliacion/{id}', [
        'as'   => 'pfd.conciliacion',
        'uses' => 'PDFController@conciliacion'
    ]);

    Route::get('viajes_netos', [
        'as' => 'pdf.viajes_netos',
        'uses' => 'PDFController@viajes_netos'
    ]);

    Route::get('corte/{corte}', [
        'as' => 'pdf.corte',
        'uses' => 'PDFController@corte'
    ]);
    
    Route::get('viajes_netos_conflicto', [
        'as' => 'pdf.viajes_netos_conflicto',
        'uses' => 'PDFController@viajes_netos_conflicto'
    ]);

    Route::get('telefonos-impresoras', [
        'as' => 'pdf.telefonos-impresoras',
        'uses' => 'PDFController@telefonos_impresoras'
    ]);
    Route::get('configuracion-diaria', [
        'as' => 'pdf.configuracion-diaria',
        'uses' => 'PDFController@configuracion_diaria'
    ]);
});

//XLS Routes
Route::group(['prefix' => 'xls'], function () {

    Route::get('conciliacion/{id}', [
        'as'   => 'xls.conciliacion',
        'uses' => 'XLSController@conciliacion'
    ]);
    Route::get('conciliaciones', [
        'as'   => 'xls.conciliaciones',
        'uses' => 'XLSController@conciliaciones'
    ]);

});

//Reportes Routes
Route::group(['prefix' => 'reportes'], function () {
    Route::get('viajes_netos/create', [
        'as'   => 'reportes.viajes_netos.diario.create',
        'uses' => 'ReportesController@viajes_netos_create'
    ]);
    Route::get('viajes_netos/show', [
        'as'   => 'reportes.viajes_netos.diario.show',
        'uses' => 'ReportesController@viajes_netos_show'
    ]);
    Route::get('viajes_netos/completo/create', [
        'as'   => 'reportes.viajes_netos.completo.create',
        'uses' => 'ReportesController@viajes_netos_completo_create'
    ]);
    Route::get('viajes_netos/completo/show', [
        'as'   => 'reportes.viajes_netos.completo.show',
        'uses' => 'ReportesController@viajes_netos_completo_show'
    ]);
    Route::get('viajes_netos/auditoria/create', [
        'as'   => 'reportes.viajes_netos.auditoria.create',
        'uses' => 'ReportesController@viajes_netos_auditoria_create'
    ]);
    Route::get('viajes_netos/auditoria/show', [
        'as'   => 'reportes.viajes_netos.auditoria.show',
        'uses' => 'ReportesController@viajes_netos_auditoria_show'
    ]);
    Route::get('inicio_viajes/create', [
        'as'   => 'reportes.inicio_viajes.create',
        'uses' => 'ReportesController@inicio_viajes_create'
    ]);
    Route::get('inicio_viajes/show', [
        'as'   => 'reportes.inicio_viajes.show',
        'uses' => 'ReportesController@inicio_viajes_show'
    ]);
    Route::get('conciliacion_detalle/create', [
        'as'   => 'reportes.conciliacion_detalle.create',
        'uses' => 'ReportesController@conciliacion_detalle_create'
    ]);
    Route::get('conciliacion_detalle/show', [
        'as'   => 'reportes.conciliacion_detalle.show',
        'uses' => 'ReportesController@conciliacion_detalle_show'
    ]);
});

Route::resource('conciliaciones', 'ConciliacionesController');

Route::post('conciliacion/{conciliacion}/detalles', 'ConciliacionesDetallesController@store')->name('conciliaciones.detalles.store');
Route::get('conciliacion/{conciliacion}/detalles', 'ConciliacionesDetallesController@index')->name('conciliaciones.detalles.index');
Route::delete('conciliacion/{conciliacion}/detalles/{detalle}', 'ConciliacionesDetallesController@destroy')->name('conciliaciones.detalles.destroy');
Route::get('conciliacion_info_carga/{filename}', 'ConciliacionesDetallesController@detalle_carga')->name('conciliacion.info');

Route::get('viajes', 'ViajesController@index')->name('viajes.index');
Route::patch('viajes/{viaje}', 'ViajesController@update');
Route::get('viajes/edit', 'ViajesController@edit')->name('viajes.edit');

//Rutas de corte de checador
Route::get('corte/create', 'CorteController@create')->name('corte.create');
Route::post('corte', 'CorteController@store')->name('corte.store');
Route::get('corte/{corte}', 'CorteController@show')->name('corte.show');
Route::get('corte', 'CorteController@index')->name('corte.index');
Route::get('corte/{corte}/edit', 'CorteController@edit')->name('corte.edit');
Route::patch('corte/{corte}', 'CorteController@update')->name('corte.update');

Route::get('corte/{corte}/viajes_netos', 'CorteViajesController@index')->name('corte.viajes_netos.index');
Route::patch('corte/{corte}/viajes_netos/{viaje_neto}', 'CorteViajesController@update')->name('corte.viajes_netos.update');


//Rutas de Configuración Diaria
Route::get('configuracion-diaria', 'ConfiguracionDiariaController@index')->name('configuracion-diaria.index');
Route::get('init/configuracion-diaria', 'ConfiguracionDiariaController@init')->name('configuracion-diaria.init');
Route::post('configuracion-diaria', 'ConfiguracionDiariaController@store');
Route::delete('configuracion-diaria/{id}', 'ConfiguracionDiariaController@destroy');

//Rutas de Roles y Permisos
Route::resource('user.roles', 'UserRolesController');

//Rutas de Administración
Route::group(['prefix' => 'administracion', 'middleware' => ['ability:administrador-sistema|administrador-permisos,auditoria-resumen-configuracion|permisos_cierre_x_periodo|consulta-asignacion-proyecto']], function () {
    Route::get('roles_permisos', 'RolesPermisosController@roles_permisos')->name('administracion.roles_permisos');
    Route::get('cierre_usuario_configuracion/cierre_periodo','AdministracionCierrePeriodoController@index')->name('validar-cierre-periodo.configuracion');
    Route::get('cierre_usuario_configuracion/cierre_periodo/init','AdministracionCierrePeriodoController@init');
    Route::post('cierre_usuario_configuracion/cierre_periodo/save', 'AdministracionCierrePeriodoController@save')->name('cierre.save');
    Route::get('roles_permisos/init', 'RolesPermisosController@init');
    Route::post('roles_permisos/roles', 'RolesPermisosController@roles_store')->name('roles.store');
    Route::post('roles_permisos/permisos', 'RolesPermisosController@permisos_store')->name('permisos.store');
    Route::post('roles_permisos/permisos_rol_store', 'RolesPermisosController@permisos_rol_store')->name('permisos_roles.store');
    Route::post('roles_permisos/rol_usuario_store', 'RolesPermisosController@roles_usuario_store')->name('rol_usuario.store');
    Route::post('roles_permisos/permisos_roles','RolesPermisosController@permisos_roles')->name('permisos.roles');

});
Route::resource('usuarios_sistema', 'UsuarioSistemaController');


Route::resource('telefonos-impresoras', 'TelefonosImpresorasController');


Route::group(['prefix' => 'csv'],function () {
    Route::get('rutas', 'CSVController@rutas')->name('csv.rutas');
    Route::get('origenes', 'CSVController@origenes')->name('csv.origenes');
    Route::get('tiros', 'CSVController@tiros')->name('csv.tiros');
    Route::get('camiones', 'CSVController@camiones')->name('csv.camiones');
    Route::get('materiales', 'CSVController@materiales')->name('csv.materiales');
    Route::get('empresas', 'CSVController@empresas')->name('csv.empresas');
    Route::get('sindicatos', 'CSVController@sindicatos')->name('csv.sindicatos');
    Route::get('centros-costos', 'CSVController@centros_costos')->name('csv.centros-costos');
    Route::get('etapas-proyecto', 'CSVController@etapas_proyecto')->name('csv.etapas-proyecto');
    Route::get('fda-material', 'CSVController@fda_material')->name('csv.fda-material');
    Route::get('fda-banco-material', 'CSVController@fda_banco_material')->name('csv.fda-banco-material');
    Route::get('marcas', 'CSVController@marcas')->name('csv.marcas');
    Route::get('operadores', 'CSVController@operadores')->name('csv.operadores');
    Route::get('tarifas-material', 'CSVController@tarifas_material')->name('csv.tarifas-material');
    Route::get('tarifas-peso', 'CSVController@tarifas_peso')->name('csv.tarifas-peso');
    Route::get('configuracion-checadores', 'CSVController@configuracion_checadores')->name('csv.configuracion-checadores');
    Route::get('impresoras', 'CSVController@impresoras')->name('csv.impresoras');
    Route::get('telefonos', 'CSVController@telefonos')->name('csv.telefonos');
    Route::get('usuario-rol', 'CSVController@usuario_rol')->name('csv.usuario_rol');
    Route::get('rol-permiso', 'CSVController@rol_permiso')->name('csv.rol_permiso');
    Route::get('usuario-permiso', 'CSVController@usuario_permiso')->name('csv.usuario_permiso');
});

Route::get('detalle_configuracion', 'DetalleAdministracionController@index')->name('detalle.configuracion');
/*
 * API Routes
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['middleware' => 'cors'], function($api) {

    // Autenticacion de las Api´s
    $api->post('authenticate', 'App\Http\Controllers\API\AuthController@authenticate');

    // Rutas de API Registro de Tags
    $api->post('tags_nuevos', 'App\Http\Controllers\API\TagsController@store');
    $api->get('tags_nuevos', 'App\Http\Controllers\API\TagsController@lista');

    $api->get('registro_camiones/{role}', 'App\Http\Controllers\API\RegistroCamionesController@index');
    $api->get('registro_camiones', 'App\Http\Controllers\API\RegistroCamionesController@lista');
    $api->post('registro_camiones/camion', 'App\Http\Controllers\API\RegistroCamionesController@camion_store');
    $api->post('registro_camiones/imagen', 'App\Http\Controllers\API\RegistroCamionesController@imagen_store');

    //Authenticate Routes
    /*$api->post('authenticate', 'Ghi\Http\Controllers\Api\Auth\AuthController@authenticate');

    $api->get('logout', 'Ghi\Http\Controllers\Api\Auth\AuthController@getLogout');


    $api->get('test', 'Ghi\Http\Controllers\Api\TestController@index');

    $api->get('almacenes', 'Ghi\Http\Controllers\Api\AlmacenesController@lists');
    
    /* Rutas de sincronizacion de actividad y actividades con dispositivos móviles*/
    /*$api->post('reporte-actividades', 'Ghi\Http\Controllers\Api\ReportesActividadController@store');  // recibe la actividad 

    $api->post('reporte-actividades/{idReporte}/actividades', 'Ghi\Http\Controllers\Api\ActividadesController@store'); // recibe las actividades

    // Rutas para la validación de sesiones de usuarios en los dispositivos móviles
    $api->get('sesion-movil/{usuario}', 'Ghi\Http\Controllers\Api\SesionController@show');

    $api->patch('sesion-movil/{usuario}', 'Ghi\Http\Controllers\Api\SesionController@update');

    $api->post('sesion-movil', 'Ghi\Http\Controllers\Api\SesionController@store');*/
});
Route::resource('usuario_proyecto', 'UsuarioProyectoController');
Route::get('checkpermission/{permission}', 'UserController@checkpermission');


Route::get('historico/camiones/{id}', 'HistoricoController@camiones');
Route::get('historico/empresas/{id}', 'HistoricoController@empresas');
Route::get('historico/etapas/{id}', 'HistoricoController@etapasproyectos');
Route::get('historico/impresoras/{id}', 'HistoricoController@impresoras');
Route::get('historico/marcas/{id}', 'HistoricoController@marcas');
Route::get('historico/materiales/{id}', 'HistoricoController@materiales');
Route::get('historico/operadores/{id}', 'HistoricoController@operadores');
Route::get('historico/tiros/{id}', 'HistoricoController@tiros');
Route::get('historico/telefonos/{id}', 'HistoricoController@telefonos');
Route::get('historico/sindicatos/{id}', 'HistoricoController@sindicatos');
Route::get('historico/rutas/{id}', 'HistoricoController@rutas');
Route::get('historico/origenes/{id}', 'HistoricoController@origenes');

//Route::group(['middleware' => ['permission:consulta-solicitud-reactivar']], function () {
    Route::resource('solicitud-reactivacion', 'SolicitudReactivacionController');
//});

//Route::group(['middleware' => ['permission:consulta-solicitud-actualizar']], function () {
    Route::resource('solicitud-actualizacion', 'SolicitudActualizacionController');
//});
