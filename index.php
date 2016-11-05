<?php

if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');
//开启调试模式，开发阶段开启，部署时关闭
define('APP_DEBUG', True);
//定义应用目录
define('APP_PATH', './CES/');
define('APP_NAME', './CES');
//引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';
