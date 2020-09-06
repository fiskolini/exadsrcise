<?php

namespace Exadsrcise\Infrastructure;

use Exadsrcise\Domain\Common\Arr;
use Exadsrcise\Infrastructure\Exceptions\DBException;
use Exadsrcise\Infrastructure\Exceptions\DBQueryException;
use Exadsrcise\Infrastructure\Factories\DBFactory;
use mysqli;
use mysqli_stmt;
use function array_push;
use function array_unshift;
use function array_values;
use function call_user_func;
use function implode;
use function is_callable;

class DB
{
    /**
     * @var ?self $instance Singleton instance
     */
    private static ?self $instance = null;

    /**
     * @var bool $query_closed Defines if the connection is closed
     */
    private bool $query_closed = true;

    /**
     * @var ?mysqli $mysqli MySQL instance
     */
    private ?mysqli $mysqli = null;

    /**
     * @var array $connectionsSettings Connection properties
     */
    private array $connectionsSettings = [];

    /**
     * @var string $charset Connection's charset
     */
    private string $charset = 'utf8';

    /**
     * @var mysqli_stmt|false $query Query statement
     */
    private $query;

    /**
     * DB constructor.
     *
     * @param string      $host
     * @param int         $port
     * @param string      $db
     * @param string      $user
     * @param string      $pass
     * @param string|null $socket
     */
    public function __construct(
        string $host,
        int $port,
        string $db,
        string $user,
        string $pass,
        string $socket = null
    )
    {
        $this->setConnection(array(
            'host'     => $host,
            'username' => $user,
            'password' => $pass,
            'db'       => $db,
            'port'     => $port,
            'socket'   => $socket
        ));
    }

    /**
     * Create & store at _mysqli new mysqli instance
     *
     * @param array $params
     *
     * @return $this
     */
    private function setConnection(array $params)
    {
        foreach (array('host', 'username', 'password', 'db', 'port', 'socket') as $key) {
            $param = isset($params[$key]) ? $params[$key] : null;
            $this->connectionsSettings[$key] = $param;
        }

        return $this;
    }

    /**
     * Get singleton instance
     *
     * @return static
     */
    public static function instance(): self
    {
        if( self::$instance === null )
            return self::$instance = DBFactory::factory();

        return self::$instance;
    }

    /**
     * Perform a SELECT statement
     *
     * @param string $from       Table to select
     * @param string ...$columns Columns to select
     *
     * @return self
     * @throws DBException
     * @throws DBQueryException
     */
    public function selectFrom(string $from, ...$columns): self
    {
        $this->requiresReconnect();

        $columns = empty($columns) ? '*' : $this->escapeIdentifier($columns);

        if( $this->query = $this->prepare("SELECT {$columns} FROM `{$from}`") ) {
            $this->query->execute();
            if( $this->query->errno ) {
                $this->queryError('Unable to process MySQL query - ' . $this->query->error);
            }
        } else {
            $this->queryError('Unable to prepare statement - ' . $this->mysqli->error);
        }

        return $this;
    }

    /**
     * Checks if requires a reconnection
     *
     * @throws DBException
     */
    private function requiresReconnect()
    {
        if( ! $this->query_closed )
            $this->query->close();
        else
            $this->connect();
    }

    /**
     * Connect into Database
     *
     * @throws DBException
     */
    private function connect()
    {
        if( $this->mysqli !== null )
            return;

        $conn = new mysqli(...array_values($this->connectionsSettings));

        if( $conn->connect_error ) {
            throw new DBException(
                "Connect Error [{$conn->connect_errno}]: {$conn->connect_error}",
                $conn->connect_errno
            );
        }

        $this->mysqli = $conn;
        $this->setCharset($this->charset);
        $this->query_closed = false;
    }

    /**
     * Set connection charset
     *
     * @param string $charset Charset to apply
     *
     * @return self
     */
    public function setCharset(string $charset)
    {
        if( ! empty($charset) )
            $this->mysqli->set_charset(
                $this->charset = $charset
            );

        return $this;
    }

