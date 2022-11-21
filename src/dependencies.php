<?php

use Slim\App;
use Medoo\Medoo;

return function (App $app) {
    $container = $app->getContainer();

    // view renderer
    $container['renderer'] = function ($c) {
        $settings = $c->get('settings')['renderer'];
        return new \Slim\Views\PhpRenderer($settings['template_path']);
    };
    $container['flash'] = function () {
        return new \Slim\Flash\Messages();
    };
    // View Twig
    $container['view'] = function ($c) {
        $settings = $c->get('settings')['renderer'];
        $view = new \Slim\Views\Twig($settings['template_path'], [
            'cache' => false,
            'debug' => true
        ]);

        // Instantiate and add Slim specific extension
        $router = $c->get('router');
        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
        $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
        $view->addExtension(new Knlv\Slim\Views\TwigMessages(
            new Slim\Flash\Messages()
        ));
        $view->addExtension(new \Twig\Extension\DebugExtension());
        $environment = $view->getEnvironment();
        $environment->addGlobal('session', $_SESSION);
        // $view->global('')
        return $view;
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };
    // Medoo
    $container['db'] = function ($c) {
        $dbConfig = $c->get('settings')['db'];
        return new Medoo([
            'database_type' => 'mysql',
            'server' => $dbConfig['host'],
            'database_name' => $dbConfig['db_name'],
            'username' => $dbConfig['db_user'],
            'password' => $dbConfig['db_pass'],
        ]);
    };
};
