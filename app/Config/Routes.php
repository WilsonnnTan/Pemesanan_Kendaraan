<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

// Root URL redirect ke login
$routes->get('/', 'Auth::login');

// Auth routes (tidak perlu filter)
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');

// Routes yang memerlukan autentikasi
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Dashboard::index');
    
    // Vehicle Booking routes
    $routes->get('vehicle-booking', 'VehicleBooking::index');
    $routes->get('vehicle-booking/create', 'VehicleBooking::create');
    $routes->post('vehicle-booking/store', 'VehicleBooking::store');
    $routes->get('vehicle-booking/edit/(:num)', 'VehicleBooking::edit/$1');
    $routes->post('vehicle-booking/update/(:num)', 'VehicleBooking::update/$1');
    $routes->get('vehicle-booking/delete/(:num)', 'VehicleBooking::delete/$1');
    
    // Approval routes (memerlukan role admin/approver)
    $routes->group('', ['filter' => 'admin'], function($routes) {
        $routes->get('approval', 'Approval::index');
        $routes->get('approval/(:num)', 'Approval::show/$1');
        $routes->post('approval/(:num)/approve', 'Approval::approve/$1');
    });
});

// Admin routes (memerlukan role admin)
$routes->group('admin', ['filter' => 'admin'], function($routes) {
    $routes->get('users', 'Admin::users');
    $routes->get('vehicles', 'Admin::vehicles');
    $routes->post('vehicles/add', 'Admin::addVehicle');
    $routes->get('vehicles/(:num)/assign-approvers', 'Admin::assignApprovers/$1');
    $routes->post('vehicles/(:num)/assign-approvers', 'Admin::assignApprovers/$1');
});

// Report routes (dengan filter auth)
$routes->group('report', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Report::index');
    $routes->get('export/excel', 'Report::export/excel');
    $routes->get('export/pdf', 'Report::export/pdf');
});

// Approval routes
$routes->group('approval', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Approval::index');
    $routes->post('approve/(:num)', 'Approval::approve/$1');
    $routes->post('reject/(:num)', 'Approval::reject/$1');
});