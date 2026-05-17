<?php

// =============================================
//  ROTAS DA APLICAÇÃO
// =============================================

// --- Autenticação ---
$router->get('/login',    'AuthController@showLogin');
$router->post('/login',   'AuthController@login');
$router->get('/logout',   'AuthController@logout');

// --- Dashboard ---
$router->get('/',          'DashboardController@index');
$router->get('/dashboard', 'DashboardController@index');

// --- Currículos ---
$router->get('/curriculos',             'CurriculoController@index');
$router->get('/curriculos/novo',        'CurriculoController@create');
$router->post('/curriculos',            'CurriculoController@store');
$router->get('/curriculos/:id',         'CurriculoController@show');
$router->get('/curriculos/:id/editar',  'CurriculoController@edit');
$router->post('/curriculos/:id',        'CurriculoController@update');
$router->post('/curriculos/:id/delete', 'CurriculoController@destroy');

// Processar com IA
$router->post('/curriculos/:id/processar', 'CurriculoController@processar');

// Download PDF padronizado
$router->get('/curriculos/:id/download', 'CurriculoController@download');

// --- Administração de Usuários (somente admin) ---
$router->get('/admin/usuarios',              'AdminController@usuarios');
$router->get('/admin/usuarios/novo',         'AdminController@novoUsuario');
$router->post('/admin/usuarios',             'AdminController@criarUsuario');
$router->get('/admin/usuarios/:id/editar',   'AdminController@editarUsuario');
$router->post('/admin/usuarios/:id',         'AdminController@atualizarUsuario');
$router->post('/admin/usuarios/:id/delete',  'AdminController@deletarUsuario');
$router->post('/admin/usuarios/:id/toggle',  'AdminController@toggleAtivo');
