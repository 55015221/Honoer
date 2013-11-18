<?php

class CommentModel extends RelationModel {

    protected $fields = array('comment_id', 'article_id', 'comment_title', 'comment_content', 'create_time', 'create_ip', 'comment_email',
        '_pk' => 'comment_id', '_autoinc' => true);

    public function getList($where = null, &$pages = false) {
        if (false !== $pages) {
            import('@.ORG.Page');
            $count = $this->where($where)->count();
            $Page = new Page($count, C('PAGESIZE'));
            $pages = $Page->show();
            $this->limit($Page->firstRow . ',' . $Page->listRows);
        }
        return $this->field(true)
                        ->where($where)
                        ->select();
    }

    public function getDetail($where) {
        is_numeric($where) && $where = array('comment_id' => $where);
        return $this->field(true)
                        ->where($where)
                        ->find();
    }

    public function addComment($args) {
        $args['comment_content']= htmlspecialchars($args['comment_content']);
        $args['create_ip'] = get_client_ip();
        $args['create_time'] = timer();
        if ($this->create($args)) {
            if ($this->add()) {
                return true;
            } else {
                return $this->getError();
            }
        }
    }

}

?>