    /**
     * Escape identifier from given column name
     *
     * @param array<string>|string $column
     *
     * @return string
     */
    private function escapeIdentifier($column)
    {
        if( is_string($column) )
            $column = [$column];

        return implode(', ', Arr::wrapElementsWith($column, '`'));
    }

    /**
     * Prepare statement
     *
     * @param string $query Query
     *
     * @return false|mysqli_stmt
     */
    private function prepare(string $query)
    {
        return $this->mysqli->prepare($query);
    }

    /**
     * Throw given error
     *
     * @param string $error Error message
     *
     * @throws DBQueryException
     */
    private function queryError(string $error)
    {
        $this->disconnect();
        throw new DBQueryException($error);
    }

    /**
     * Close connection
     */
    public function disconnect()
    {
        $this->mysqli->close();
    }

    /**
     * Insert new rows into given table
     *
     * @param string $table   Table to insert
     * @param array  $columns Columns to insert
     * @param array  $values  Values
     *
     * @return self
     * @throws DBException
     */
    public function insertRow(string $table, array $columns, array $values): self
    {
        $this->requiresReconnect();

        list($sql, $paramTypes, $paramArray) = $this->prepareInsertStatement($table, $columns, $values);

        if( $this->query = $this->prepare($sql) ) {
            array_unshift($paramArray, $paramTypes);

            $this->query->bind_param(...$paramArray);
            $this->query->execute();

            if( $this->query->errno ) {
                $this->queryError('Unable to process MySQL query (check your params) - ' . $this->query->error);
            }
            $this->query_closed = false;
        } else {
            $this->queryError('Unable to prepare MySQL statement (check your syntax) - ' . $this->query->error);
        }
        return $this;
    }

    /**
     * Prepare insert statement
     *
     * @param string $table   table to insert
     * @param array  $columns columns to apply
     * @param array  $values  Data to insert
     *
     * @return array
     */
    private function prepareInsertStatement(string $table, array $columns, array $values): array
    {
        $sql = "INSERT INTO `{$table}` ({$this->escapeIdentifier($columns)}) VALUES ";

        $paramTypes = '';
        $paramArray = array();
        $sqlArray = array();
        $isMultipleInsert = null;

        foreach ($values as $row) {
            if( is_array($row) ) {
                $isMultipleInsert = true;
                $sqlArray[] = '(' . implode(',', array_fill(0, count($row), '?')) . ')';
                foreach ($row as $element) {
                    $paramTypes .= $this->getParamType($element);
                    $paramArray[] = $element;
                }
            } else {
                $isMultipleInsert = false;
                $sqlArray[] = '?';
                $paramTypes .= $this->getParamType($row);
                $paramArray[] = $row;
            }
        }

        if( $isMultipleInsert )
            $sql .= implode(',', $sqlArray);
        else
            $sql .= '(' . implode(',', $sqlArray) . ')';

        return array($sql, $paramTypes, $paramArray);
    }

    /**
     * Get type of given param
     *
     * @param mixed $var Var to check
     *
     * @return string
     */
    private function getParamType($var)
    {
        switch (gettype($var)) {
            case 'string':
                return 's';
            case 'double':
                return 'd';
            case 'integer':
                return 'i';
            default:
                return 'b';
        }
    }

    /**
     * Fetch data from table - SELECT clause
     *
     * @param callable|null $callback callback to be invoked in every row
     *
     * @return array
     */
    public function all(callable $callback = null): array
    {
        $params = array();
        $rows = array();
        $metaData = $this->query->result_metadata();

        while ($field = $metaData->fetch_field()) {
            $params[] = &$rows[$field->name];
        }

        $this->query->bind_result(...$params);

        $result = array();
        while ($this->query->fetch()) {
            $r = array();
            foreach ($rows as $key => $val) {
                $r[$key] = $val;
            }

            if( $callback !== null && is_callable($callback) )
                call_user_func($callback, $r);

            array_push($result, $r);
        }

        $this->closeQuery();

        return $result;
    }

    /**
     * Close query
     */
    private function closeQuery()
    {
        $this->query->close();
        $this->query_closed = true;
    }

}
