<?php

use App\Controller\ProductController;
use App\Controller\UserController;
use App\Middleware\Auth;
use Slim\App;


return function (App $app) {
  $app->get('/', UserController::class . ':index')->add(new Auth());
  $app->group('/auth', function ($app) {
    $app->get('/login', function ($request, $response, $args) {
      return $this->view->render($response, 'pages/login.twig');
    });
    $app->post('/login', UserController::class . ':login');
    $app->get('/register', function ($request, $response, $args) {
      if (Auth::isLogined()) {
        return $response->withRedirect('/');
      }
      return $this->view->render($response, 'pages/register.twig');
    });
    $app->post('/register', UserController::class . ':register');
    $app->post('/logout', function ($request, $response, $args) {
      session_destroy();
      return $response->withRedirect('/auth/login');
    })->add(new Auth());
  });
  // route group for dashboard
  $app->group('/dashboard', function ($app) {
    $app->get('/products', ProductController::class . ':toProducts');
  })->add(new Auth());
  // route group for api
  $app->group('/api', function ($app) {
    $app->get('/products', ProductController::class . ':showAll');
    $app->post('/products', ProductController::class . ':addProduct');
    $app->get('/products/{id}', ProductController::class . ':show');
    $app->delete('/products/{id}', ProductController::class . ':destroy');
  })->add(new Auth());
};
