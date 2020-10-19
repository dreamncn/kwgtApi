<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * File delete
 *
 * @package app\vendor\sql
 * Date: 2020/10/14 11:20 下午
 * Author: ankio
 * Desciption:delete SQL
 */

namespace app\vendor\sql\sql;


use app\vendor\debug\Log;

class delete extends sqlBase
{
    public function delete(){
        $this->opt=[];
        $this->opt['tableName']=$this->tableName;
        $this->opt['type']='delete';
        $this->bindParam=[];
       // $this->setType($sqlIndex);

        return $this;
    }
    public function table($table_name){
        return parent::table($table_name);
    }

    public function where($conditions){
        return parent::where($conditions);
    }

    private function translateSql(){
        $sql='';
        $sql.=$this->getOpt('DELETE FROM','tableName');
        $sql.=$this->getOpt('WHERE','where');
        $this->traSql=$sql.";";

        if(isDebug()){
            Log::debug('SQL',$sql);
        }
    }

    public function commit(){
        $this->translateSql();
        return $this->sql->execute($this->traSql,$this->bindParam,false);
    }
}
