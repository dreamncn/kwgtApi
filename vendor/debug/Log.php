<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\vendor\debug;
use Exception;

class Log
{
    private $handler;
    private $level;
    public function __construct($file = '', $level = 15)
    {
        $file=APP_LOG . date('Y-m-d') . DS . $file . '.log';
        $dir_name = dirname($file);
        //目录不存在就创建
        if (!file_exists($dir_name)) {
            $this->mkdirs($dir_name);
        }
        $this->handler = fopen($file, 'a');
        $this->level = $level;
    }

    public function __destruct()
    {
        fclose($this->handler);
    }

    public static function debug($tag, $msg)
    {
        if(!isDebug())return;
        $self = new self($tag, 15);
        $self->write(1, $msg);
    }

    /**
     * @param $level
     * @param $msg
     */
    protected function write($level, $msg)
    {
        $msg = '[' . date('Y-m-d H:i:s') . '][' . $this->getLevelStr($level) . '] ' . $msg . "\n";
        flock($this->handler, LOCK_EX);
        fwrite($this->handler, $msg, strlen($msg));
        flock($this->handler, LOCK_UN);
    }

    private function getLevelStr($level)
    {
        switch ($level) {
            case 1:
                return 'debug';
                break;
            case 2:
                return 'info';
                break;
            case 4:
                return 'warn';
                break;
            case 8:
                return 'error';
                break;
            default:
                return 'debug';
        }
    }

    public static function warn($tag, $msg)
    {
        $self = new self($tag, 15);
        $self->write(4, $msg);
    }

    public static function error($tag, $msg)
    {
        $self = new self($tag, 15);
        $debugInfo = debug_backtrace();
        $stack = "[";
        foreach ($debugInfo as $key => $val) {
            if (array_key_exists("file", $val)) {
                $stack .= ",file:" . $val["file"];
            }
            if (array_key_exists("line", $val)) {
                $stack .= ",line:" . $val["line"];
            }
            if (array_key_exists("function", $val)) {
                $stack .= ",function:" . $val["function"];
            }
        }
        $stack .= "]";
        $self->write(8, $stack . $msg);
    }

    public static function info($tag, $msg)
    {
        $self = new self($tag, 15);
        $self->write(2, $msg);
    }


    public function mkdirs($dir)
    {
        if (is_dir(dirname($dir))) {
            mkdir($dir);
        } else  $this->mkdirs(dirname($dir));

    }

    public static function rm($date, $logName)
    {
        try{
            if($date==null&&$logName==null){
                rmdir(APP_LOG);
                mkdir(APP_LOG);
            }
            if($date!==null&&$logName==null){
                rmdir(APP_LOG.$date);
            }
            if($date!==null&&$logName!==null){
                unlink(APP_LOG.$date.$logName);
            }

        }catch (Exception $e){

        }
    }
}
