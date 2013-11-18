<?php

class ArticleAction extends CommonAction {

    private $_model = null;

    public function __construct() {
        parent::__construct();
        $this->_model = D('Article');
    }

    public function index() {
        $cid = $_GET['cid'];
        $keyword = $_GET['keyword'];
        $date = $_GET['date'];
        if (isset($keyword) && !empty($keyword)) {
            $where = array(
                'article_title' => array('like', "%{$keyword}%"),
                'article_content' => array('like', "%{$keyword}%"),
                '_logic' => 'OR',
            );
        }
        if (isset($date) && !empty($date)) {
            $where = array('create_time' => array('like', "%{$date}%"));
        }
        !empty($cid) && $where = array('class_id' => $cid);
        $data = $this->_model->getList($where, $pages);
        $this->assign('data', $data);
        $this->assign('page', $pages);
        $this->right();
        $this->display();
    }

    public function read() {
        $aid = $_GET['aid'];
        $data = $this->_model->relation(true)->getDetail($aid);
        $prev = $this->_model->getDetail(array('article_id' => array('lt', $aid)), array('article_id' => 'DESC'));
        $next = $this->_model->getDetail(array('article_id' => array('gt', $aid)), array('article_id' => 'ASC'));
        $this->_model->where(array('article_id' => $aid))->setInc('article_view', 1);
        $this->assign('prev', $prev);
        $this->assign('next', $next);
        $this->assign('data', $data);
        //设置SEO信息
        $this->assign('seo', setseo(array($data['article_title'], $data['article_intro'], $data['article_keyword'])));
        $this->right();
        $this->display();
    }

    public function right() {
        //热门文章 根据点击次数去7条
        $hot = $this->_model->getSeven('top');
        $new = $this->_model->getSeven('new');
        $dataClass = $this->_model->getClassByDate();
        $this->assign('dataClass', $dataClass);
        $this->assign('hot', $hot);
        $this->assign('new', $new);
    }

    public function search() {
        $keyword = $_GET['keyword'];
        $where = array(
            'article_title' => array('like', "%{$keyword}%"),
            'article_content' => array('like', "%{$keyword}%"),
            '_logic' => 'OR',
        );
        $data = $this->_model->getList($where, $pages);
        $this->assign('data', $data);
        $this->assign('page', $pages);
        $this->display('index');
    }

    public function reply() {
        $args = array_merge(array('article_id' => $_GET['aid']), $_POST);
        $result = D('Comment')->addComment($args);
        $this->ajaxReturn($result, '留言成功！', $result);
    }

    public function masonry() {
        $data = $this->_model->getList($where, $pages);
        foreach ($data as $k => $val) {
            $data[$k]['url'] = U('Article/read', array('aid' => $val['article_id']));
        }
        $this->ajaxReturn(array('data' => $data, 'pages' => $pages), '成功~', 1);
    }

}