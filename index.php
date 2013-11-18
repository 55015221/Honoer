<?php

header('Content-Type:text/html;charset=utf-8');
//定义项目路径
define('THINK_PATH', './ThinkPHP3.0/');
define('APP_NAME', 'Honoer');
define('APP_PATH', './Honoer/');
define('APP_DEBUG', true);
define('UPLOAD_PATH', 'Public/Upload/'); //路径前面不能加./
define('HTML_PATH', './Html/');

//加载并运行ThinkPHP
require( THINK_PATH . "ThinkPHP.php");
?>
