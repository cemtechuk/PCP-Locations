<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::loginPost');
$routes->get('/logout', 'AuthController::logout');

$routes->group('', ['filter' => 'admin'], function($routes) {
    $routes->get('/users', 'UsersController::index');
    $routes->get('/users/create', 'UsersController::create');
    $routes->post('/users/create', 'UsersController::store');
    $routes->get('/users/edit/(:num)', 'UsersController::edit/$1');
    $routes->post('/users/edit/(:num)', 'UsersController::update/$1');
    $routes->post('/users/delete/(:num)', 'UsersController::delete/$1');

    $routes->get('/apikeys', 'ApiKeysController::index');
    $routes->get('/apikeys/create', 'ApiKeysController::create');
    $routes->post('/apikeys/create', 'ApiKeysController::store');
    $routes->post('/apikeys/revoke/(:num)', 'ApiKeysController::revoke/$1');
    $routes->post('/apikeys/delete/(:num)', 'ApiKeysController::delete/$1');

    $routes->get('/import', 'ImportController::index');
    $routes->post('/import', 'ImportController::store');
});

// Public REST API — authenticated via X-API-Key header, no session required
$routes->group('api/v1', ['filter' => 'apikey'], function($routes) {
    $routes->get('exchanges', 'ApiController::exchanges');
    $routes->get('exchanges/(:segment)/(:segment)', 'ApiController::exchangeDetail/$1/$2');
    $routes->get('cabinets/(:num)', 'ApiController::cabinet/$1');
    $routes->get('nearby', 'ApiController::nearby');
    $routes->get('search', 'ApiController::search');
});

// Cabinet & Exchange management — editor and admin only
$routes->group('', ['filter' => 'editor'], function($routes) {
    $routes->get('/exchange/create', 'CabinetController::createExchange');
    $routes->post('/exchange/create', 'CabinetController::storeExchange');
    $routes->get('/cabinet/create/(:segment)/(:segment)', 'CabinetController::create/$1/$2');
    $routes->post('/cabinet/create', 'CabinetController::store');
    $routes->get('/cabinet/edit/(:num)', 'CabinetController::edit/$1');
    $routes->post('/cabinet/edit/(:num)', 'CabinetController::update/$1');
});

$routes->get('/', 'Home::index');
$routes->get('/api/exchanges', 'Home::exchangeSearch');   // AJAX live search
$routes->get('/api/nearby', 'Home::nearbyExchanges');      // GPS nearest exchanges
$routes->get('/exchange/(:segment)/(:segment)', 'Home::exchangeDetail/$1/$2'); // /exchange/CL/HACKNEY
$routes->get('/cabinet/(:num)', 'Home::cabinet/$1');
