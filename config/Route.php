<?php
/*
路由配置,路由规则设置
*/
return [
    "<m>/<c>-<a>-<id>.asp"=>"index/main/api",

    "admin-<id>.html"=>"index/main/admin",
    "admin"=>"index/main/login",

	"<m>/<c>/<a>"=>"<m>/<c>/<a>",
    ""=>"index/main/index",
];

