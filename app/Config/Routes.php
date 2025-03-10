<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Index');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', 'Home::index');

//auto.pro
$routes->get('/test', 'Test::index');

$routes->get('/', 'Index::index');
$routes->add('/login', 'Index::login');
$routes->get('/logout', 'Index::logout');

$routes->get('/export', 'Index::export');

$routes->add('/settings', 'Index::settings');
//$routes->post('/settings', 'Index::settings');

$routes->add('/upload', 'Index::upload');
//$routes->post('/upload', 'Index::upload');

$routes->add('/doubles', 'Index::doubles');
//$routes->post('/doubles', 'Index::doubles');

$routes->get('/error-products', 'Index::errorProducts');
$routes->get('/list/(:num)', 'Index::listProducts/$1');
$routes->post('/search-products', 'Index::searchProducts');

$routes->get('/edit-product/(:num)', 'Index::editProduct/$1');
$routes->post('/edit-product', 'Index::editProduct');

$routes->get('/clear', 'Index::clear');
$routes->get('/results', 'Index::result');

//della.ua
$routes->add('/cargo-list', 'Index::cargoFilter');

//CRON group
$routes->get('/cron/c1min', 'Cron::c1min');
$routes->get('/cron/c2min', 'Cron::c2min');
$routes->get('/cron/c24hour', 'Cron::c24hour');

//CLI group
$routes->cli('/cron/12min', 'Cron::c1min');
$routes->cli('/cron/c2min', 'Cron::c2min');
$routes->cli('/cron/c24hour', 'Cron::c24hour');
$routes->cli('/', 'Cron::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
