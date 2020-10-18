<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * File insert
 *
 * @package app\vendor\sql
 * Date: 2020/10/14 11:31 下午
 * Author: ankio
 * Desciption:
 */

namespace app\vendor\sql\sql;


use app\vendor\debug\Error;
use app\vendor\debug\Log;

class insert extends sqlBase
{
    /*SQL语句插入方式*/
    const Normal='NORMAL';
    const Ignore='IGNORE';
    const Duplicate='DUPLICATE';
    public function insert($model=self::Normal){
        $this->opt=[];
        $this->opt['tableName']=$this->tableName;
        $this->opt['type']='insert';
        $this->opt['model']=$model;
        $this->bindParam=[];
        return $this;
    }
    public function table($table_name){
        return parent::table($table_name);
    }

    public function where($conditions){
        return parent::where($conditions);
    }

    public function keyValue($kv){
        $key=array_keys($kv);
        $value=array_values($kv);
        return $this->keys($key)->values([$value]);
    }

    public function keys($key,$colums=array()){
        if($this->opt['model']==self::Duplicate&&sizeof($colums)==0){
            Error::err('Database Err: duplicate insert must have update field');
        }
        $value = '';
        foreach ($key as $v){
            $value.="`{$v}`,";
        }
        $value='('.rtrim($value, ",").')';
        $this->opt['key']=$value;
        foreach ($colums as $k) {
            $update[] = "`{$k}`" . " = VALUES(" . $k.')';
        }
        if($colums!==[])
            $this->opt['colums']=implode(', ', $update);
        return $this;
    }
    public function values($row){
        $length= sizeof($row);
        $k=0;$values=[];$marks='';
        for($i=0;$i<$length;$i++){
            $marks.='(';
            foreach ($row[$i] as $val){
                $values[":_INSERT_" . $k] = $val;
                $marks.= ":_INSERT_" . $k.',';
                $k++;

            }
            $marks=rtrim($marks, ",").'),';
        }
        $marks=rtrim($marks, ",");
        $this->opt['values']=$marks;
        $this->bindParam+=$values;

        return $this;
    }
    private function translateSql(){
        $sql='';
        switch ($this->opt['model']){
            case self::Duplicate:
                $sql.=$this->getOpt('INSERT INTO','tableName');
                $sql.=$this->getOpt('','key');
                $sql.=$this->getOpt('VALUES','values');
                $sql.=$this->getOpt('ON DUPLICATE KEY UPDATE','colums');
                break;
            case self::Normal:
                $sql.=$this->getOpt('INSERT INTO','tableName');
                $sql.=$this->getOpt('','key');
                $sql.=$this->getOpt('VALUES','values');
                break;
            case self::Ignore:
                $sql.=$this->getOpt('INSERT IGNORE INTO','tableName');
                $sql.=$this->getOpt('','key');
                $sql.=$this->getOpt('VALUES','values');
                break;
        }
        $this->traSql=$sql;
        if(isDebug()){
            Log::debug('SQL',$sql);
        }
    }
    public function commit(){
        $this->translateSql();
        $this->sql->execute($this->traSql,$this->bindParam,false);
        return $this->sql->dbInstance($GLOBALS['database'][$this->sql->sqlIndex])->lastInsertId();
    }

}
