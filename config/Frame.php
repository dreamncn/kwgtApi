<?php
/*
*框架配置
**/
return [
	"host"=>["localhost:8080"],//网站域名
	"crossDomain"=>[],//允许跨域的域名
	"debug"=>true,//是否为调试模式
	"architecture"=>"mvc",//框架开发模式，具有两种，MVC/Sperate,
    "error" => 'error',//非调试状态出错显示的信息
    "protect"=>[
        "csrf"=>true,//是否开启CSRF防御，使用分离式开发模式，该选项无效，需要防御请自行添加CSRF API,
         "password"=>true,//开启密码安全防御，避免被暴力破解
    ]

];

