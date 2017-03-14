<?php

/**
 * Mapper
 */

namespace PROJECT\Services\Shared\Application;

class Mapper
{
    const TYPE_INT = 'int';
    const TYPE_STRING = 'string';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_FLOAT = 'float';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';
    const TYPE_DEFAULT = 'default';
    const OBJECT_TO_DB_ID = 'object-to-db';
    const DB_TO_OBJECT_ID = 'db-to-object';
    const API_RESPONSE_ID = 'api-reponse';

    private $models = [];
    private $mappingConfig = [];

     private $dependencies = [
        'Configs'     => 'PROJECT\Services\Shared\Application\Configs',
    ];

    /**
     * contructor
     */
    public function __construct($dependencies)
    {
        $this->dependenciesService = $dependencies;
        $this->dependenciesService->loadDependencies($this, $this->dependencies);
        $configsService = $dependencies->getDependency($this, 'Configs');
        $this->configs  = $this->dependenciesService->getDependency($this, 'Configs');
    }

    /**
     * Add a mapping to mapping  object list
     * @param  string $modelName Model name
     * @param  array  $config     Mapping configuration
     * @param  array  $config     Mapping configuration

     */
    public function addMapping($modelName, $mappingConfig, $refreshMapping = false)
    {
        // Check if mapping exists
        if (!isset($mappingConfig[$modelName])) {
            throw new \Exception("Cannot find mapping for " . $modelName, 400);
        }

        $mapping = $this->getMapping($modelName);

        if (!$refreshMapping && !empty($mapping)) {
            return $mapping;
        }

        $this->setMapping($modelName, $mappingConfig[$modelName]);

        return $mappingConfig;
    }

    /**
     * Map an object for a specific source
     * @param  string  $modelName  Model name
     * @param  Object  $model      Model/Object instance to transform
     * @param  string  $source     Source/destination that request the model
     * @param  boolean $strict     Should we be strict
     * @return Object              Transformed/mapped model
     */
    public function map($modelName, $model, $source, $strict = false)
    {
        if (!isset($this->mappingConfig[$modelName][$source])) {
            if ($strict) {
                throw new \Exception("Mapping not found", 404);
            }

            // If not strict, we return the object as is
            return $model;
        }

        $rootProperties = $this->mappingConfig[$modelName][$source];
        $newModel = $this->createNewObject($modelName, $rootProperties, $model);
        return $newModel;
    }

    /**
     * Set mapping and keep it in a global array
     * @param  string $modelName Object name
     * @param  array $mapping     Mapping config
     */
    private function setMapping($modelName, $mapping)
    {
        // Keep mapping for this model
        $this->mappingConfig[$modelName] = $mapping;
    }

    public function isMappingExists($modelName)
    {
        return !empty($this->mappingConfig[$modelName]);
    }

    private function getMapping($modelName)
    {
        if (empty($this->mappingConfig[$modelName])) {
            return null;
        }

        return $this->mappingConfig[$modelName];
    }

    private function createNewObject($modelName, $properties, $model)
    {
        $newModel = new \StdClass();
        
        foreach ($properties as $property => $rules) {
            if (isset($model->{$property})) {
                $newModel->{$rules['key']} = $this->addProperty($modelName, $property, $model, $rules);
            } elseif (isset($rules['validation']['required'])) {
                // if property is required but model doesn't have it, we create a null property
                $newModel->{$rules['key']} = null;
            }
        }
        return $newModel;
    }

    private function addProperty($modelName, $propertyName, $model, $rules)
    {
        if (isset($model->{$propertyName})) {
            if ($rules['type'] === static::TYPE_OBJECT) {
                if (isset($rules['children'])) {
                    return $this->createNewObject($modelName, $rules['children'], $model->{$propertyName});
                } else {
                    return $model->{$propertyName};
                }
            } else if ($rules['type'] === static::TYPE_ARRAY) {
                if (isset($rules['children'])) {
                    $children = [];
                    foreach ($model->{$propertyName} as $child) {
                       $children[] = $this->createNewObject($modelName, $rules['children'], $child);
                    }
                    
                    return $children;
                }

            } else {
                $value = $model->{$propertyName};
                //$this->validate();
                return $this->cast($value, $rules['type']);
            }
        }
    }

    private function cast($value, $type)
    {
        switch ($type) {
            case static::TYPE_INT:
                return (int) $value;
            case static::TYPE_STRING:
                return (string) $value;
            case static::TYPE_BOOLEAN:
                return (bool) $value;
            case static::TYPE_FLOAT:
                return (float) $value;
        }

        // Return value not casted
        return $value;
    }
}
