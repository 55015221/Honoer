<?php

class ArticleAction extends CommonAction {

    public function index() {
        C('PAGESIZE',20);
        $data = D('Article')->getList(null, $pages);
        $data = sub_content($data, 'article_content', 120);
        $this->assign('data', $data);
        $this->assign('page', $pages);
        //热门文章 根据点击次数�?�?
        $hot = D('Article')->getSeven('top');
        $new = D('Article')->getSeven('new');
        $this->assign('hot', $hot);
        $this->assign('new', $new);
        $this->display();
    }

    public function add() {
        self::category();
        $this->display('Article:edit');
    }

    public function edit() {
        $aid = $this->_get('aid');
        $data = D('Article')->relation(true)->getDetail($aid);
        $this->assign('data', $data);
        self::category();
        $this->display();
    }

    private function category() {
        $class = D('Class')->getList(array('class_pid' => 5, 'class_group' => 'home'));
        $this->assign('class', $class);
    }

    public function addsave() {
        if (!empty($_POST)) {
            if ($result = D('Article')->saveArticel($_POST)) {
                $this->ajaxReturn(null, '操作成功！', 1);
            } else {
                $this->ajaxReturn(null, $result, 0);
            }
        }
        return false;
    }

    public function del(){
	$aid = $this->_get('aid');
	if($result = M('Article')->where(array('article_id'=>$aid))->delete()){
		$this->ajaxReturn(null, '操作成功！', 1);
	}else{
		$this->ajaxReturn(null, $result, 0);
	}


    }

}
