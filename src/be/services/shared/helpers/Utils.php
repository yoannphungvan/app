<?php

namespace PROJECT\Services\Shared\Helpers;

class Utils
{
    private $dependencies = [
        'Configs'         => 'PROJECT\Services\Shared\Application\Configs',
    ];

    public function __construct($dependencies)
    {
        $dependencies->loadDependencies($this, $this->dependencies);
        $this->configsService = $dependencies->getDependency($this, 'Configs');
        $configs = $this->configsService->getConfigs();
    }

    public function arrayToObject($array)
    {
        return json_decode(json_encode($array), false);
    }

    public function objectToArray($obj)
    {
        return json_decode(json_encode($obj), true);
    }

    public function diffObject($objReference, $objCompared, $diff = [])
    {
        $objReference = self::objectToArray($objReference);
        $objCompared = self::objectToArray($objCompared);

        foreach ($objReference as $key => $value) {
            $resultDiff = $this->compareProperty($objReference, $objCompared, $key, $diff);
            if ($resultDiff !== true) {
                $diff[$key] = $resultDiff;
            }
        }

        return $diff;
    }

    public function compareProperty($objReference, $objCompared, $key, $diff)
    {
        if (is_array($objReference[$key])) {
            return $this->diffObject($objReference[$key], $objCompared[$key], $diff[$key]);
        } else if (!isset($objCompared[$key]) || $objReference[$key] != $objCompared[$key]) {
            return $objReference[$key] . ' => ' . (isset($objCompared[$key]) ? $objCompared[$key] : 'undefined');
        } else {
            return true;
        }
    }

    public function getDuplicateInArray($array)
    {
        $arrayUnique = array_unique($array);
        return array_diff_assoc($array, $arrayUnique);
    }
}
