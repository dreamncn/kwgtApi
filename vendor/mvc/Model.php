<?php

namespace app\vendor\mvc;



use app\vendor\sql\mysql;


class Model extends mysql
{

    /**
     * Model constructor.
     * @param null $table_name
     */
    public function __construct($table_name = null)
    {
       // if ($table_name) $this->table_name = $table_name;
        parent::__construct($table_name);
    }



    /*
     * 单个设置
     * */
    public function setOption($idName,$id,$opt,$val){
      return  $this->update()->where([$idName=>$id])->set([$opt=>$val])->commit();
    }




}
