<?php

class CommonAction extends BasicAction {

    public function _initialize() {
        parent::_initialize();
        $topnav = D('Class')->classToTree(array('class_group' => 'home'));
        $this->assign('topnav', $topnav);
    }

}