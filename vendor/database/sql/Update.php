<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

/**
 * File update
 *
 * @package app\vendor\sql
 * Date: 2020/10/14 11:27 下午
 * Author: ankio
 * Desciption:
 */

namespace app\vendor\database\sql;


class Update extends sqlBase
{
    public function update()
    {
        $this->opt = [];
        $this->opt['tableName'] = $this->tableName;
        $this->opt['type'] = 'update';
        $this->bindParam = [];
        return $this;
    }

    public function table($table_name)
    {
        return parent::table($table_name);
    }

    public function where($conditions)
    {
        return parent::where($conditions);
    }

    public function set($row)
    {
        $values = [];
        $set = '';
        foreach ($row as $k => $v) {
            if (is_int($k)) {
                $set .= $v . ',';
                continue;
            }
            $values[":_UPDATE_" . $k] = $v;
            $set .= "`{$k}` = " . ":_UPDATE_" . $k . ',';
        }
        $set = rtrim($set, ",");
        $this->bindParam += $values;
        $this->opt['set'] = $set;
        return $this;
    }

    private function translateSql()
    {
        $sql = '';
        $sql .= $this->getOpt('UPDATE', 'tableName');
        $sql .= $this->getOpt('SET', 'set');
        $sql .= $this->getOpt('WHERE', 'where');
        $this->traSql = $sql . ";";


    }

    public function commit()
    {
        $this->translateSql();
        return $this->sql->execute($this->traSql, $this->bindParam, false);
    }
}
