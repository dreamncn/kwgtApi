<?php
/**
 * File sql.php
 * Author : Dreamn
 * Date : 7/31/2020 1:38 AM
 * Description:
 */
namespace app\vendor\debug\sql;
use app\core\Error;
use PDO;
use PDOException;
use PDOStatement;
class sql{
    protected $sql=[];//查询过的sql语句列表
    protected $opt=[];//封装常见的数据库查询选项
    protected $page=null;//开启分页的分页数据
    protected $table_name;
    protected $traSql=null;//编译完成的sql语句
    protected $bindParam=[];//绑定的参数列表

    public function __construct($table_name='')
    {
        if (!class_exists("PDO") || !in_array("mysql", PDO::getAvailableDrivers(), true)) {
            Error::err('Database Err: PDO or PDO_MYSQL doesn\'t exist!');
        }
        //初始化基础数据
        $this->opt['type']='select';
        $this->opt['table_name']=$table_name;
        $this->table_name=$table_name;
    }
    //对数据进行处理，转换成编译所需的sql语句
    private function translateSql(){
        $sql=$this->opt['type'];//select insert delete update
        $table=$this->opt['table_name'];//必填项目
        if($sql==''||$table=='')Error::err('Database Err: Missing required item "select/insert/delete/update" and "table"');
        switch($sql){
            case 'select':
                $sql='';
                $sql.=$this->getOpt('SELECT','field');
                $sql.=$this->getOpt('FROM','table_name');
                $sql.=$this->getOpt('WHERE','where');
                $sql.=$this->getOpt('ORDER BY','order');
                $sql.=$this->getOpt('LIMIT','limit');
                break;
            case 'update':
                $sql='';
                $sql.=$this->getOpt('UPDATE','table_name');
                $sql.=$this->getOpt('SET','set');
                $sql.=$this->getOpt('WHERE','where');
                break;
            case 'insert':
                $sql='';
                if(isset($this->opt['model'])){
                    switch ($this->opt['model']){
                        case self::Duplicate:
                            $sql.=$this->getOpt('INSERT INTO','table_name');
                            $sql.=$this->getOpt('','key');
                            $sql.=$this->getOpt('VALUES','values');
                            $sql.=$this->getOpt('ON DUPLICATE KEY UPDATE','colums');
                            break;
                        case self::Normal:
                            $sql.=$this->getOpt('INSERT INTO','table_name');
                            $sql.=$this->getOpt('','key');
                            $sql.=$this->getOpt('VALUES','values');
                            break;
                        case self::Ignore:
                            $sql.=$this->getOpt('INSERT IGNORE INTO','table_name');
                            $sql.=$this->getOpt('','key');
                            $sql.=$this->getOpt('VALUES','values');
                            break;
                    }
                }

                break;
            case 'delete':
                $sql='';
                $sql.=$this->getOpt('DELETE FROM','table_name');
                $sql.=$this->getOpt('WHERE','where');
                break;
        }
        $this->traSql=$sql;
        if(isDebug()){
            logs('[SQL]'.$sql,'info');
        }
    }
    /*
     * 获取设置的字段信息
     * */
    private function getOpt($head,$opt){
        if(isset($this->opt[$opt]))return ' '.$head.' '.$this->opt[$opt].' ';
        return ' ';
    }
    /**
     * 获取数据库连接函数
     * @param $db_config array 数据库配置信息
     * @return PDO
     */
    protected function dbInstance($db_config)
    {
        if (empty($GLOBALS['mysql_instances'])) {
            try {
                $GLOBALS['mysql_instances'] = new PDO(
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
        }
        return $GLOBALS['mysql_instances'];
    }
    /**
     * @param string $table_name
     * @return sql
     */
    protected function table($table_name){
        $this->opt['table_name']=$table_name;
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


}
