<?php

class BelleModel extends Model {

    protected $fields = array('belle_id', 'belle_name', 'belle_path', 'create_time',
        '_pk' => 'belle_id', '_autoinc' => true);

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
        is_numeric($where) && $where = array('belle_id' => $where);
        return $this->field(true)
                        ->where($where)
                        ->find();
    }

    public function addImages($urls) {
        if (empty($urls)) {
            return false;
        }
        import("@.ORG.Collect");
        $collect = new Collect();
        $files = $collect->getImages($urls);
	$create_time = date('Y-m-d H:i:s', time());
	$total =0;
        foreach ($files as $value) {
            $arr = explode('/', $value);
            $name = array_pop($arr);
	    $values .= '(" ","' . $name . '","' . $value . '","' . $create_time . '"),';
	    $total+=1;
        }
        $str = rtrim($values, ',');
        $sql = "INSERT INTO " . C('DB_PREFIX') . "belle VALUES" . $str . ";";
        $model = new Model();
	$model->execute($sql);
	return $total;
    }

}

?>
