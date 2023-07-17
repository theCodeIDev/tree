<?php

class Database
{
    /** @var PDO */
    public $conn = null;
    public $error = '';
    public $errno = 0;
    public $sql = '';
    public $errInfo = array('', '', '');
    public $trace = array();
    public $bTransaction = false;
    public $addr = '';
    public $db_name = '';
    public $db_drv = '';
    /** @var PDOStatement */
    public $lastStmt = null;
    public $params = [];

    /**
     * Конструктор подключения к БД
     *
     *
     * @param string $h Хост
     * @param string $u Login
     * @param string $p Password
     * @param string $db_name Database name
     * @param string $driver PDO driver name
     * @param string $charset Character set
     * @param string $charset Database schema
     */
    function __construct($h = NULL, $u = NULL, $p = NULL, $db = NULL, $driver = 'mysql', $charset = 'UTF8', $schema = '')
    {
        try {
            $opt = [];
            $srv = $h ? $h : SQL_SERVER;
            $usr = $u ? $u : SQL_USER;
            $pwd = $p ? $p : SQL_PWD;

            $db_name = $db ? $db : SQL_BASE;
            $this->db_drv = $driver;

            $m = [];
            $port = 0;
            if (preg_match('/^(.+):(\d+)$/', $srv, $m)) {
                $srv = $m[1];
                $port = intval($m[2]);
            }

            $dsn = "$driver:host=$srv;dbname=$db_name";
            if ($port) $dsn .= ";port=$port";
            if ($charset) $dsn .= ";charset=$charset";

            $this->addr = $srv;
            $this->db_name = $db_name;
            $this->conn = $usr ? new PDO($dsn, $usr, $pwd, $opt) : new PDO($dsn);

        } catch (PDOException $ex) {
            $this->error = $ex->getMessage();
            $this->errno = $ex->getCode();
            $this->trace = $ex->getTraceAsString();
        }
    }

    /**
     * Check if connection is successful
     * @return boolean
     */
    public function valid()
    {
        return $this->conn != null;
    }


    private function fillStmtError()
    {
        $err = $this->lastStmt->errorCode();
        $this->error = $err != 0 ? "Error:$err" : '';
        $this->errInfo = $this->lastStmt->errorInfo();
        if (isset($this->errInfo[2])) {
            $this->error = $this->errInfo[2];
        }
    }

    private function fillError()
    {
        $err = $this->conn->errorCode();
        $this->error = $err != 0 ? "Error:$err" : '';
        $this->errInfo = $this->conn->errorInfo();
        if (isset($this->errInfo[2])) {
            $this->error = $this->errInfo[2];
        }
    }


    /**
     * Last inserted row Id
     * @returns int
     */
    public function lastInsertId($name = null)
    {
        return intval($this->conn->lastInsertId($name));
    }


    /**
     * MySQL Query + read
     *
     * @param string Query
     * @returns array
     */
    public function select($query, $fetchType = PDO::FETCH_ASSOC)
    {
        $this->sql = $query;
        $this->lastStmt = $this->conn->query($query, $fetchType);
        $this->fillError();
        if ($this->lastStmt === FALSE) {
            return false;
        }
        $result = array();
        foreach ($this->lastStmt as $rez) {
            $result[] = $rez;
        }
        $this->lastStmt = null;
        return $result;
    }


    /**
     * Affected by last query rows count
     *
     * @returns int
     */
    public function affectedRows()
    {
        return $this->lastStmt ? $this->lastStmt->rowCount() : 0;
    }

    public function inTransaction()
    {
        return $this->bTransaction;
    }

    public function beginTransaction()
    {
        $this->bTransaction = $this->conn->beginTransaction();
        return $this->bTransaction;
    }

    public function commit()
    {
        $this->bTransaction = false;
        return $this->conn->commit();
    }

    public function rollBack()
    {
        $this->bTransaction = false;
        return $this->conn->rollBack();
    }

    /**
     * PDO prepare query
     *
     * @param string Query
     * @return connect_db
     */
    public function prepare($sql)
    {
        $this->lastStmt = $this->conn->prepare($sql);
        $this->fillError();
        $this->sql = $sql;
        $this->params = [];
        return $this;
    }


    /**
     * bind Value
     *
     * @param string Parameter name
     * @param any Parameter value
     * @param number Parameter type, default PDO:PARAM_STR
     * @return connect_db
     */
    public function bind($par, $val, $type = PDO::PARAM_STR)
    {
        if (is_integer($val)) $type = PDO::PARAM_INT;
        $this->lastStmt->bindValue($par, $val, $type);
        $this->params[$par] = $type == PDO::PARAM_INT ? intval($val) : $val;
        return $this;
    }


    public function execute()
    {
        $ret = $this->lastStmt->execute();
        $this->fillStmtError();
        return $ret;
    }

    public function execute_all($type = PDO::FETCH_ASSOC, $default = [])
    {
        $ok = $this->lastStmt->execute();
        $this->fillStmtError();
        if ($ok) return $this->lastStmt->fetchAll($type);
        return $default;

    }


}
