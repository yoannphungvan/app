<?php
/* ---------------------------------------------------------
 * src/be/services/MySQLRepository.php
 *
 * A MySQL repository.
 *
 * Copyright 2015 - PROJECT
 * --------------------------------------------------------- */

namespace PROJECT\Services\Shared\Repositories;

/**
 * A MySQL repository
 * */
class Repository
{
    private $dependencies = [
        'Configs'         => 'PROJECT\Services\Shared\Application\Configs',
        'MySQLRepositoty' => 'PROJECT\Services\Shared\Repositories\MySQLRepository',
        'UserModel'       => 'PROJECT\Bundles\Models\User',
    ];

    /**
     * Constructor.
     **/
    public function __construct($dependencies)
    {
        $this->dependenciesService = $dependencies;
        $this->dependenciesService->loadDependencies($this, $this->dependencies);
        
        $this->configs = $dependencies->getDependency($this, 'Configs')->getConfigs();
        $this->repo = $this->dependenciesService->getDependency($this, 'MySQLRepositoty');
    }

   public function get($columns, $id) 
    {
        return $this->repo->get(
            $this->model,
            $columns,
            $id
        );
    }

    public function getList($columns, $filters, $order, $groupBy, $page = 0, $perPage = 20) 
    {
        return $this->repo->getList(
            $this->model,
            $columns, 
            $filters, 
            $order, 
            $groupBy, 
            $page, 
            $perPage
        );
    }
    
    public function create($data) 
    {
        return $this->repo->create(
            $this->model,
            $data
        );
    } 

    public function update($id, $data) 
    {
        return $this->repo->update(
            $this->model,
            $id,
            $data
        );
    }  

    public function delete($id) 
    {
        return $this->repo->delete(
            $this->model,
            $id
        );
    }
}
