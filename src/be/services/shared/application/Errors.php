<?php

namespace PROJECT\Services\Shared\Application;

class Errors
{
    private $errors = [];

    /**
     * Constructor
     *
     * @param string $env
     */
    public function __construct($dependencies)
    {
        $this->dependenciesService = $dependencies;
        $this->dependenciesService->loadDependencies($this, $this->dependencies);
    }

    public function setBundle($name, $path, $services)
    {
        $bundle = new Bundle($name, $path, $services);
        return $bundle;
    }
}
