<?php

namespace PROJECT\Services\Shared\Application;

class Configs
{
    private $env;
    private $configs;

    /**
     * Constructor
     *
     * @param string $env
     */
    public function __construct($env)
    {
        $this->env = $env;
        $this->configs = [];
    }

    public function loadFile($fileName)
    {
        $configsToLoad = require_once $fileName;
        $this->configs = array_merge($this->configs, $configsToLoad);
        return $this->configs;
    }

    public function getConfigs()
    {
        return $this->configs;
    }
}

