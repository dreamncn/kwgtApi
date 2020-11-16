<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\vendor\web;


class Cookie
{
    private static $instance=null;
    private  $expire=0;//过期时间 单位为s 默认是会话 关闭浏览器就不在存在
    private $path='';//路径 默认在本目录及子目录下有效 /表示根目录下有效
    private $domain='';//域
    private $secure=false;//是否只在https协议下设置默认不是
    private $httponly=false;//如果为TRUE，则只能通过HTTP协议访问cookie。 这意味着脚本语言（例如JavaScript）无法访问cookie
    /**
     * [__construct description]
     * 构造函数完成cookie参数初始化工作
     * @DateTime 2018-07-25T09:50:51+0800
     * @param    array                    $options [cookie相关选项]
     */
    private function __construct(array $options=[]){
        $this->getOptions($options);
    }
    private function getOptions(array $options=[]){
        if(isset($options['expire'])) {$this->expire=$options['expire'];}
        if(isset($options['path'])) $this->path=$options['path'];
        if(isset($options['domain'])) $this->domain=$options['domain'];
        if(isset($options['secure'])) $this->secure=$options['secure'];
        if(isset($options['httponly'])) $this->httponly=$options['httponly'];
        return $this;
    }
    /**
     * [getInstance description]
     * 单例模式
     * @DateTime 2018-07-25T09:50:01+0800
     * @param    array                    $options [cookie相关选项]
     * @return   object                    $options [对象实例]
     */
    public static function getInstance(array $options=[]){
        if(is_null(self::$instance)){
            $class=__CLASS__;
            self::$instance=new $class($options);
        }
        return self::$instance;
    }
    /**
     * 设置cookie
     * Func description
     * @DateTime 2018-07-25T09:42:37+0800
     * @param    [string]                 $name    [cookie名称]
     * @param    [mixed]                   $vlaue   [cookie值]
     * @param    array                    $options [cookie相关选项]
     */
    public function set($name,$value,array $options=[]){
        if(is_array($options)&&count($options)>0){
            $this->getOptions($options);
        }
        if(is_array($value)||is_object($value)){
            $value=json_encode($value);
            var_dump($value);

        }
        setcookie($name,$value,$this->expire,$this->path,$this->domain,$this->secure,$this->httponly);
    }
    /**
     * [get description]
     * 读取cookie值
     * @DateTime 2018-07-25T11:34:04+0800
     * @param    [string]                   $name [cookie名称]
     * @return   [mixed]                         [数组形式的值或者单个的值]
     */
    public function get($name){
        $value=$_COOKIE[$name];
        if(is_array($value)){
            $arr=[];
            foreach ($value as $k => $v) {
                # code...
                $arr[$k]=substr($v, 0,1)=='{'? json_decode($value):$v;
            }
            return $arr;
        }else{
            return substr($value, 0,1)=='{'? json_decode($value):$value;}
    }
    /**
     * [delete description]
     * 删除相应的cookie
     * @DateTime 2018-07-25T11:53:24+0800
     * @param    [string]                   $name    [cookie名称 可以是数组]
     * @param    array                    $options [cookie相关参数]
     * @return   [type]                            [description]
     */
    public function delete($name,array $options=[]){
        if(is_array($options)&&count($options)>0){
            $this->getOptions($options);
        }
        $value=$_COOKIE[$name];
        if($value){
            if(is_array($value)){

                foreach ($value as $k => $v) {
                    # code...
                    setcookie($name.'['.$k.']','',time()-1,$this->path,$this->domain,$this->secure,$this->httponly);
                    unset($v);
                }
            }else{
                setcookie($name,'',time()-1,$this->path,$this->domain,$this->secure,$this->httponly);
                unset($value);
            }
        }
    }


}