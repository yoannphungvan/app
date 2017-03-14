<?php

namespace PROJECT\Services\Shared\Repositories;

interface RepositoryInterface
{
    public function get($model, $columns, $id);

    public function getList($model, $columns, $filters, $order, $groupBy, $page = 0, $perPage = 20);

    public function create($model, $values);

    public function update($model, $id, $data);

    public function delete($model, $id);
}
