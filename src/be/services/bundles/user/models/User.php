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
            'description' => ['key' => 'description', 'type' => Mapper::TYPE_STRING],
            'city' => ['key' => 'city', 'type' => Mapper::TYPE_STRING],
            'picture' => ['key' => 'picture', 'type' => Mapper::TYPE_STRING],
            'active' => ['key' => 'active', 'type' => Mapper::TYPE_INT],
            'lastLoginDate' => ['key' => 'last_login_date', 'type' => Mapper::TYPE_STRING],
            'createdDate' => ['key' => 'created_date', 'type' => Mapper::TYPE_STRING],
            'modificationDate' => ['key' => 'modification_date', 'type' => Mapper::TYPE_STRING],
        ],
        Mapper::DB_TO_OBJECT_ID => [
            'id' => ['key' => 'id', 'type' => Mapper::TYPE_INT],
            'firstname' => ['key' => 'firstName', 'type' => Mapper::TYPE_STRING],
            'lastname' => ['key' => 'lastName', 'type' => Mapper::TYPE_STRING],
            'email' => ['key' => 'email', 'type' => Mapper::TYPE_STRING],
            'password' => ['key' => 'password', 'type' => Mapper::TYPE_STRING],
            'description' => ['key' => 'description', 'type' => Mapper::TYPE_STRING],
            'city' => ['key' => 'city', 'type' => Mapper::TYPE_STRING],
            'picture' => ['key' => 'picture', 'type' => Mapper::TYPE_STRING],
            'active' => ['key' => 'active', 'type' => Mapper::TYPE_INT],
            'last_login_date' => ['key' => 'lastLoginDate', 'type' => Mapper::TYPE_STRING],
            'created_date' => ['key' => 'createdDate', 'type' => Mapper::TYPE_STRING],
            'modification_date' => ['key' => 'modificationDate', 'type' => Mapper::TYPE_STRING],
        ],
        Mapper::API_RESPONSE_ID => [
            'id' => ['key' => 'id', 'type' => Mapper::TYPE_INT],
            'firstName' => ['key' => 'firstName', 'type' => Mapper::TYPE_STRING],
            'lastName' => ['key' => 'firstName', 'type' => Mapper::TYPE_STRING],
            'email' => ['key' => 'email', 'type' => Mapper::TYPE_STRING],
            'description' => ['key' => 'description', 'type' => Mapper::TYPE_STRING],
            'city' => ['key' => 'city', 'type' => Mapper::TYPE_STRING],
            'picture' => ['key' => 'picture', 'type' => Mapper::TYPE_STRING],
            'active' => ['key' => 'active', 'type' => Mapper::TYPE_INT],
            'lastLoginDate' => ['key' => 'lastLoginDate', 'type' => Mapper::TYPE_STRING],
            'createdDate' => ['key' => 'createdDate', 'type' => Mapper::TYPE_STRING],
            'modificationDate' => ['key' => 'modificationDate', 'type' => Mapper::TYPE_STRING],
        ]
    ];
  }

