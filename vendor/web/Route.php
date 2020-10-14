<?php
namespace app\vendor\web;
use app\vendor\debug\Error;
use app\vendor\cache\Cache;
use app\vendor\debug\Log;

/**
 * Class Route
 * @package app\vendor\lib
 * @note URL路由类
 */
class Route{
    const Post = 3;
    const Get = 1;
    const Cookie = 2;

    const Native  = 1; //原生的Url形式,指从index.php，比如c=blog&a=read&m=index&id=3
    const Diy  = 2;
                        /*
                         * 经过urlRoute后的Url,指的是:
                         * /index/blog/read/3
                         * /index/blog/read/3.html
                         * /index/blog-read-3.asp
                         * /index/blog/read?id=3
                         * /index/blog/read?refer=/a/b/2.html
                         * */
    /**
     * @param $m
     * @param $c

     * @param $a
     * @param array $params
     * @return mixed
     */
    public static function url($m,$c,$a,$params=[]){
        $paramsStr = empty($params) ? '' : '?' . http_build_query($params);
        $route = "$m/$c/$a";
        $url = Response::getAddress() . "/";
        $default=$url.$route.$paramsStr;
        Cache::init(365 * 24 * 60 * 60, APP_ROUTE);
        //初始化路由缓存，不区分大小写
        $data=null;
        if(!isDebug())
            $data = Cache::get('route_'.$default);
        if($data!==null){
            Log::debug('route','Find Rewrite Cache: ' . $default . ' => ' . $data);
            return  $data;
        }



        $arr=str_replace("<m>",$m,$GLOBALS['route']);
        $arr=str_replace("<c>",$c,$arr);
        $arr=str_replace("<a>",$a,$arr);
        $arr= array_flip(array_unique($arr));


        $route_find=$route;
        if(isset($arr[$route])){
            Log::debug('route','Find Rule: ' . $arr[$route] );
            //处理参数部分
            $route_find=$arr[$route];
            $route_find=str_replace("<m>",$m,$route_find);
            $route_find=str_replace("<c>",$c,$route_find);
            $route_find=str_replace("<a>",$a,$route_find);
            foreach ($params as $key=>$val){
                if(strpos($route_find,"<$key>")!==false){
                    $route_find=str_replace("<$key>",$val,$route_find);
                    unset($params[$key]);
                }

            }
        }


        Log::debug('route','Replace Rule: ' . $route_find );

        if($route_find==$route||strpos($route_find,'<')!==false){
            $retUrl=$default;
        }else{
            $paramsStr = empty($params) ? '' : '?' . http_build_query($params);
            $retUrl=$url.$route_find.$paramsStr;
        }
        if(!isDebug())
            Cache::set('route_'.$default,$retUrl);

        return $retUrl;

    }

    public static function rewrite(){
        //不允许的参数
        if(isset($_REQUEST['m'])||isset($_REQUEST['a'])||isset($_REQUEST['c'])){
            Error::_err_router("The following parameters are not allowed：m,a,c!");
        }

        $url = strtolower(urldecode($_SERVER['REQUEST_URI']));
        $data = null;
        if (!isDebug()) {//非调试状态从缓存读取
            Cache::init(365 * 24 * 60 * 60, APP_ROUTE);
            //初始化路由缓存，不区分大小写
            $data = Cache::get($url);
        }
        Log::debug("route","--------------------------------");
        if ($data !== null && isset($data['real']) && isset($data['route'])) {
            Log::debug('route','Find Rewrite Cache: ' . $url . ' => ' . $data['real']);
            $route_arr_cp = $data['route'];

        } else {
            Log::debug('route','Not Find Rewrite Cache: ' . $url);
            $route_arr = self::convertUrl();

            Log::debug("route","-> Match Rules:".print_r($route_arr,true));



            if(!isset($route_arr['m'])||!isset($route_arr['a'])||!isset($route_arr['c'])){
                Error::_err_router("Error Route! We need at least three parameters.");
            }

            $route_arr = array_merge($_GET , $route_arr);//get中的参数直接覆盖

            $route_arr_cp = $route_arr;

            //重写缓存表
            $__module = ($route_arr['m']);
            unset($route_arr['m']);

            $__controller = ($route_arr['c']);
            unset($route_arr['c']);

            $__action = ($route_arr['a']);
            unset($route_arr['a']);

            if(url($__module,$__controller,$__action,$route_arr)!==Response::getNowAddress()){
                Error::_err_router("Error Route! A defined route cannot be accessed directly.\nThis Address:".Response::getNowAddress().'  Regular Address:'.url($__module,$__controller,$__action,$route_arr));
            }

            $real = "$__module/$__controller/$__action";
            if (sizeof($route_arr)) {
                $real .= '?' . http_build_query($route_arr);
            }
            $arr = [
                'real' => $real,
                'route' => $route_arr_cp
            ];
            if(!isDebug())
                Cache::set($url, $arr);
            Log::debug('route','Rewrite Cache: ' . $real);
        }

        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE, $route_arr_cp);

        GLOBAL $__module, $__controller, $__action;
        $__module = $_REQUEST['m'];
        $__controller = $_REQUEST['c'];
        $__action = $_REQUEST['a'];
    }
    public static function convertUrl(){
        $route_arr=[];

        $url=strtolower($GLOBALS['http_scheme'].$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);


        Log::debug("route","The original Url:$url");
        if(strpos($url,'?')!==false){
            $url=substr($url,0,strpos($url,'?'));
        }
        Log::debug("route","The original Url Without Params:$url");
        foreach ($GLOBALS['route'] as $rule => $mapper) {
            $rule = Response::getAddress() . '/' . $rule;



            Log::debug("route","-> Url Rule:$rule");
            $rule=strtolower($rule);
            $rule = '/' . str_ireplace(
                    array('\\\\',$GLOBALS['http_scheme'], '/', '<', '>', '.'),
                    array('', '', '\/', '(?P<', '>[\x{4e00}-\x{9fa5}a-zA-Z0-9_\.-]+)', '\.'), $rule) . '$/u';




            if (preg_match($rule, $url, $matchs)) {
                $route = explode("/", $mapper);
                if (isset($route[2])) {
                    list($route_arr['m'], $route_arr['c'], $route_arr['a']) = $route;
                }
                foreach ($matchs as $matchkey => $matchval) {
                    if (!is_int($matchkey)) $route_arr[$matchkey] = $matchval;
                }
                break;
            }

        }

        return $route_arr;
    }
}





