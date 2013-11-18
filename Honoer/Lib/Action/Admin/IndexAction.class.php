<?php

class IndexAction extends CommonAction {

    //系统环境信息
    public function index() {

        if (function_exists('gd_info')) {
            $gd = gd_info();
            $gd_version = $gd['GD Version'];
        } else {
            $gd_version = "不支持";
        }
        $webinfo = array(
            '操作系统' => PHP_OS,
            '运行环境' => $_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式' => php_sapi_name(),
            '主机名IP端口' => $_SERVER['SERVER_NAME'] . ' (' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . ')',
            '程序目录' => __ROOT__ . '/',
            '最大上传限制' => ini_get('upload_max_filesize'),
            '最大执行时间' => ini_get('max_execution_time') . '秒',
            '剩余空间' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
            'PHP运行方式' => php_sapi_name(),
            'MYSQL版本' => function_exists("mysql_close") ? mysql_get_client_info() : '不支持',
            'GD库版本' => $gd_version,
            'MYSQL版本' => mysql_get_server_info(),
            '北京时间' => gmdate("Y年n月j日 H:i:s", time() + 8 * 3600),
            '采集函数检测' => ini_get('allow_url_fopen') ? '支持' : '不支持',
            'CURL扩展' => function_exists('curl_init') ? '支持' : '不支持',
            'register_globals' => get_cfg_var("register_globals") == "1" ? "ON" : "OFF",
            'magic_quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'magic_quotes_runtime' => (1 === get_magic_quotes_runtime()) ? 'YES' : 'NO',
            'Runtime目录是否可写' => is_writable(RUNTIME_PATH) ? '支持' : '不支持',
            '配置文件是否可写' => is_writable(CONF_PATH) ? '支持' : '不支持',
            '上传目录是否可写' => is_writable(UPLOAD_PATH) ? '支持' : '不支持',
        );
        $this->assign('webinfo', $webinfo);
        $this->display();
    }

    public function seting() {
        if (!empty($_POST)) {
            $_POST['WEB_STATISTIC']=stripslashes($_POST['WEB_STATISTIC']);
            $_POST['WEB_SHARE']=stripslashes($_POST['WEB_SHARE']);
            $oldConfig = require CONF_PATH . '/config.php';
            $config = array_merge($oldConfig, $_POST);
            arr2file(CONF_PATH . '/config.php', $config);
            if (updateCache()) {
                $this->ajaxReturn(null, '修改成功！', 1);
            }
        } else {
            $this->display();
        }
    }

}