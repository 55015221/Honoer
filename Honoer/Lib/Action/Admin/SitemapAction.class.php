<?php

class SitemapAction extends BaseAction {

    public function xml() {
        $items = M('Items');
        $items_list = $items->field('id,add_time')->where('status=1 and is_del=0')->order('add_time desc')->limit('0,500')->select();

        foreach ($items_list as $key => $val) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . get_url('index', $val['id'], 'item');
            $url = str_replace('&', '&amp;', $url);
            $add_time = date('Y-m-d', $val['add_time']);
            $num = mt_rand(1, 10) / 10;
            $xml .="<url>\r\n<loc>" . $url . "</loc>\r\n<lastmod>" . $add_time . "</lastmod>\r\n<changefreq>always</changefreq>\r\n<priority>" . $num . "</priority>\r\n</url>\r\n";
        }
        $file = '<?xml version="1.0" encoding="utf-8"?>' . "\r\n<urlset>\r\n" . $xml . "</urlset>";
        file_put_contents('./Public/statics/sitemap.xml', $file);

        $this->success('成功生成sitemap.xml');
    }

    public function html() {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . C('web_path') . 'index.php?a=sitemap&m=Index&g=Home';
        $content = file_get_contents($url);
        file_put_contents('./Public/statics/sitemap.html', $content);

        $this->success('成功生成sitemap.html');
    }

}

