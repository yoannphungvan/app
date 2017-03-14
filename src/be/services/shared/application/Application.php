<?php

namespace PROJECT\Services\Shared\Application;

use Silex\Application as SilexApplication;

class Application extends SilexApplication
{
    protected $app;
    protected $env;
    protected $paths;
    protected $routes;
    protected $controllers;
    protected $services;
    protected $dependenciesService;
    protected $bundles;
    protected $rootPath;
    protected $modelForRoute;
    protected $serviceForRoute;

    public $configs;

    private $dependencies = [
        'Paths'       => 'PROJECT\Services\Shared\Application\Paths',
        'Configs'     => 'PROJECT\Services\Shared\Application\Configs',
        'Routing'     => 'PROJECT\Services\Shared\Application\Routing',
        'Environment' => 'PROJECT\Services\Shared\Application\Environment',
        'Bundles'     => 'PROJECT\Services\Shared\Application\Bundles',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->app = parent::__construct();

        // Load dependencies
        $this->dependenciesService = new Dependencies($this);
        $this->dependenciesService->loadDependencies($this, $this->dependencies);

        $this->pathsService   = $this->dependenciesService->getDependency($this, 'Paths');
        $this->configsService = $this->dependenciesService->getDependency($this, 'Configs');
        $this->routingService = $this->dependenciesService->getDependency($this, 'Routing');
        $this->envService     = $this->dependenciesService->getDependency($this, 'Environment');
        $this->bundlesService = $this->dependenciesService->getDependency($this, 'Bundles');
    }

    public function setRootPath($rootPath)
    {
        $this->rootPath = $this->pathsService->setRootPath($rootPath);
    }

    public function getRootPath()
    {
        return $this->pathsService->getRootPath();
    }

    public function setPath($key, $path)
    {
        $this->pathsService->setPath($key, $path);
    }

    public function getPaths()
    {
        return $this->pathsService->getPaths();
    }

    public function getPath($key)
    {
        return $this->pathsService->getPath($key);
    }

    public function getEnv()
    {
        return $this->envService->get();
    }

    public function setConfig($path)
    {
        $this->configs = $this->configsService->loadFile($path);
    }

    public function getConfigs()
    {
        return $this->configs;
    }

    public function setMiddleware($middlewareFile)
    {
        $app = $this;
        require $middlewareFile;
    }

    public function setExternalServices($servicesFile)
    {
        $app = $this;
        require $servicesFile;
    }

    public function setService($serviceName, $serviceNamespace)
    {
        $this->dependenciesService->addService($serviceName, $serviceNamespace);
    }

    public function getServices()
    {
        return $this->dependenciesService->getServices();
    }

    public function getDependenciesService()
    {
        return $this->dependenciesService;
    }

    public function setLocale($domain, $pathLocales, $locale, $charset)
    {
        // Set language
        putenv('LC_ALL=' . $locale . '.' . $charset);
        setlocale(LC_ALL, $locale . '.' . $charset);

        // Specify the location of the translation tables
        bindtextdomain($domain, $pathLocales);
        bind_textdomain_codeset($domain, $charset);

        // Choose domain
        textdomain($domain);
    }

    public function setRoutes($routesPath) 
    {
        $routes = require $routesPath;

        foreach ($routes as $route) {
            if (!empty($route['name']) && !empty($route['service'])) {
                $this->setServiceForRoute($route['name'], $this->dependenciesService->getServiceNameFromNamespace($route['service']));
            }
            if (!empty($route['name']) && !empty($route['model'])) {
                $this->setModelForRoute($route['name'], $route['model']);
            }
        }

        $this->routingService->addRoutes($this, $routes);
    }

    public function setBundle($name, $path, $loadRoutes = true) 
    {
        $bundle = $this->bundlesService->setBundle($name, $path);
        
        if ($loadRoutes) {
            $this->setRoutes($bundle->getRoutes($path), true);
        }

        foreach ($bundle->getServices() as $serviceName => $namespace) {
            $this->setService($serviceName, $namespace);
        }
    }

    public function setServiceForRoute($controller, $service)
    {
        $this->serviceForRoute[$controller] = $service;
    }

    public function setModelForRoute($controller, $model)
    {
        $this->modelForRoute[$controller] = $model;
    }

    public function getServiceForRoute()
    {
        return $this->serviceForRoute[$this->getCurrentRoute()];
    }

    public function getModelForRoute()
    {
        return $this->modelForRoute[$this->getCurrentRoute()];
    }

    public function getQueryParam($name, $default = null)
    {
        return $this['request']->get($name, $default);
    }

    public function getAllQueryParams()
    {
        return $this['request']->query->all();
    }

    public function getPayload()
    {
        return $this['request']->request->all();
    }

    public function getCurrentRoute()
    {
        return $this['request']->get('_route');
    }
}