<?php

class HtmlAction extends CommonAction {

    public function index() {
        echo 'Html/index';
    }

    //生成首页静态页面
    public function home() {
        header("Content-Type:text/html; charset=UTF-8");
        ob_end_flush();
        ob_implicit_flush(true);
        echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">开始生成</span><br />';
        $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Home/Index/index');
        $content = file_get_contents($url);
        $htmlfile = $this->build('index', null, $content);
        $htmlfile = ltrim($htmlfile, './');
        echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成首页：' . $htmlfile . '</span><br />';
        echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';
        //$this->redirect('Html:index');
    }

    //生成文章页静态页面
    public function article() {
        set_time_limit(0);
        header("Content-Type:text/html; charset=UTF-8");
        ob_end_flush();
        ob_implicit_flush(true);
        echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">开始生成</span><br />';
        $file = '<script>document.body.scrollTop = document.body.scrollHeight;</script>';
        $Article = M('Article');
//        $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Home/Article/index');
//        $htmlfile = $this->gain_content($url, 0, C('TMPL_TEMPLATE_SUFFIX'), C('PAGESIZE'));
//        $htmlfile = str_replace('./' . C('home.url_dir_articleCate') . '/', '', $htmlfile);
//        echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成文章分类页：' . $htmlfile . '</span><br />' . $file;
        $pages = $Article->field('article_id')->where()->count();
        $num = ceil($pages / C('PAGESIZE'));
        for ($p = 1; $p <= $num; $p++) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Home/Article/index', array('p' => $p));
            $htmlfile = $this->gain_content($url, $p, HTML_PATH . 'Article', C('PAGESIZE'));
            $htmlfile = str_replace('./Article', '', $htmlfile);
            echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成文章分类页：' . $htmlfile . '</span><br />' . $file;
        }
        unset($result);
        $result = $Article->field('article_id')->where()->select();
        foreach ($result as $key => $val) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . U('Home/Article/read', array('aid' => $val['article_id']));
            $htmlfile = $this->gain_content($url, $val['article_id'], HTML_PATH . 'Article-read');
            $htmlfile = str_replace('./Article/', '', $htmlfile);
            echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">生成文章页：' . $htmlfile . '</span><br />' . $file;
        }
        echo '<span style="color:#0099CC;font-size:14px;line-height:20px;">任务完成</span>';
        //$this->redirect('Html:index');
    }

    //生成静态页类
    private function build($htmlfile = '', $htmlpath = '', $content) {
        $htmlpath = !empty($htmlpath) ? $htmlpath : HTML_PATH;
        $htmlfile = $htmlpath . '-' . $htmlfile . C('TMPL_TEMPLATE_SUFFIX');
        if (!is_dir(dirname($htmlfile)))
        // 如果静态目录不存在 则创建
            mk_dir(dirname($htmlfile));
        if (false === file_put_contents($htmlfile, $content))
            throw_exception(L('_CACHE_WRITE_ERROR_') . ':' . $htmlfile);
        return $htmlfile;
    }

    //获取静态页内容并修改分页
    private function gain_content($url, $id, $dir, $limit) {
        $content = file_get_contents($url);
        $nums = $this->match_nums($content);
        $totalRows = $nums[1];
        $totalPages = $nums[3];
        //如果没有内容,直接生成
        if ($totalPages == '') {
            return $this->build($id, './' . $dir, $content);
        }
        //引入自定义分页类
        import("@.ORG.Page");
        $message = '';
        for ($i = 1; $i <= $totalPages; $i++) {
            $url = $url . '&p=' . $i;
            $content = file_get_contents($url);
            $page = new page($limit, $totalRows, $i, 5, './', $id);
            $pages = $page->subPageCss();
            $content = preg_replace('/<div class="page">(.*)<\/div>/', '<div class="page">' . $pages . '</div>', $content);

            if ($i == 1) {
                $str = $this->build($id, './' . $dir . '/', $content);
                $message .= $str . "\r\n";
            } else {
                $str = $this->build($id . '-' . $i, './' . $dir . '/', $content);
                $message .= $str . "\r\n";
            }
        }
        return $message;
    }

    //正则获取商品总数
    private function match_nums($content) {
        preg_match('/<span class=\"totalRows\">(.*?)<\/span> 条记录 <span class=\"nowPage\">(.*?)<\/span>\/(.*?) 页/', $content, $result);
        isset($result) ? $nums = $result : $nums = '';
        return $nums;
    }

}

?>
