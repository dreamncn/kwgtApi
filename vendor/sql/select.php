<?php
/**
 * File select.php
 * Author : Dreamn
 * Date : 7/31/2020 1:22 AM
 * Description:
 */
namespace app\core\lib\sql;
use app\core\Error;

class select extends sql{
    /************select语句封装***********/
    /**
     * @param string $field
     * @return select
     */
    public function select($field="*"){
        $this->opt=[];
        $this->opt['table_name']=$this->table_name;
        $this->opt['type']='select';
        $this->opt['field']=$field;
        $this->bindParam=[];
        return $this;
    }
    /**
     * @param string $table_name
     * @return select
     */
    public function table($table_name){
        return parent::table($table_name);
    }
    /**
     * @param array $conditions 查询条件，支持后面几种格式
     * @return select
     */
    public function where($conditions){
        return parent::where($conditions);
    }
}
