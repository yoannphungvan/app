<?php

namespace PROJECT\Services\Shared\Application;

class Paths
{
    private $paths;
    private $rootPath;

    /**
     * Constructor
     *
     * @param string $env
     */
    public function __construct($paths = [])
    {
        $this->configs = [];
    }

    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    public function getRootPath()
    {
        return $this->rootPath;
    }

    public function setPath($key, $path)
    {
        $this->paths[$key] = $this->getRootPath() . '/' . $path;
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function getPath($key)
    {
        return $this->paths[$key];
    }
}

