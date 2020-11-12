<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * File mysql
 *
 * @package app\vendor\sql
 * Date: 2020/10/14 1:50 下午
 * Author: ankio
 * Desciption: sql的集合类
 */

namespace app\vendor\database;


use app\vendor\database\sql\Delete;
use app\vendor\database\sql\Insert;
use app\vendor\database\sql\Select;
use app\vendor\database\sql\sqlExec;
use app\vendor\database\sql\Update;

class Sql
{
    private $tableName;
    private $instances = [];
    protected $sqlIndex = "master";

    /*SQL语句插入方式*/

    public function __construct($tableName = '')
    {
        $this->sql = new sqlExec();
        $this->tableName = $tableName;
    }

    /**
     * 因为分离select、update等语法糖，导致无法使用事务进行统一管理
     * 除非底部共用一个sql执行，顶部多层封装，不使用继承，就是说，执行类与基类完全分离，最后commit实例化执行类执行，通过顶层共享sql执行类获取完整上下文
     */

    /**
     * @param string $name
     *
     * @return Delete|Insert|Select|Update|sqlExec
     */
    private function sqlInstance($name = "")
    {
        if ($name === "") return $this->sql;
        $class = 'app\vendor\database\sql\\' . $name;
        if (isset($this->instances[$name]) && get_class($this->instances[$name]) === $class)
            return $this->instances[$name];

        if (class_exists($class)) {
            $this->instances[$name] = new $class($this->tableName, $this->sql);
        } else {
            return $this->sql;
        }
        return $this->instances[$name];
    }


    public function select($field = "*")
    {
        return $this->sqlInstance("Select")->select($field);
    }

    public function getPage()
    {
        return $this->sqlInstance("Select")->getPage();
    }

    public function delete()
    {
        return $this->sqlInstance("Delete")->delete();
    }

    public function insert($model)
    {
        return $this->sqlInstance("Insert")->insert($model);
    }

    public function update()
    {
        return $this->sqlInstance("Update")->update();
    }

    public function execute($sql, $params = array(), $readonly = false)
    {
        return $this->sqlInstance()->execute($sql, $params, $readonly);
    }

    public function dumpSql()
    {
        return $this->sql->dumpSql();
    }

    public function beginTransaction()
    {
        $this->sqlInstance()->execute("BEGIN");
    }

    public function rollBack()
    {
        $this->sqlInstance()->execute("ROLLBACK");
    }

    public function commit()
    {
        $this->sqlInstance()->execute("COMMIT");
    }

    public function setDbLocation($path,$name){
        $this->sql->setDbFile($path,$name);
        return $this;
    }

    public function setDatabase($sqlType)
    {
        $this->sql->setDatabase($sqlType);
        $this->sqlIndex = $sqlType;
        return $this;
    }

    public function emptyTable($string)
    {
        $this->sqlInstance()->emptyTable($string);
    }
}
