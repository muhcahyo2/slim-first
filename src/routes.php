<?php

use App\Controller\OrderController;
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
    $app->get('/orders', OrderController::class . ':toOrders');
  })->add(new Auth());
  // route group for api
  $app->group('/api', function ($app) {

    $app->get('/products', ProductController::class . ':showAll');
    $app->post('/products', ProductController::class . ':addProduct');
    $app->get('/products/ready', ProductController::class . ':getProductReady');
    $app->get('/products/{id}', ProductController::class . ':show');
    $app->put('/products/{id}', ProductController::class . ':update');
    $app->delete('/products/{id}', ProductController::class . ':destroy');

    $app->get('/orders', OrderController::class . ':showAll');
    $app->post('/orders', OrderController::class . ':addOrder');
    $app->post('/orders/{id}/done', OrderController::class . ':doneOrder');
    $app->delete('/orders/{id}', OrderController::class . ':destroy');
  })->add(new Auth());
};
