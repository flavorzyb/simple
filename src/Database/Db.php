<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/17
 * Time: 上午12:22
 */

namespace Simple\Database;

use PDO;

use Simple\Config\Repository;

class Db
{
    /**
     * @var Repository
     */
    protected $config           = null;

    /**
     * default pdo connection name
     *
     * @var string
     */
    protected $defaultPdoName   = null;

    /**
     * the default PDO connection
     *
     * @var PDO
     */
    protected $defaultPdo   = null;

    /**
     * the PDO connections set
     *
     * @var array
     */
    protected $pdoArray     = [];

    /**
     * the pdo connections options
     *
     * @var array
     */
    protected $connections  = null;

    /**
     * The active PDO connection.
     * @var PDO
     */
    protected $activePdo    = null;

    /**
     * the pdo construct options array
     *
     * @var array
     */
    protected $options      = [];
    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $pdoDefaultOptions = array(
                                        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                                        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
                                        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                                        PDO::ATTR_STRINGIFY_FETCHES => false,
                                        PDO::ATTR_EMULATE_PREPARES  => false,
                                        PDO::ATTR_PERSISTENT        => false,
                                        );

    /**
     * the pdo connection fetch mode
     * @var int
     */
    protected $fetchMode    = PDO::FETCH_ASSOC;

    /**
     * Db construct
     * @param Repository $config
     * @throws DbException
     */
    public function __construct(Repository $config)
    {
        $this->config           = $config;
        $this->defaultPdoName   = $config['default'];
        $this->connections      = $config['connections'];
        $this->fetchMode        = intval($config['fetch']);

        if ((null == $this->defaultPdoName) || ('' == $this->defaultPdoName)) {
            throw new DbException("Db default connection name can not be empty.");
        }

        if ((null == $this->connections) || (!is_array($this->connections))) {
            throw new DbException("Db connections can not be empty.");
        }

        if (!isset($this->connections[$this->defaultPdoName])) {
            throw new DbException("Db connections can not contains default pdo name.");
        }

        $driver                 = $config['driver'];
        if ('mysql' !== $driver) {
            throw new DbException("Db does not support driver({$driver}).");
        }

        if ((null != $config['options']) && (is_array($config['options']))) {
            $this->options  = $config['options'];
        }
    }

    /**
     * Get the DSN string for a host / port configuration.
     * @param array $config
     * @return string
     * @throws DbException
     */
    protected function getHostDsn(array $config)
    {
        if (!isset($config['host'])) {
            throw new DbException("Db connection host can not be empty.");
        }

        if (!isset($config['database'])) {
            throw new DbException("Db connection database can not be empty.");
        }

        $charset    = "";
        if (null != $this->config['charset']) {
            $charset = ";charset={$this->config['charset']}";
        }

        if (isset($config['port'])) {
            return "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}" . $charset;
        }

        return "mysql:host={$config['host']};dbname={$config['database']}" . $charset;
    }

    /**
     * Get the PDO options based on the configuration.
     *
     * @param array $options
     * @return array
     */
    protected function getPdoOptions(array $options)
    {
        $result = $this->pdoDefaultOptions;

        foreach ($options as $k => $v)
        {
            $result[$k] = $v;
        }

        return $result;
    }

    /**
     * create a pdo instance
     *
     * @param array $config
     * @return PDO
     * @throws \PDOException
     */
    protected function createPdo(array $config)
    {
        $dsn        = $this->getHostDsn($config);
        $userName   = (isset($config['username']) ? $config['username'] : null);
        $password   = (isset($config['password']) ? $config['password'] : null);
        $options    = $this->getPdoOptions($config);

        return new PDO($dsn, $userName, $password, $options);
    }

    /**
     * init pdo connection options after instance
     * @param PDO $pdo
     * @return PDO
     */
    protected function initOptions(PDO $pdo)
    {
        // set time zone
        if (isset($this->config['timezone']))
        {
            $pdo->prepare('set time_zone="' . $this->config['timezone'] . '"')->execute();
        }

        //set charset
        if (isset($this->config['charset']))
        {
            $pdo->prepare('set names "' . $this->config['charset'] . '"')->execute();
        }

        // set strict mode
        if (isset($this->config['strict']) && $this->config['strict'])
        {
            $pdo->prepare("set session sql_mode='STRICT_ALL_TABLES'")->execute();
        }

        return $pdo;
    }

    /**
     * get pdo connection by name
     *
     * @param string $name
     * @return PDO
     * @throws DbException
     */
    public function getPdo($name)
    {
        if (!isset($this->connections[$name])) {
            $name   = $this->defaultPdoName;
        }

        if (isset($this->pdoArray[$name])) {
            return $this->pdoArray[$name];
        }

        $pdo                    = $this->createPdo($this->connections[$name]);
        $this->pdoArray[$name]  = $this->initOptions($pdo);
        return $this->pdoArray[$name];
    }

    /**
     * get default pdo connection
     * @return PDO
     * @throws DbException
     */
    public function getDefaultPdo()
    {
        return $this->getPdo($this->defaultPdoName);
    }

    /**
     * get active pdo connection
     *
     * @return PDO
     */
    public function getActivePdo()
    {
        return $this->activePdo;
    }

    /**
     * set active pdo connection
     *
     * @param PDO $pdo
     */
    protected function setActivePdo(PDO $pdo)
    {
        $this->activePdo    = $pdo;
    }

