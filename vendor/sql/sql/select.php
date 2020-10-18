<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\vendor\sql\sql;

use app\vendor\debug\Log;

class select extends sqlBase {
    protected $page=null;//开启分页的分页数据
    /**
     * @param string $field
     *
     * @return select
     */
    public function select($field="*"){
        $this->opt=[];
        $this->opt['tableName']=$this->tableName;
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
     * @param $string
     *
     * @return $this
     */
    public function orderBy($string)
    {
        $this->opt['order']=$string;
        return $this;
    }


    public function limit($limit='1')
    {
        unset($this->opt['page']);
        $this->opt['limit']=$limit;
        return $this;
    }
    public function page($start=1,$count=10,$range=10)
    {
        unset($this->opt['limit']);
        $this->opt['page'] = true;
        $this->opt['start'] =$start;
        $this->opt['count'] =$count;
        $this->opt['range'] =$range;
        return $this;
    }
    /**
     * @param array $conditions 查询条件，支持后面几种格式
     * @return select
     */
    public function where($conditions){
        return parent::where($conditions);
    }

    private function translateSql(){
        $sql='';
        $sql.=$this->getOpt('SELECT','field');
        $sql.=$this->getOpt('FROM','tableName');
        $sql.=$this->getOpt('WHERE','where');
        $sql.=$this->getOpt('ORDER BY','order');
        $sql.=$this->getOpt('LIMIT','limit');
        $this->traSql=$sql;
        if(isDebug()){
            Log::debug('SQL',$sql);
        }
    }

    public function commit(){
        if(isset($this->opt['page'])){
            $sql= 'SELECT COUNT(*) as M_COUNTER ';

            $sql.=$this->getOpt('FROM','table_name');
            $sql.=$this->getOpt('WHERE','where');

            $sql.=$this->getOpt('ORDER BY','order');

            $total = $this->sql->execute( $sql, $this->bindParam,true);
            $this->page=$this->pager($this->opt['start'], $this->opt['count'], $this->opt['range'], $total[0]['M_COUNTER']);
            if(!empty($this->page))
                $this->opt['limit'] =  $this->page['offset'] . ',' . $this->page['limit'];
        }
        $this->translateSql();
        return $this->sql->execute($this->traSql,$this->bindParam,true);
    }
    /**
     * 分页处理函数
     * @param $page
     * @param int $pageSize
     * @param int $scope
     * @param int $total
     * @return array|null
     */
    protected function pager($page, $pageSize = 10, $scope = 10, $total = 0)
    {
        $this->page = null;
        if ($total > $pageSize) {
            $total_page = ceil($total / $pageSize);
            $page = min(intval(max($page, 1)), $total_page);
            $this->page = array(
                'total_count' => $total,//总数量
                'page_size' => $pageSize,//一页大小
                'total_page' => $total_page,//总页数
                'first_page' => 1,//第一页
                'prev_page' => ((1 == $page) ? 1 : ($page - 1)),//上一页
                'next_page' => (($page == $total_page) ? $total_page : ($page + 1)),//下一页
                'last_page' => $total_page,//最后一页
                'current_page' => $page,//当前页
                'all_pages' => array(),//所有页
                'offset' => ($page - 1) * $pageSize,
                'limit' => $pageSize,
            );
            $scope = (int)$scope;
            if ($total_page <= $scope) {
                $this->page['all_pages'] = range(1, $total_page);
            } elseif ($page <= $scope / 2) {
                $this->page['all_pages'] = range(1, $scope);
            } elseif ($page <= $total_page - $scope / 2) {
                $right = $page + (int)($scope / 2);
                $this->page['all_pages'] = range($right - $scope + 1, $right);
            } else {
                $this->page['all_pages'] = range($total_page - $scope + 1, $total_page);
            }
        }
        return $this->page;
    }
    public function getPage(){
        return $this->page;
    }


}
