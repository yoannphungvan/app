<?php

  namespace PROJECT\Services\Shared\Application;

  use PROJECT\Services\Shared\Application\Mapper as Mapper;

  abstract class Models implements \ArrayAccess
  {
    const TABLE_NAME  = '';
    const TABLE_ALIAS = 't';
    const PRIMARY_KEY = 'id';

    protected $_mapping;

    protected $_properties = [];

    public function offsetExists($key)
    {
        return isset($this->_properties[$key]);
    }

    public function offsetGet($key)
    {
        return $this->_properties[$key];
    }

    public function offsetSet($key, $value)
    {
        $this->_properties[$key] = $value;
        $this->{$key} = $value;
    }

    public function offsetUnset($key)
    {
        if (isset($this->_properties[$key])) { 
            unset($this->_properties[$key]);
        }
        if (isset($this->{$key})) { 
            unset($this->{$key});
        }
    }

    public function getTableName()
    {
        return static::TABLE_NAME;
    }

    public function getAllColumns()
    {
        return array_values(array_map(function($object) {
            return $object['key'];
        }, $this->_mapping[Mapper::OBJECT_TO_DB_ID]));
    }

    public function getTableAlias()
    {
        return static::TABLE_ALIAS;
    }

    public function getPrimaryKey()
    {
        return static::PRIMARY_KEY;
    }

    public function getMapping()
    {
        return [$this->getModelName() => $this->_mapping];
    }

    public function getModelName()
    {
        return get_class($this);
    }
  }
