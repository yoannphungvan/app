<?php

namespace PROJECT\Services\Shared\Repositories;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * A MySQL repository
 * */
class MySQL
{
    /**
     * @var Connection $connection A DB Connection
     * */
    protected $connection;

    /**
     * @var Configuration $configuration A DB Configuration
     * */
    protected $configuration;

    private $dependencies = [
        'Configs'     => 'PROJECT\Services\Shared\Application\Configs',
    ];

    /**
     * Constructor.
     *
     * @param string $host A host
     * @param string $username A username
     * @param string $password A password
     * @param string $db A database
     * @param string $port A port
     **/
    public function __construct($dependencies)
    {
        $dependencies->loadDependencies($this, $this->dependencies);
        $configsService = $dependencies->getDependency($this, 'Configs');
        $configs = $configsService->getConfigs();

        $this->mysqlConfiguration = new Configuration();

        // Default master mysql connection
        $connectionParams = $configs['repositories']['mysql']['master'];

        // Slave mysql connection if slaves are defined
        if (isset($configs['repositories']['mysql']['slaves'])) {
            $connectionParams = [
              'wrapperClass' => 'Doctrine\DBAL\Connections\MasterSlaveConnection',
              'driver' => 'pdo_mysql',
              'master' => $connectionParams,
              'slaves' => $configs['repositories']['mysql']['slaves']
            ];
        }
        $this->connection = DriverManager::getConnection($connectionParams, $this->mysqlConfiguration);
    }

    /**
     * Destructor.
     * */
    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * Get a query builder instance
     * */
    public function createQuery()
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * Get the connection object for running prepared statements.
     *
     * @return Doctrine\DBAL\Connection The connection object.
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Execute a query
     *
     * @param QueryBuilder $queryBuilder A query builder
     * @return Statement a statement
     **/
    public function execute(QueryBuilder $queryBuilder)
    {
        $query = $queryBuilder->getSql();

        $statement = $queryBuilder->execute($query);

        return $statement;
    }

    /**
      * Accessor function to run raw prepared queries if required.
      *
      * @param   string  $sql The raw sql string
      * @param   array  $params  Array of paramaters to replace in the query.
      *
      * @return  array Returns the rows returned from the query.
      */
    public function executeQuery($sql, $params = [], $types = [])
    {
        return $this->connection->executeQuery($sql, $params)->fetchAll();
    }

    /**
      * Accessor fucntion to run executeUpdate directly on the connection
      * with raw sql.
      *
      * @param string $sql The sql string to run with possible place holders.
      * @param array $params Array of parameters for positional or named args in the query.
      * @param array $types DBAL types for the provied arguments.
      *
      * @return integer The number of affected rows.
      */
    public function executeUpdate($sql, $params = [], $types = [])
    {
        return $this->connection
        ->executeUpdate($sql, $params, $types);
    }

    /**
     * Ping
     *
     * @return boolean
     **/
    public function ping()
    {
        return $this->connection->ping();
    }
}
