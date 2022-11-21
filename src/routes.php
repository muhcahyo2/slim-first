<?php

use App\Controller\UserController;
use App\Middleware\Auth;
use Slim\App;


return function (App $app) {
  $app->get('/', UserController::class . ':index');
  $app->group('/auth', function($app){
    $app->post('/login', UserController::class. ':login');
    $app->post('/singup', UserController::class. ':register');
    $app->post('/logout', function($request, $response, $args){
      session_destroy();
      return $response->withRedirect('/');
    })->add(new Auth());
  });
};
