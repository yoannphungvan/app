<?php

return [
    [
        'pattern'    => '/api/user/{id}',
        'controller' => 'PROJECT\Controllers\RestController::get',
        'name'       => 'user_get',
        'service'    => 'PROJECT\Bundles\Services\User',
        'model'      => 'PROJECT\Bundles\Models\User',
        'method'     => ['get']    
    ],
    [
        'pattern'    => '/api/user',
        'controller' => 'PROJECT\Controllers\RestController::getList',
        'name'       => 'user_get_list',
        'service'    => 'PROJECT\Bundles\Services\User',
        'model'      => 'PROJECT\Bundles\Models\User',
        'method'     => ['get']    
    ],
    [
        'pattern'    => '/api/user',
        'controller' => 'PROJECT\Controllers\RestController::post',
        'name'       => 'user_post',
        'service'    => 'PROJECT\Bundles\Services\User',
        'model'      => 'PROJECT\Bundles\Models\User',
        'method'     => ['post']    
    ],
    [
        'pattern'    => '/api/user/{id}',
        'controller' => 'PROJECT\Controllers\RestController::put',
        'name'       => 'user_put',
        'service'    => 'PROJECT\Bundles\Services\User',
        'model'      => 'PROJECT\Bundles\Models\User',
        'method'     => ['put']    
    ],
    [
        'pattern'    => '/api/user/{id}',
        'controller' => 'PROJECT\Controllers\RestController::delete',
        'name'       => 'user_delete',
        'service'    => 'PROJECT\Bundles\Services\User',
        'model'      => 'PROJECT\Bundles\Models\User',
        'method'     => ['delete']    
    ]
];