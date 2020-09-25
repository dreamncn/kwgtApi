<?php
/**
 * File Response.php
 * Author : Dreamn
 * Date : 7/30/2020 12:49 AM
 * Description:响应类
 */

namespace app\vendor\lib\Web;
class Response
{
    /**
     * 获得完整域名（包含协议）
     * @return string
     */
    public static function getAddress()
    {
        return $GLOBALS['http_scheme'] . $_SERVER["HTTP_HOST"];
    }

    /**
     * 获取本机IP
     * @return string
     */
    public static function getMyIp()
    {
        return gethostbyname(gethostname());
    }

    public static function location($url){
        header("Location:{$url}");
    }

    public static function msg($err=false,$code=404,$title="",$msg="",$time=3,$url='',$desc="立即跳转"){
        header("Content-type: text/html; charset=utf-8", true, $code);
        $err=$err?":(":":)";
        if($time==0){
            self::location($url);
            return;
        }
        $script='';
        if($url=='')$msg2='';
        elseif($time!=-1){
            $msg2="还有<span id='jump'>{$time}</span>秒为您自动跳转，<a href='{$url}' target='_self'>{$desc}</a>";
            $script= <<<EOF
<script>let wait={$time};setInterval(function() {document.getElementById("jump").innerText=--wait;if(wait<=0){location.href="{$url}";}}, 1000);</script>
EOF;

        }else{

            $msg2="<span id='jump'><a href='{$url}' target='_self'>{$desc}</a></span>";
        }
        echo <<<EOF
<html lang="zh-cn"><head><meta charset="utf-8"><style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff;  color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><title>{$title}</title></head><body><div style="padding: 24px 48px;"> <h1>{$err}</h1><p><span style="font-size:32px;">$title</span></p><p><span style="font-size:25px;">$msg</span></p><span style="font-size:25px;">$msg2</span></div>{$script}</body></html>
EOF;
        exit;
    }
}
