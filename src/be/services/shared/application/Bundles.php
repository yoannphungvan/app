<?php

namespace PROJECT\Services\Shared\Application;

class Bundles
{
    private $bundles;

    private $dependencies = [];

    public function __construct($dependencies)
    {
        $this->dependenciesService = $dependencies;
        $this->dependenciesService->loadDependencies($this, $this->dependencies);
    }

    public function setBundle($name, $path)
    {
        $bundle = new Bundle($this->dependenciesService, $name, $path);
        return $bundle;
    }
}
