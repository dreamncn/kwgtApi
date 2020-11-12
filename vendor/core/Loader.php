<?php


namespace app\vendor\core;


use app\vendor\debug\Error;
use app\vendor\debug\Log;


class Loader
{
    public static function register()
    {
        spl_autoload_register('app\\vendor\\core\\Loader::autoload', true, true);
        $data = scandir(APP_LIB);
        foreach ($data as $value) {
            if ($value != '.' && $value != '..') {
                $file = APP_LIB . DS . $value . DS . 'autoload.php';
                if (file_exists($file)) include $file;//注册第三方库的自动加载
            }
        }
    }

    public static function autoload($realClass)
    {

        $classArr = self::getClass($realClass);
        $class = $classArr['class'] . '.php';
        $namespace = $classArr['namespace'];

        $file = APP_DIR . DS . str_replace('app/', '', $namespace) . DS . $class;
        if (file_exists($file)) {
            include_once $file;
            if (isDebug())
                Log::info('Loader', 'Load Class "' . $realClass . '"');
            return;
        }
        if (isDebug())
            Log::warn('Loader', 'We Can\'t find this class "' . $realClass . '"(' . $file . ') in default Loader , You may have loaded it in another loader');
    }

    public static function getClass($class)
    {
        if (strpos($class, '.')) Error::err('[Loader]"' . $class . '" is not a valid class name！');
        $name = explode('\\', $class);
        $size = sizeof($name);
        $namespace = '';
        for ($i = 0; $i < $size - 1; $i++) {
            $namespace .= $name[$i] . (($i < $size - 2) ? '/' : '');
        }
        return array('namespace' => $namespace, 'class' => $name[$size - 1]);
    }

}
