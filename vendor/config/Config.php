<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\vendor\config;
class Config
{
    private $fileData;
    private $fileName;
    private $path = APP_CONF;

    public static function getInstance($file)
    {
        $conf = new Config();
        $conf->fileData = "";
        $conf->fileName = "$file.yml";
        return $conf->getConfigFile();
    }

    private function getConfigFile()
    {
        $file = $this->path . $this->fileName;
        if (file_exists($file)) {
            $this->fileData = Spyc::YAMLLoad($file);
        }
        return $this;
    }

    public function setLoaction($path)
    {
        $this->path = $path;
        return $this->getConfigFile();
    }

    public function get()
    {
        return $this->fileData;
    }

    public function getOne($key)
    {
        return isset($this->fileData[$key]) ? $this->fileData[$key] : null;
    }

    public function setAll($data)
    {
        $this->fileData = $data;
        $file = $this->path . $this->fileName;
        file_put_contents($file, Spyc::YAMLDump($this->fileData));
    }

    public function set($key, $val)
    {
        $this->fileData[$key] = $val;
        $file = $this->path . $this->fileName;
        file_put_contents($file, Spyc::YAMLDump($this->fileData));
    }
}
