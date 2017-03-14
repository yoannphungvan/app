<?php

namespace PROJECT\Services\Shared\Repositories;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

use PROJECT\Services\Shared\Application\Mapper as Mapper;

/**
 * A MySQL repository
 * */
class MySQLRepository implements RepositoryInterface
{
    private $dependencies = [
        'Configs'     => 'PROJECT\Services\Shared\Application\Configs',
        'MySQL'       => 'PROJECT\Services\Shared\Repositories\MySQL',
        'Mapper'      => 'PROJECT\Services\Shared\Application\Mapper',
        'Utils'       => 'PROJECT\Services\Shared\Helpers\Utils',
    ];

    public function __construct($dependencies)
    {
        $dependencies->loadDependencies($this, $this->dependencies);
        $this->configs = $dependencies->getDependency($this, 'Configs')->getConfigs();
        $this->mysql   = $dependencies->getDependency($this, 'MySQL');
        $this->mapper  = $dependencies->getDependency($this, 'Mapper');
        $this->utils   = $dependencies->getDependency($this, 'Utils');
        $this->models  = [];
    }

    public function setModel($model)
    {
        $this->models[$model->getModelName()] = $model;
        $this->mapper->addMapping($model->getModelName(), $model->getMapping());
    }

    public function getModel($modelName)
    {
        $this->models[$modelName];
    }

    public function getQuery($model, $columns, $id)
    {
        $qb = $this->mysql->createQuery();
        $qb->select($this->getColumns($model, $columns));
        $qb->from($model->getTableName(), $model->getTableAlias());
        $qb->where($this->addWhereById($qb, $model, $id));

        return $qb;
    }

    public function get($model, $columns, $id)
    {
        $qb = $this->getQuery($model, $columns, $id);
        
        $result = $qb->execute($qb);
        if($result instanceof \Doctrine\DBAL\Driver\Statement) {
            $result = $result->fetch();
        }
      
        return $this->mapDbToObject($model, $result);
    }

    protected function getColumns($model, $columns)
    {        
        if ($columns === 'all') {
            return $model->getAllColumns();
        }

        $columns = $this->utils->arrayToObject(array_flip($columns));
        $columns = $this->mapper->map($model->getModelName(), $columns, 'db');
        return array_keys((array)$columns);
    }


    public function getListQuery($model, $columns, $filters, $order, $groupBy, $page = 0, $perPage = 20)
    {
        $qb = $this->mysql->createQuery();
        $qb->select($this->getColumns($model, $columns));
        $qb->from($model->getTableName(), $model->getTableAlias());

        // static::setQueryFilters($qbBuilder, $table, $filters, "select", $whereStatement);
        // static::setGroupBy($qbBuilder, $groupBy);
        // static::setQueryOrder($qbBuilder, $sort);

        if (!empty($filters)) {
            $where = $qb->expr()->andx();
            foreach ($filters as $key => $value) {
                $where->add($qb->expr()->eq($key, $qb->expr()->literal($value)));
            }
            $qb->andWhere($where);
        }

        if (isset($perPage) && $perPage > -1) {
            $qb->setFirstResult($page * $perPage);
            $qb->setMaxResults($perPage);
        }

        return $qb;
    }

    public function getList($model, $columns, $filters, $order, $groupBy, $page = 0, $perPage = 20)
    {
        $qb = $this->getListQuery($model, $columns, $filters, $order, $groupBy, $page, $perPage);

        $query = $qb->getSQL();
        $results = $qb->execute($query);
        
        if ($results instanceof \Doctrine\DBAL\Driver\Statement) {
            $results = $results->fetchAll();
        }

        foreach ($results as &$result) {
            $result = $this->mapDbToObject($model, $result);
        }

        return $results;
    }

    public function createQuery($model, $values)
    {
        $qb = $this->mysql->createQuery();
        $qb->insert($model->getTableName());

        $values = $this->mapObjectToDB($model, $values);

        foreach ($values as $key => $value) {
            if (isset($value)) {
              $qb->setValue('`' . $key . '`', $qb->expr()->literal($value));
            }
        }

        return $qb;
    }

    public function create($model, $values)
    {
        $qb = $this->createQuery($model, $values);

        try {
            $affectedRows = $qb->execute($qb->getSql());
        } catch (\Exception $e) {
            error_log($qb->getSql());
        }

        if ($affectedRows == 1) {
            $values[$model->getPrimaryKey()] = $this->mysql->getConnection()->lastInsertId();
            return $values;
        } else {
            throw new \Exception('Could not create resource', 400);
        }

        return null;
    }

    public function updateQuery($model, $id, $values)
    {
        $qb = $this->mysql->createQuery();
        $qb->update($model->getTableName());
        $qb->where($this->addWhereById($qb, $model, $id));

        $values = $this->mapObjectToDB($model, $values);

        foreach ($values as $key => $value) {
            if (isset($value)) {
              $qb->set('`' . $key . '`', $qb->expr()->literal($value));
            }
        }

        return $qb;
    }

    public function update($model, $id, $values)
    {
        $qb = $this->updateQuery($model, $id, $values);

        try {
            $affectedRows = $qb->execute($qb->getSql());
        } catch (\Exception $e) {
          
        }

        return $values;
    }

    public function deleteQuery($model, $id)
    {
        $qb = $this->mysql->createQuery();
        $qb->delete($model->getTableName());

        $where = $qb->expr()->andx();
        // @WESHOULD: manage several primary keys
        $where->add($qb->expr()->eq('`' . $model->getPrimaryKey() . '`', $qb->expr()->literal($id)));
        $qb->where($where);

        return $qb;
    }

    public function delete($model, $id)
    {
        $qb = $this->deleteQuery($model, $id);

        try {
            $affectedRows = $qb->execute($qb->getSql());
        } catch (\Exception $e) {
          
        }

        return $affectedRows;
    }

    protected function addWhereById($qb, $model, $id)
    {
        $where = $qb->expr()->andx();
        // @WESHOULD: manage several primary keys
        $where->add($qb->expr()->eq('`' . $model->getPrimaryKey() . '`', $qb->expr()->literal($id)));
        
        return $where;
    }

    protected function mapObjectToDB($model, $values)
    {
        $values = $this->utils->arrayToObject($values);
        $values = $this->mapper->map($model->getModelName(), $values, Mapper::OBJECT_TO_DB_ID);
        return (array) $values;
    }

    protected function mapDbToObject($model, $values)
    {
        $values = $this->utils->arrayToObject($values);
        $values = $this->mapper->map($model->getModelName(), $values, Mapper::DB_TO_OBJECT_ID);
        return (array) $values;
    }
}
