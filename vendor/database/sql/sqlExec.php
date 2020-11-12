<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * File sql
 *
 * @package app\vendor\sql
 * Date: 2020/10/14 1:50 下午
 * Author: ankio
 * Desciption:sql sql的封装
 */

namespace app\vendor\database\sql;

use app\vendor\config\Config;
use app\vendor\debug\Error;
use app\vendor\debug\Log;
use PDO;
use PDOException;
use PDOStatement;

class sqlExec
{


    protected $sqlType = "mysql";
    public $sqlIndex = "master";
    private $sqlList = [];
    private $db = null;
    private $name = null;
    private $dbData = null;

    public function setDbFile($file, $name)
    {
        $this->db = $file;
        $this->name = $name;
        $this->getDbFile();
    }

    private function getDbData()
    {
        if ($this->dbData === null) {
            return $this->getDbFile();
        }
        return $this->dbData === null;
    }

    private function getDbFile()
    {
        if ($this->db !== null && $this->name !== null) {
            $this->dbData = Config::getInstance($this->name)->setLoaction($this->db)->get();
        } else {
            $this->dbData = Config::getInstance("db")->get();;
        }
        return $this->dbData;

    }

    public function setDatabase($sqlIndex)
    {
        $this->getDbFile();
        $this->sqlIndex = $sqlIndex;
        $this->sqlType = isset($this->getDbData()[$sqlIndex]['type']) ? $GLOBALS['database'][$sqlIndex]['type'] : "mysql";
        return $this;
    }

    /**
     * 获取数据库连接函数
     *
     * @param $db_config array 数据库配置信息
     *
     * @return PDO
     */
    public function dbInstance($db_config)
    {

        $dsn = [
            "mysql" => "mysql:dbname={$db_config['db']};host={$db_config['host']};port={$db_config['port']}",
            "sqlite3" => "sqlite:{$db_config['host']}",
            "sqlite2" => "sqlite:{$db_config['host']}",
            "sqlserver" => "odbc:Driver={SQL Server};Server={$db_config['host']};Database={$db_config['db']}",
        ];
        $connectData = "";
        try {

            if (!isset($dsn[$this->sqlType]))
                Error::err("Database Err: We don't support this type database.({$this->sqlType})");
            $connectData = $dsn[$this->sqlType];
            return new PDO(
                $dsn[$this->sqlType],
                $db_config['username'],
                $db_config['password'],
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'' . $db_config['charset'] . '\'',
                    PDO::ATTR_PERSISTENT => true
                ));
        } catch (PDOException $e) {
            Error::err('Database Err: ' . $e->getMessage() . ". SQL Connected Data  {$connectData}");
        }
        return null;
    }


    /**
     * 直接执行sql语句
     *
     * @param string $sql sql语句
     * @param array $params
     * @param bool $readonly 是否为查找模式
     *
     * @return mixed
     */
    public function execute($sql, $params = array(), $readonly = false)
    {

        $start = microtime(true);
        /**
         * @var $sth PDOStatement
         */

        $sth = $this->dbInstance($this->getDbData()[$this->sqlIndex])->prepare($sql);
        if ($sth == false)
            Error::err('Database SQL: "' . $sql . '", Can\'t prepared! ');
        if (is_array($params) && !empty($params)) foreach ($params as $k => $v) {
            if (is_int($v)) {
                $data_type = PDO::PARAM_INT;
            } elseif (is_bool($v)) {
                $data_type = PDO::PARAM_BOOL;
            } elseif (is_null($v)) {
                $data_type = PDO::PARAM_NULL;
            } else {
                $data_type = PDO::PARAM_STR;
            }

            $sth->bindParam($k, $v, $data_type);
        }
        if ($sth->execute()) {
            $end = microtime(true) - $start;
            if (isDebug()) {
                Log::debug('sql', $sql);
                $sqlDefault = $sql;
                foreach ($params as $k => $v) {
                    $sqlDefault = str_replace($k, "\"$v\"", $sqlDefault);
                }
                $this->sqlList[] = [$sql, $sqlDefault, strval($end * 1000) . "ms"];
            }

            return $readonly ? $sth->fetchAll(PDO::FETCH_ASSOC) : $sth->rowCount();

        }
        $err = $sth->errorInfo();
        Error::err('Database SQL: "' . $sql . '", ErrorInfo: ' . $err[2]);
        return false;
    }


    public function emptyTable($string)
    {
        switch ($this->sqlType) {
            case "sqlite2":
            case "sqlite3":
                return $this->execute("DELETE FROM '$string';");
            case "mysql":
                return $this->execute("TRUNCATE TABLE '$string';");
        }

        return $this->execute("TRUNCATE TABLE '$string';");
    }

    public function dumpSql()
    {
        return $this->sqlList;
    }
}
