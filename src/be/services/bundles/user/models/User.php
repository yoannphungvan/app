<?php

  namespace PROJECT\Bundles\Models;

  use PROJECT\Services\Shared\Application\Models;
  use PROJECT\Services\Shared\Application\Mapper as Mapper;

  class User extends Models
  {
    const TABLE_NAME  = 'user';
    const TABLE_ALIAS = 'u';
    const PRIMARY_KEY = 'id';

    public $firstName;
    
    public $lastName;

    public $email;

    public $password;

    public $_mapping = [
        Mapper::OBJECT_TO_DB_ID => [
            'id' => ['key' => 'id', 'type' => Mapper::TYPE_INT],
            'firstName' => ['key' => 'firstname', 'type' => Mapper::TYPE_STRING],
            'lastName' => ['key' => 'lastname', 'type' => Mapper::TYPE_STRING],
            'email' => ['key' => 'email', 'type' => Mapper::TYPE_STRING],
            'password' => ['key' => 'password', 'type' => Mapper::TYPE_STRING],
        ],
        Mapper::DB_TO_OBJECT_ID => [
            'id' => ['key' => 'id', 'type' => Mapper::TYPE_INT],
            'firstname' => ['key' => 'firstName', 'type' => Mapper::TYPE_STRING],
            'lastname' => ['key' => 'lastName', 'type' => Mapper::TYPE_STRING],
            'email' => ['key' => 'email', 'type' => Mapper::TYPE_STRING],
            'password' => ['key' => 'password', 'type' => Mapper::TYPE_STRING],
        ],
        'apiResponse' => [
            'id' => ['key' => 'id', 'type' => Mapper::TYPE_INT],
            'firstName' => ['key' => 'f', 'type' => Mapper::TYPE_STRING],
            'lastName' => ['key' => 'l', 'type' => Mapper::TYPE_STRING],
            'email' => ['key' => 'courriel', 'type' => Mapper::TYPE_STRING],
            'password' => ['key' => 'password', 'type' => Mapper::TYPE_STRING],
        ]
    ];
  }
