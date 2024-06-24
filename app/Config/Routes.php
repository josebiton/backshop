<?php

use CodeIgniter\Router\RouteCollection;


/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('auth/login', 'auth\Auth::logueo');
$routes->post('auth/register', 'auth\Auth::register');
$routes->get('auth/ini','auth\Auth::x');


$routes->get('users', 'Users::index');
$routes->get('users/(:num)', 'Users::show/$1');
$routes->post('users', 'Users::store');
$routes->put('users/(:num)', 'Users::update/$1');
$routes->delete('users/(:num)', 'Users::delete/$1');


// CATEGORIAS
$routes->get('categorias', 'Categoria::index');
$routes->get('categorias/(:num)', 'Categoria::show/$1');
$routes->post('categorias', 'Categoria::store');
$routes->put('categorias/(:num)', 'Categoria::update/$1');
$routes->delete('categorias/(:num)', 'Categoria::delete/$1');

// PRODUCTOS
$routes->get('productos', 'Producto::index');
$routes->post('productos', 'Producto::create');
$routes->get('productos/(:num)', 'Producto::ProductosPorId/$1');
$routes->get('productos-categoria/(:num)', 'Producto::getProductosPorCategoria/$1');



// METODO PAGO EMPRESA
$routes->get('metodo-pago-emp', 'MetodoPagoEmpresa::index');
$routes->post('metodo-pago-emp', 'MetodoPagoEmpresa::store');

// METODO ENVIO EMPRESA
$routes->get('metodo-envio-emp', 'MetodoEnvioEmpresa::index');
$routes->post('metodo-envio-emp', 'MetodoEnvioEmpresa::store');

// PEDIDO
$routes->get('pedidos', 'Pedido::index');
$routes->post('pedidos', 'Pedido::store');

// DETALLE PEDIDO
$routes->get('detalle-pedidos', 'DetallePedido::index');
$routes->post('detalle-pedidos', 'DetallePedido::store');

// PERSONA
$routes->get('personas', 'Persona::index');
$routes->get('clientes_persona', 'Persona::clientesPersona');
$routes->get('personas/(:num)', 'Persona::show/$1');
$routes->post('personas', 'Persona::store');


