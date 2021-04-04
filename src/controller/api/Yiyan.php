<?php
/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\api;

use app\lib\HttpClient\HttpClient;
use app\vendor\cache\Cache;
use app\vendor\debug\Log;
use Exception;


/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/


/**
 * Class Yiyan
 * @package app\controller\api
 * a    动画
 * b    漫画
 * c    游戏
 * d    文学
 * e    原创
 * f    来自网络
 * g    其他
 * h    影视
 * i    诗词
 * j    网易云
 * k    哲学
 * l    抖机灵
 */
class Yiyan extends BaseController
{
    function Hitokoto()
    {
        $c = arg("type", "");
        if (arg("refresh", "") == "true") {
            $this->getData($c);
        } else {
            //从缓存加载
            if (Cache::get("yiyan_hitokoto_$c") == null) {
                $this->getData($c);
            }
        }

        switch (arg("depart")) {
            case "hitokot":
                return Cache::get("yiyan_hitokoto_$c");
            case "from":
                return Cache::get("yiyan_from_$c");
            case "author":
                return Cache::get("yiyan_from_who_$c");
        }

        return Cache::get("yiyan_hitokoto_$c") . "|" . Cache::get("yiyan_from_$c") . "|" . Cache::get("yiyan_from_who_$c");
    }

    private function getData($c)
    {
        $httpReq = new HttpClient("https://v1.hitokoto.cn");
        $param = [
            "encode" => "json",
            "c" => $c//类型参数
        ];
        try {
            $httpReq->get("https://v1.hitokoto.cn/?" . http_build_query($param));
            Cache::init(180);
            $json = json_decode($httpReq->getBody(), true);
            // dump($json);
            if ($json == null || !isset($json["hitokoto"])) return;
            Cache::set("yiyan_hitokoto_$c", $json["hitokoto"]);
            Cache::set("yiyan_from_$c", $json["from"]);
            Cache::set("yiyan_from_who_$c", $json["from_who"] == "null" ? $json["creator"] : $json["from_who"]);
        } catch (Exception $e) {
            Log::info("apiHttp", "站点响应太慢。");
        }
    }


}