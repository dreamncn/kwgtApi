<?php
/*******************************************************************************
 * Copyright (c) 2020. CleanPHP. All Rights Reserved.
 ******************************************************************************/

use app\vendor\debug\codeCheck;


function help()
{
    echo <<<EOF
Usage: php clean.php [options] 

Options:
  check                     Check your code and give you some suggestions for improvement.
  release                   Publish your code, which will output your code as a compressed package to the release folder, and remove some unnecessary release content.
  run [index/main/index]    Run cleanphp in command line mode.
EOF;
    return null;
}

function run($argv)
{
    if (!isset($argv[2])) return help();
    $_SERVER['CLEAN_CONSOLE'] = true;
    $_SERVER["HTTP_HOST"] = "localhost";
    $_SERVER["REQUEST_URI"] = "/" . $argv[2];
    include './public/index.php';
    return null;
}

function release()
{

}

function check()
{
    codeCheck::run();
}

if (!isset($argv[1]))
    return help();

switch ($argv[1]) {
    case "check":
        check();
        break;
    case "release":
        release();
        break;
    case "run":
        run($argv);
        break;
    default:
        help();
}






