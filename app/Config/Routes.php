<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Sitio público
$routes->get('/', 'Inicio::index');

$routes->get('carreras', 'Carreras::index');
$routes->get('carreras/(:segment)', 'Carreras::ver/$1');
$routes->get('carreras/(:segment)/(plan|resolucion)', 'Carreras::documento/$1/$2');

$routes->get('noticias', 'Noticias::index');
$routes->get('noticias/(:segment)', 'Noticias::ver/$1');

$routes->get('blog', 'Blog::index');
$routes->get('blog/(:segment)', 'Blog::ver/$1');

$routes->get('buscar', 'Buscar::index');
$routes->get('privacidad', 'Privacidad::index');
$routes->get('rss.xml', 'Feed::rss');
$routes->get('sitemap.xml', 'Feed::sitemap');

// Ingreso, registro de alumnos, recuperación de contraseña y salida
$routes->get('login', 'Login::index');
$routes->post('login/ingresar', 'Login::ingresar');
$routes->get('login/olvide', 'Login::olvide');
$routes->post('login/olvide', 'Login::enviarOlvide');
$routes->get('login/restablecer/(:segment)', 'Login::restablecer/$1');
$routes->post('login/restablecer', 'Login::guardarRestablecer');
$routes->get('registro', 'Registro::index');
$routes->post('registro', 'Registro::guardar');
$routes->get('login/google', 'LoginGoogle::ingresar');
$routes->get('login/google/volver', 'LoginGoogle::volver');
$routes->post('salir', 'Login::salir');

// Cualquier usuario con sesión iniciada
$routes->group('', ['filter' => 'auth:admin,editor,alumnado'], static function (RouteCollection $routes): void {
    $routes->post('blog/me-gusta/(:num)', 'Blog::meGusta/$1');
    $routes->post('blog/vista/(:num)', 'Blog::vista/$1');
    $routes->get('cuenta', 'Cuenta::index');
    $routes->post('cuenta/actualizar', 'Cuenta::actualizar');
    $routes->post('cuenta/password', 'Cuenta::password');
    $routes->post('cuenta/borrar', 'Cuenta::borrar');
});

// Panel: administradores y editores
$routes->group('admin', ['filter' => 'auth:admin,editor'], static function (RouteCollection $routes): void {
    $routes->get('/', 'AdminPanel::index');

    $routes->get('noticias', 'AdminNoticias::index');
    $routes->get('noticias/nuevo', 'AdminNoticias::nuevo');
    $routes->post('noticias/insertar', 'AdminNoticias::insertar');
    $routes->get('noticias/editar/(:num)', 'AdminNoticias::editar/$1');
    $routes->post('noticias/actualizar', 'AdminNoticias::actualizar');

    $routes->get('posteos', 'AdminPosteos::index');
    $routes->get('posteos/nuevo', 'AdminPosteos::nuevo');
    $routes->post('posteos/insertar', 'AdminPosteos::insertar');
    $routes->get('posteos/editar/(:num)', 'AdminPosteos::editar/$1');
    $routes->post('posteos/actualizar', 'AdminPosteos::actualizar');

    $routes->get('media', 'AdminMedia::index');
    $routes->get('media/nuevo', 'AdminMedia::nuevo');
    $routes->post('media/insertar', 'AdminMedia::insertar');
    $routes->get('media/editar/(:num)', 'AdminMedia::editar/$1');
    $routes->post('media/actualizar', 'AdminMedia::actualizar');
    $routes->post('media/borrar/(:num)', 'AdminMedia::borrar/$1');
    $routes->post('media/imagen-editor', 'AdminMedia::imagenEditor');
});

// Panel: solo administradores
$routes->group('admin', ['filter' => 'auth:admin'], static function (RouteCollection $routes): void {
    $routes->post('noticias/borrar/(:num)', 'AdminNoticias::borrar/$1');
    $routes->post('posteos/borrar/(:num)', 'AdminPosteos::borrar/$1');

    $routes->get('carreras', 'AdminCarreras::index');
    $routes->get('carreras/nuevo', 'AdminCarreras::nuevo');
    $routes->post('carreras/insertar', 'AdminCarreras::insertar');
    $routes->get('carreras/editar/(:num)', 'AdminCarreras::editar/$1');
    $routes->post('carreras/actualizar', 'AdminCarreras::actualizar');
    $routes->post('carreras/borrar/(:num)', 'AdminCarreras::borrar/$1');

    $routes->get('papelera/(noticias|posteos|carreras|media|usuarios)', 'AdminPapelera::index/$1');
    $routes->post('papelera/restaurar/(noticias|posteos|carreras|media|usuarios)/(:num)', 'AdminPapelera::restaurar/$1/$2');
    $routes->post('papelera/eliminar/(noticias|posteos|carreras|media|usuarios)/(:num)', 'AdminPapelera::eliminar/$1/$2');

    $routes->get('usuarios', 'AdminUsuarios::index');
    $routes->get('usuarios/nuevo', 'AdminUsuarios::nuevo');
    $routes->post('usuarios/insertar', 'AdminUsuarios::insertar');
    $routes->get('usuarios/editar/(:num)', 'AdminUsuarios::editar/$1');
    $routes->post('usuarios/actualizar', 'AdminUsuarios::actualizar');
    $routes->post('usuarios/borrar/(:num)', 'AdminUsuarios::borrar/$1');

    $routes->get('configuracion', 'AdminConfiguracion::index');
    $routes->post('configuracion/actualizar', 'AdminConfiguracion::actualizar');
});
