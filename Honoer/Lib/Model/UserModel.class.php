<?php

class UserModel extends RelationModel {

    protected $fields = array('user_id', 'user_account', 'user_nickname', 'user_password',
        '_pk' => 'user_id', '_autoinc' => true);

    public function getDetail($uid) {
        return $this->field(true)->find($uid);
    }

}

?>
