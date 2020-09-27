<?php
/*
路由配置,路由规则设置
*/
return [
    "index/main-api-<id>.asp"=>"index/main/api",

    "admin-<id>.html"=>"index/main/admin",
    "<file>.php"=>"index/main/test",
    ""=>"index/main/index",
    "dontgo/<c>/<a>"=>"admin/<c>/<a>",
	"<m>/<c>/<a>"=>"<m>/<c>/<a>",

];

