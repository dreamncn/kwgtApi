<?php

namespace app\vendor\mvc;



/**
 * Class Model
 * @package lib\speed\mvc
 */
class Model extends Mysql
{

    public $page;
    /**
     * 数据表名称
     * @var string $table_name
     */
    protected $table_name;


    /**
     * Model constructor.
     * @param null $table_name
     */
    public function __construct($table_name = null)
    {
        if ($table_name) $this->table_name = $table_name;
        parent::__construct($table_name);
    }



    /*
     * 单个设置
     * */
    public function setOption($idName,$id,$opt,$val){
      return  $this->update()->where(array($idName=>$id))->set(array($opt=>$val))->commit();
    }
}
