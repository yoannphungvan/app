<?php

use PROJECT\Services\Shared\Application\Application;

define('PATH_ROOT', dirname(dirname(__DIR__)));

// Autoload
require_once PATH_ROOT . '/vendor/be/autoload.php';

$app = new Application();

// Paths
$app->setRootPath(PATH_ROOT);
$app->setPath('logs', 'logs');
$app->setPath('configs', 'configs/env');
$app->setPath('routes', 'configs/routes');
$app->setPath('locales', 'locales');
$app->setPath('src', 'src/be');
$app->setPath('services', 'src/be/services');
$app->setPath('bundles', 'src/be/services/bundles');
$app->setPath('middlewares', 'src/be/services/shared/middlewares');
$app->setPath('vendor', 'vendor/be');
$app->setPath('web', 'web');
$app->setPath('views', 'src/views');

// Configs
$app->setConfig($app->getPath('configs') . '/configs-shared.php');
$app->setConfig($app->getPath('configs') . '/'. $app->getEnv() . '/configs.php');

// Locales
$app->setLocale(
	$app->configs['localisation']['domain'], 
	$app->getPath('locales'), 
	$app->configs['localisation']['locale'], 
	$app->configs['localisation']['charset']
);

// Middlewares
$app->setMiddleware($app->getPath('middlewares') . '/authentification.php'); 
$app->setMiddleware($app->getPath('middlewares') . '/errorHandler.php');
$app->setMiddleware($app->getPath('middlewares') . '/jsonDecoder.php');
$app->setMiddleware($app->getPath('middlewares') . '/response.php');
 
// Routes
$app->setRoutes($app->getPath('routes') . '/default.php');

$app->setExternalServices($app->getPath('services') . '/common.php');
$app->setService('Utils', 'PROJECT\Services\Shared\Helpers\Utils');

// Bundles
$app->setBundle('user', $app->getPath('bundles') . '/user', true); 

return $app;
