<?php

class IndexAction extends CommonAction {

    public function index() {
        $Article = D('Article');
        C('PAGESIZE',10);
        $data = $Article->getList($where, $pages);
        $this->assign('data', $data);
        $this->display();
    }

    public function read() {
        $page = $_GET['page'];

        $Belle = D('Belle');
        $Belle->page("$page,10");
        $data = $Belle->getList($where);
        $this->assign('data', $data);
        $this->assign('page', $page);
        $this->display('Index:index');
    }

}