    /**
     * use pdo connection by name
     *
     * @param string $name
     * @throws DbException
     */
    public function usePdo($name)
    {
        if (!isset($this->connections[$name])) {
            $name   = $this->defaultPdoName;
        }

        $pdo = $this->getPdo($name);
        $this->setActivePdo($pdo);
    }

    /**
     * use default pdo connection
     * @throws DbException
     */
    public function useDefaultPdo()
    {
        $this->usePdo($this->defaultPdoName);
    }

    /**
     * disconnect pdo by name
     *
     * @param string $name
     * @throws DbException
     */
    public function disconnect($name)
    {
        if (isset($this->pdoArray[$name])) {
            $pdo    = $this->pdoArray[$name];
            if ($this->activePdo == $pdo) {
                $this->activePdo = null;
            }

            unset($this->pdoArray[$name]);
        }
    }

    /**
     * reconnect pdo by name
     *
     * @param string $name
     * @throws DbException
     */
    public function reconnect($name)
    {
        if (isset($this->connections[$name])) {
            $this->disconnect($name);
            $this->usePdo($name);
        }
    }

    /**
     * Run a select statement and return a single result.
     *
     * @param string $query
     * @param array $bindings
     * @return array
     * @throws DbException
     */
    public function selectOne($query, $bindings = array())
    {
        $query .= " LIMIT 1;";
        $result = $this->select($query, $bindings);
        return (isset($result[0]) ? $result[0] : []);
    }

    /**
     * Run a select statement against the database.
     *
     * @param string $query
     * @param array $bindings
     * @return array
     * @throws DbException
     */
    public function select($query, $bindings = array())
    {
        $statement  = $this->getActivePdo()->prepare($query);

        if ($statement->execute($bindings)) {
            return $statement->fetchAll($this->fetchMode);
        }

        throw new DbException("Db execute fail [{$query}]");
    }

    /**
     * Run an insert statement against the database.
     *
     * @param string $query
     * @param array $bindings
     * @return bool
     * @throws DbException
     */
    public function insert($query, $bindings = array())
    {
        return $this->statement($query, $bindings);
    }

    /**
     * (PHP 5 &gt;= 5.1.0, PECL pdo &gt;= 0.1.0)<br/>
     * Returns the ID of the last inserted row or sequence value
     * @link http://php.net/manual/en/pdo.lastinsertid.php
     * @param string $name [optional] <p>
     * Name of the sequence object from which the ID should be returned.
     * </p>
     * @return string If a sequence name was not specified for the <i>name</i>
     * parameter, <b>PDO::lastInsertId</b> returns a
     * string representing the row ID of the last row that was inserted into
     * the database.
     * </p>
     * <p>
     * If a sequence name was specified for the <i>name</i>
     * parameter, <b>PDO::lastInsertId</b> returns a
     * string representing the last value retrieved from the specified sequence
     * object.
     * </p>
     * <p>
     * If the PDO driver does not support this capability,
     * <b>PDO::lastInsertId</b> triggers an
     * IM001 SQLSTATE.
     */
    public function lastInsertId($name = null)
    {
        return $this->getActivePdo()->lastInsertId($name);
    }

    /**
     * Run an update statement against the database.
     *
     * @param string $query
     * @param array $bindings
     * @return int
     * @throws DbException
     */
    public function update($query, $bindings = array())
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a delete statement against the database.
     *
     * @param string $query
     * @param array $bindings
     * @return int
     * @throws DbException
     */
    public function delete($query, $bindings = array())
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return bool
     * @throws DbException
     */
    protected function statement($query, $bindings = array())
    {
        $statement  = $this->getActivePdo()->prepare($query);

        return $statement->execute($bindings);
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     * @throws DbException
     */
    protected function affectingStatement($query, $bindings = array())
    {
        $statement  = $this->getActivePdo()->prepare($query);

        $statement->execute($bindings);

        return $statement->rowCount();
    }

    /**
     * (PHP 5 &gt;= 5.1.0, PECL pdo &gt;= 0.1.0)<br/>
     * Execute an SQL statement and return the number of affected rows
     * @link http://php.net/manual/en/pdo.exec.php
     * @param string $query <p>
     * The SQL statement to prepare and execute.
     * </p>
     * <p>
     * Data inside the query should be properly escaped.
     * </p>
     * @return int <b>PDO::exec</b> returns the number of rows that were modified
     * or deleted by the SQL statement you issued. If no rows were affected,
     * <b>PDO::exec</b> returns 0.
     * </p>
     * This function may
     * return Boolean <b>FALSE</b>, but may also return a non-Boolean value which
     * evaluates to <b>FALSE</b>. Please read the section on Booleans for more
     * information. Use the ===
     * operator for testing the return value of this
     * function.
     * <p>
     * The following example incorrectly relies on the return value of
     * <b>PDO::exec</b>, wherein a statement that affected 0 rows
     * results in a call to <b>die</b>:
     * <code>
     * $db->exec() or die(print_r($db->errorInfo(), true));
     * </code>
     */
    public function execute($query)
    {
        return $this->getActivePdo()->exec($query);
    }

    /**
     * Start a new database transaction.
     *
     * @return void
     * @throws DbException
     */
    public function beginTransaction()
    {
        $this->getActivePdo()->beginTransaction();
    }

    /**
     * Commit the active database transaction.
     *
     * @return void
     * @throws DbException
     */
    public function commit()
    {
        $this->getActivePdo()->commit();
    }

    /**
     * Rollback the active database transaction.
     *
     * @return void
     */
    public function rollBack()
    {
        $this->getActivePdo()->rollBack();
    }
}
