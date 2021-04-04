<?php
/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/

namespace app\controller\api;

use app\lib\HttpClient\HttpClient;
use app\lib\SimpleHtmlDom\phpQuery;
use app\lib\SimpleHtmlDom\phpQueryObject;
use app\vendor\cache\Cache;


/*******************************************************************************
 * Copyright (c) 2021. CleanPHP. All Rights Reserved.
 ******************************************************************************/


/**
 * Class Yiyan
 * @package app\controller\api
 */
class News extends BaseController
{
    /**
     * @var phpQueryObject $html
     */
    private $html;

    public function init()
    {
        parent::init();

        $this->html = phpQuery::newDocumentHTML($this->getCache());
    }

    private function getCache()
    {
        $data = Cache::get("tophub_today");
        if ($data == null)
            $this->getData();
        return $data;
    }

    private function getData()
    {
        $httpReq = new HttpClient("https://tophub.today");
        $httpReq->get("https://tophub.today");
        Cache::init(3600 * 6);
        Cache::set("tophub_today", $httpReq->getBody());
    }
    //知乎热榜

    /**
     * 0 微博
     * 1 知乎
     * @return mixed|string|null
     */

    function getNews()
    {
        $c = arg("type", "1", true, "int");
        if (arg("refresh", "") == "true") {
            $this->getText($c);
        } else {
            //从缓存加载
            if (Cache::get("News_$c") == null) {
                $this->getText($c);
            }
        }

        $data = Cache::get("News_$c");

        switch (arg("depart")) {
            case "pic":
                return $data['pic'];
            case "title":
                return $data['title'];
            case "count":
                return $data['count'];
            case "urls":
                return $data["urls"][arg("index", 0, true, "int")];
            case "urlNum":
                return $data["urlNum"][arg("index", 0, true, "int")];
            case "urlTitle":
                return $data["urlTitle"][arg("index", 0, true, "int")];
            case "urlHot":
                return $data["urlHot"][arg("index", 0, true, "int")];
        }
        return $this->toString($data);
    }

    private function getText($id)
    {

        $elem = $this->html->find(".cc-cd:eq($id)");
        $html = phpQuery::newDocumentHTML($elem->html());
        $elem_pic = $html->find("img:eq(0)")->attr("src");
        $elem_title = $html->find(".cc-cd-sb-st:eq(0)")->html();
        $elem_list = $html->find(".cc-cd-cb.nano");
        $elemHtml = phpQuery::newDocumentHTML($elem_list->html());
        $aElem = $elemHtml->find("a")->attrs("href");
        $divSElem = $elemHtml->find(".s")->texts();
        $divTElem = $elemHtml->find(".t")->texts();
        $divEElem = $elemHtml->find(".e")->texts();

        //     Cache::init(3600*6);
        Cache::set("News_$id", [
            "pic" => $elem_pic,
            "title" => $elem_title,
            "count" => sizeof($aElem),
            "urls" => $aElem,
            "urlNum" => $divSElem,
            "urlTitle" => $divTElem,
            "urlHot" => $divEElem
        ]);
    }

    private function toString($data)
    {
        return "pic " . $data['pic'] . " title " . $data["title"] . " count " . $data["count"] . " urls " . implode('|', $data["urls"]) . " urlNum " . implode('|', $data["urlNum"]) . " urlTitle " . implode('|', $data["urlTitle"]) . " urlHot " . implode('|', $data["urlHot"]);
    }

    public function __destruct()
    {
        $this->html = null;
    }
}