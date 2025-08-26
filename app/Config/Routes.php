<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// FRONTEND
$routes->get('/', 'Home::index');

// API REST
$routes->group('api', ['filter' => 'cors'],  static function ($routes) {
    $routes->resource('tasks', ['controller' => 'Api\TaskController']);
});
