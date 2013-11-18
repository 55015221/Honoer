<?php

class ArticleModel extends RelationModel {

    protected $fields = array('article_id', 'class_id', 'article_title', 'article_intro', 'article_content', 'user_id', 'create_time',
        'article_view', 'article_comment', 'article_picpath', 'article_description', 'article_keywords',
        '_pk' => 'article_id', '_autoinc' => true);
    protected $_link = array(
        'Comment' => array(
            'mapping_type' => HAS_MANY,
            'mapping_name' => '_comment',
            'class_name' => '_comment',
            'foreign_key' => 'article_id',
            'mapping_order' => 'create_time DESC',
        ),
        'User' => array(
            'mapping_type' => BELONGS_TO,
            'mapping_name' => '_user',
            'class_name' => '_user',
            'foreign_key' => 'user_id',
            'as_fields' => 'user_nickname',
        ),
        'Class' => array(
            'mapping_type' => BELONGS_TO,
            'mapping_name' => '_class',
            'class_name' => '_class',
            'foreign_key' => 'class_id',
            'as_fields' => 'class_name',
        ),
    );

    public function getList($where = null, &$pages = false) {

        if (false !== $pages) {
            import('@.ORG.Page');
            $count = $this->where($where)->count();
            if ($_GET[C('VAR_PAGE')] * C('PAGESIZE') > $count) {
                return null;
            }
            $Page = new Page($count, C('PAGESIZE'));
            $pages = $Page->show();
            $this->limit($Page->firstRow . ',' . $Page->listRows);
        }
        return $this->relation(true)->where($where)->order(array('create_time' => 'DESC'))->select();
    }

    function getDetail($where, $order = null) {
        if ($where === null)
            return false;
        is_numeric($where) && $where = array('article_id' => $where);
        $order !== null && $this->order($order);
        return $this->relation(true)->where($where)->find();
    }

    public function getSeven($type, $num = 8) {
        switch ($type) {
            case 'top':
                $this->order(array('article_view' => 'DESC'));
                $this->limit($num);
                break;
            case 'new':
                $this->order(array('create_time' => 'DESC'));
                $this->limit($num);
                break;
            default :
                break;
        }
        return $this->relation(true)->select();
    }

    public function saveArticel($args) {
        unset($args['article_picpath']); //删除表单传过来的值

        if (empty($args['article_id'])) {
            $args['create_time'] = date('Y-m-d H:i:s', time());
        }

        $args['user_id'] = 1;
        $upload = new UploadAction();
        $file = $upload->uploadFiles();
        if ($file) {
            $args['article_picpath'] = ltrim($file['savepath'], '.') . 'thumb_' . $file['savename'];
        }
        $args['article_title'] = strip_tags($args['article_title']);
        empty($args['article_intro']) && $args['article_intro'] = cn_substr(del_html_tags($args['article_content']), 180);
        if ($this->create($args)) {
            $result = (isset($args['article_id']) && !empty($args['article_id'])) ? $this->save() : $this->add();
            if ($result) {
                return true;
            }
        }
        return $this->getError();
    }

    public function getClassByDate($format = '%Y-%m') {
        $format = "DATE_FORMAT(`create_time`,'{$format}')";
        $this->field(array("COUNT(*)" => 'total', $format => 'date'));
        $this->where(array());
        $this->order(array('date' => 'DESC'));
        $this->group("date");
        return $this->getList();
    }

}

?>
