<?php

namespace app\vendor\mvc;


use app\vendor\debug\Log;

class Controller
{
    private $layout = '';//layout布局文件
    public $_auto_display = true;//是否自动展示
    private $_auto_path_dir = '';//非自动定位的view的路径的真实路径
    protected $_v;//view对象
    private $_data = array();//模板参数数组


    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
    }

    public function setLayout($file)
    {
        $this->layout = $file;
    }

    public function setAutoPathDir($dir)
    {
        $this->_auto_path_dir = $dir;
    }

    function setData($name, $value)
    {
        $this->_data[$name] = $value;
    }

    function setArray($array)
    {
        $this->_data = $array;
    }


    /**
     * @param null $tpl_name 模板名称
     * @param bool $return 是否直接返回
     * @return false|string
     */
    public function display($tpl_name, $return = false)
    {
        $GLOBALS['display_start'] = microtime(true);
        Log::debug('view', 'Try to compile file "' . $tpl_name . '"');
        if (!$this->_v) {
            $compile_dir = APP_TMP;
            if ($this->_auto_path_dir !== "")
                $this->_v = new View($this->_auto_path_dir, $compile_dir);
            else
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
