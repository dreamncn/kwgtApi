<?php
/*
数据库配置
*/
return [
	"master"=>[
	    "type"=>"mysql",
		"host"=>"localhost",//数据库地址
		"username"=>"root",//数据库用户名
		"password"=>"toor",//数据库密码
        "port"=>"3306",
		"db"=>"test",//数据库的库名
		"charset"=>"utf-8"//数据库编码
	]
    ,"sqlite"=>[
	    "type"=>"sqlite3",
        "host"=>APP_DIR.DS."storage".DS."sql".DS."1.db",//数据库地址
        "username"=>"",//数据库用户名
        "password"=>"",//数据库密码
        "port"=>"",
        "db"=>"",//数据库的库名
        "charset"=>"utf-8"//数据库编码
    ],
];

