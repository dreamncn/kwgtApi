<?php

namespace app\vendor\mvc;


use app\vendor\lib\Log;

class Controller
{
    public string $layout = '';
    public bool $_auto_display = true;
    protected $_v;
    private array $_data = array();

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
    }

    public function &__get($name)
    {
        return $this->_data[$name];
    }

    function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }


    /**
     * @param $url string 重定向
     */
    public function location($url){
        header("Location: $url");
    }

    /**
     * @param null $tpl_name 模板名称
     * @param bool $return 是否直接返回
     * @return false|string
     */
    public function display($tpl_name, $return = false)
    {
        $GLOBALS['display_start']=microtime(true);
        Log::debug('view','Try to compile file "'.$tpl_name.'"');

        if (!$this->_v) {
            $compile_dir = APP_TMP;
            $this->_v = new View(APP_VIEW, $compile_dir);
        }
        $this->_v->assign(get_object_vars($this));
        $this->_v->assign($this->_data);
        if ($this->layout) {
            $this->_v->assign('__template_file', $tpl_name);
            $tpl_name = $this->layout;
        }
        $this->_auto_display = false;

        if ($return) {
            return $this->_v->render($tpl_name);
        } else {
            echo $this->_v->render($tpl_name);
            return '';
        }
    }
}
