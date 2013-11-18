<?php

class BelleAction extends CommonAction {

    //系统环境信息
    public function index() {


        $this->display();
    }

    public function belle() {
	  

        $urls = array(
            'http://pic.sogou.com/d?query=%D0%D4%B8%D0%C3%C0%C5%AE&mood=0&picformat=0&mode=1&di=2&w=05009900&dr=1&did=56#did57',
            'http://pic.sogou.com/d?query=%D0%D4%B8%D0%C3%C0%C5%AE&mood=0&picformat=0&mode=1&di=2&w=05009900&dr=1&_asf=pic.sogou.com&_ast=1371273219&did=86',
            'http://pic.sogou.com/d?query=%D0%D4%B8%D0%C3%C0%C5%AE&mood=0&picformat=0&mode=1&di=2&w=05009900&dr=1&_asf=pic.sogou.com&_ast=1371273219&did=205',
            'http://pic.sogou.com/d?query=%D0%D4%B8%D0%C3%C0%C5%AE&mood=0&picformat=0&mode=1&di=2&w=05009900&dr=1&did=211#did211',
            'http://pic.sogou.com/d?query=%D0%D4%B8%D0%C3%C0%C5%AE&mood=0&picformat=0&mode=1&di=2&w=05009900&dr=1&_asf=pic.sogou.com&_ast=1371273219&did=228',
            );
        $Belle = D('Belle');

        $result = $Belle->addImages($urls);
        $this->ajaxReturn($result, '操作成功！', 1);
    }

}
