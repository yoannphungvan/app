<?php

namespace PROJECT\Services\Shared\Application;

class Bundle
{
    private $name;
    private $path;
    private $services;

    const ROUTES_PATH    = 'Routes.php';
    const CONFIGS_PATH   = 'Configs.php';

    private $dependencies = [];

    public function __construct($dependencies, $name, $path)
    {
        $this->dependenciesService = $dependencies;
        $this->dependenciesService->loadDependencies($this, $this->dependencies);

        $this->name = $name;
        $this->path = $path;

        $this->services = $this->setServices();
    }

    public function getRoutes()
    {
        return $this->path . '/' . static::ROUTES_PATH;
    }

    public function getConfigs()
    {
        return $this->path . '/' . static::CONFIGS_PATH;
    }

    protected function setServices() 
    {
        $services = [];
        $routes = require $this->getRoutes();

        foreach ($routes as $route) {
            $serviceNamespace = $route['service'];
            $services[$this->dependenciesService->getServiceNameFromNamespace($serviceNamespace)] = $serviceNamespace;
        }

        return $services;
    }

    public function getServices()
    {
        return $this->services;
    }
}
