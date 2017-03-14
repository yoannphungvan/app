<?php

namespace PROJECT\Services\Shared\Application;

class Environment
{
    protected $env;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->env = 'dev';
        if (getenv('ENV')) {
          $this->env = getenv('ENV');
        }
    }

    public function get()
    {
        return $this->env;
    }
}