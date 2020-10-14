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

namespace app\vendor\sql;

use app\vendor\cache\Cache;
use app\vendor\debug\Error;
use PDO;
use PDOException;
use PDOStatement;
class sql{
    protected $sql=[];//查询过的sql语句列表
    protected $opt=[];//封装常见的数据库查询选项
    protected $page=null;//开启分页的分页数据
    protected $tableName;
    protected $traSql=null;//编译完成的sql语句
    protected $bindParam=[];//绑定的参数列表

    public function __construct($tableName='')
    {
        if (!class_exists("PDO") || !in_array("mysql", PDO::getAvailableDrivers(), true)) {
            Error::err('Database Err: PDO or PDO_MYSQL doesn\'t exist!');
        }
        //初始化基础数据
        $this->opt['type']='select';
        $this->opt['tableName']=$tableName;
        $this->tableName=$tableName;
    }

    /**
     * 获取数据库连接函数
     * @param $db_config array 数据库配置信息
     * @return PDO
     */
    protected function dbInstance($db_config)
    {
        try {
            return new PDO(
                'mysql:dbname=' . $db_config['MYSQL_DB'] . ';host=' . $db_config['MYSQL_HOST'] . ';port=' . $db_config['MYSQL_PORT'],
                $db_config['MYSQL_USER'],
                $db_config['MYSQL_PASS'],
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'' . $db_config['MYSQL_CHARSET'] . '\'',
                    PDO::ATTR_PERSISTENT => true
                ));
        } catch (PDOException $e) {
            Error::err('Database Err: ' . $e->getMessage());
        }
        return null;
    }

    //对数据进行处理，转换成编译所需的sql语句
    private function translateSql(){}
    /*
     * 获取设置的字段信息
     * */
    protected function getOpt($head,$opt){
        if(isset($this->opt[$opt]))return ' '.$head.' '.$this->opt[$opt].' ';
        return ' ';
    }

    /**
     * @param string $tableName
     * @return sql
     */
    protected function table($tableName){
        $this->opt['tableName']=$tableName;
        return $this;
    }

    /**
     * @param array $conditions 查询条件，支持后面几种格式
     * @return sql
     */
    protected function where($conditions){
        if (is_array($conditions) && !empty($conditions)) {
            $sql = null;
            $join = array();
            reset($conditions);

            foreach ($conditions as $key => $condition){
                if (is_int($key)) {
                    $join[] = $condition;
                    unset($conditions[$key]);
                    continue;
                }
                $key=str_replace('.', '_', $key);
                if (substr($key, 0, 1) != ":") {
                    unset($conditions[$key]);
                    $conditions[":_WHERE_" .$key ] = $condition;
                    $join[] = "`" . str_replace('.', '`.`', $key) . "` = :_WHERE_" . $key;
                }

            }
            if (!$sql) $sql = join(" AND ", $join);

            $this->opt['where'] = $sql;
            $this->bindParam+= $conditions;
        }
        return $this;
    }

    /**
     * 直接执行sql语句
     * @param string $sql sql语句
     * @param array $params
     * @param bool $readonly 是否为查找模式
     * @return mixed
     */
    public function execute($sql, $params = array(),$readonly=false)
    {
        /**
         * @var $sth PDOStatement
         */
        $this->sql[] = $sql;

        $sth = $this->dbInstance($GLOBALS['mysql'])->prepare($sql);

        if (is_array($params) && !empty($params)) foreach ($params as $k => &$v) {
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
        if ($sth->execute()) return $readonly ? $sth->fetchAll(PDO::FETCH_ASSOC) : $sth->rowCount();
        $err = $sth->errorInfo();
        Error::err('Database SQL: "' . $sql . '", ErrorInfo: ' . $err[2]);
        return false;
    }

    public function commit(){
        $this->translateSql();
        return $this->execute($this->traSql,$this->bindParam,false);
    }

}
