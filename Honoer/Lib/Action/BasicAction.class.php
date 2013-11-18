<?php

class BasicAction extends Action {

    public function _empty($name) {
        $empty = new EmptyAction();
        $empty->index(ACTION_NAME);
        exit;
    }

    public function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        if (isset($_SESSION['access_token']) && !empty($_SESSION['access_token'])) {
            $get_user_info = get_user_info();
            $get_info = get_info();
            $this->assign('get_user_info', $get_user_info);
            $this->assign('get_info', $get_info);
        }
    }

    public function img() {
        $filename =  $_GET['src'];
        if(file_exists($filename)){
            import('@.ORG.Timthumb');
            return;
        }
    }

}

?>
