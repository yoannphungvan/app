<?php

namespace PROJECT\Services\Shared\Application;

class Dependencies
{
    private $availableServices;
    
    public function __construct(Application $app)
    {
        $this->availableServices = [];
        $this->app               = $app; 
    }

    public function loadDependencies($currentClass, $dependencies)
    {
        $namespace = $this->getCurrentClassName($currentClass);
        foreach ($dependencies as $dependencyName => $dependencyNamespace) {
            $this->addDependency($namespace, $dependencyName, $dependencyNamespace);
        }  
    }

    public function addService($serviceName, $serviceNamespace)
    {
        if ($this->serviceExists($serviceName)) {
            throw new \Exception('Service '. $serviceName . ' already exist', 400);
        }

        $id = $this->getServiceId($serviceName);

        $app = $this->app;

        $this->app[$id] = $this->app->share(function($app) use ($serviceNamespace) {
            return new $serviceNamespace($app->getDependenciesService());
        });

        $this->availableServices[$serviceName] = $this->app[$id];   
    }

    public function getServices()
    {
        return array_keys($this->availableServices);
    }

    protected function serviceExists($serviceName)
    {
        return isset($this->availableServices[$serviceName]);
    }

    protected function getService($serviceName)
    {
        if (empty($this->availableServices[$serviceName])) {
            throw new \Exception('Service '. $serviceName . ' not found', 404);
        }

        return $this->availableServices[$serviceName];
    }

    public function addDependency($service, $dependencyName, $dependencyNamespace)
    {
        if (!$this->serviceExists($dependencyName)) {
            $this->addService($dependencyName, $dependencyNamespace);
        }

        $this->dependencies[$service][$dependencyName] = $this->getService($dependencyName);
    }

    public function getDependencyNames($serviceName)
    {
        return array_keys($this->dependencies[$serviceName]);
    }

    public function getDependency($currentClass, $dependencyName)
    {
        $className = $this->getCurrentClassName($currentClass);
        if (empty($this->dependencies[$className][$dependencyName])) {
            throw new \Exception('Missing dependency ' . $dependencyName . ' for ' . $className, 404);
        }

        return $this->dependencies[$className][$dependencyName];
    }

    private function getServiceId($serviceName) 
    {
        return $serviceName;
    }

    private function getCurrentClassName($currentClass) {
        $class = new \ReflectionClass($currentClass);
        return $class->getName();
    }

    public function getServiceNameFromNamespace($namespace)
    {
        return strtolower(preg_replace("/[^A-Za-z0-9\.\-\_]/", '', $namespace));
    } 
}

