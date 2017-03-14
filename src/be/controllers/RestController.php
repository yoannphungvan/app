<?php

namespace PROJECT\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use PROJECT\Services\Shared\Application\Mapper;

/**
 * Restful controller implementing standard get,post,update,delete HTTP functionality
 */
class RestController implements RestControllerInterface
{
    /**
     * Get a list of all models, json encoded. 
     *
     * @param Application $app An instance of the app
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getList(Application $app)
    {
        $service = $app->getServiceForRoute();
        $model   = $app->getModelForRoute();
        list(
            $columns, 
            $filters, 
            $order, 
            $groupBy, 
            $page, 
            $perPage
        )        = $this->getFiltersList($app->getAllQueryParams());
        $results = $app[$service]->getList($columns, $filters, $order, $groupBy, $page, $perPage);
        
        return $this->responseFormat($app, $results, $model);
    }

    /**
     * Returns a single model by ID
     *
     * @param Application $app An of the current app.
     * @param string $id The id of the model to fetch.
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function get(Application $app, $id)
    {
        $service = $app->getServiceForRoute();
        $model   = $app->getModelForRoute();
        $columns = $this->getFilters($app->getAllQueryParams());
        $result  = $app[$service]->get($columns, $id);

        return $this->responseFormat($app, $result, $model);
    }

    /**
     * Route handler for creating new models.
     *
     * @param Application $app An instance of the app
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function post(Application $app)
    {
        $params  = $app->getPayload();
        $service = $app->getServiceForRoute();
        $model   = $app->getModelForRoute();
        $result  = $app[$service]->create($params);

        return $this->responseFormat($app, $result, $model);
    }

    /**
     * Route handler for updating a model record in the DB.
     *
     * @param Application $app An instance of app.
     * @param Request $request An instance of incoming request
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function put(Application $app, $id)
    {
        $params  = $app->getPayload();
        $service = $app->getServiceForRoute();
        $model   = $app->getModelForRoute();
        $result  = $app[$service]->update($id, $params);

        return $this->responseFormat($app, $result, $model);
    }

    /**
     * Route handler for pathing a model record in the DB.
     *
     * @param Application $app An instance of app.
     * @param Request $request An instance of incoming request
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function patch(Application $app, $id)
    {
        $params  = $app->getPayload();
        $service = $app->getServiceForRoute();
        $model   = $app->getModelForRoute();
        $result  = $app[$service]->patch($id, $params);
        return $this->responseFormat($app, $result, $model);
    }

    /**
     * Delete an object based on the ID
     *
     * @param Application $app
     * @param int $id
     *
     * @return string|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete(Application $app, $id)
    {
        $service = $app->getServiceForRoute();
        $model = $app[$service]->delete($id);
        return $this->responseFormat($app, $model);
    }

    protected function getFiltersList($params)
    {
        $columns = 'all';
        $filters = null; 
        $order = null;
        $groupBy = null; 
        $page = 0;
        $perPage = 20;

        foreach ($params as $key => $value) {
            switch ($key) {
                case 'page':
                    $page = $value;
                    break;
                case 'per_page':
                    $perPage = $value;
                    break;
                case 'order':
                    $order = $value;
                    break;
                case 'fields':
                    $columns = array_values(explode(',', $value));
                    break;
                case 'group_by':
                    $groupBy = $value;
                    break;
                default:
                    $filters[$key] = $value;
                    break;
            }
        }

        return [$columns, $filters, $order, $groupBy, $page, $perPage];
    }

    protected function getFilters($params)
    {
        $columns = 'all';

        foreach ($params as $key => $value) {
            switch ($key) {
                case 'fields':
                    $columns = array_values(explode(',', $value));
                    break;
            }
        }

        return $columns;
    }

    protected function getRoute($app)
    {
        return $app['request']->get('_route');
    }

    public function responseFormat($app, $data, $model = null)
    {
        if ($data instanceOf \Exception) {
            $response = [
                'status' => 'error' ,
                'code'   => $data->getCode(),
                'error' => $data->getMessage()
            ];
        } else {
            $data = $this->convertResponse($app, $data, $model);
            $response = [
                'status' => 'ok' ,
                'code'   => '200',
                'data' => $data
            ];
        }
        return $app->json($response);
    }

    public function convertResponse($app, $data, $model = null)
    {
        if (null !== $model || $app['mapper']->isMappingExists($model)) {
            return $data;
        }

        return $app['mapper']->map($model, $data, Mapper::API_RESPONSE_ID);
    }
}
