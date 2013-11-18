<?php

class EmptyAction extends Action {

    public function _empty($name) {
        $message = '找不到模块:' . $name;
        $this->assign('message', $message);
        $this->notfound();
    }

    public function index($name) {
        $message = '找不到操作:' . $name;
        $this->assign('message', $message);
        $this->notfound();
    }

    private function notfound() {
        send_http_status(404);
        $this->display(THEME_PATH . '_404.html');
    }

}

?>
