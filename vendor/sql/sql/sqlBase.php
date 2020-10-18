<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * File sqlBase
 *
 * @package app\vendor\sql\sql
 * Date: 2020/10/18 12:28 下午
 * Author: ankio
 * Desciption:
 */

namespace app\vendor\sql\sql;


use app\vendor\debug\Error;
use PDO;

class sqlBase
{

    protected $opt=[];//封装常见的数据库查询选项

    protected $tableName;
    protected $traSql=null;//编译完成的sql语句
    protected $bindParam=[];//绑定的参数列表
    protected $sql=null;




    /**
     * sql constructor.
     *
     * @param string $tableName
     * @param sql  $sqlDetail
     */
    public function __construct( $tableName , $sqlDetail)
    {
        if (!class_exists("PDO") || !in_array("mysql", PDO::getAvailableDrivers(), true)) {
            Error::err('Database Err: PDO or PDO_MYSQL doesn\'t exist!');
        }
        //初始化基础数据
        $this->opt['type']='select';
        $this->opt['tableName']=$tableName;
        $this->tableName=$tableName;
        $this->sql=$sqlDetail;
    }

    protected function getOpt($head,$opt){
        if(isset($this->opt[$opt]))return ' '.$head.' '.$this->opt[$opt].' ';
        return ' ';
    }

    /**
     * @param string $tableName
     *
     * @return sqlBase
     */
    protected function table($tableName){
        $this->opt['tableName']=$tableName;
        return $this;
    }

    /**
     * @param array $conditions 查询条件，支持后面几种格式
     *
     * @return sql|sqlBase
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

}
