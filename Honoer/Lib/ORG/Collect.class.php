<?php

/**
 * 图片采集类
 */
class Collect {

    public $pattern = null;
    public $savepath = null;
    public $timeout = null;

    public function __construct($options = null) {
        $this->savepath = !empty($options['savepath']) ? $options['savepath'] : './Public/Collect';
        $this->pattern = !empty($options['pattern']) ? $options['pattern'] : "/(?:http?|https?):\/\/(?:[^\.\/\(\)\?]+)\.(?:[^\.\/]+)\.(?:com|cn|net|org)\/(?:[^\.:\"\'\(\)\?]+)\.(jpg|png|gif)/i";
        $this->timeout = !empty($options['savepath']) ? $options['savepath'] : '100';
    }

    /**
     * Curl 多线程
     * @param array $urls 并行网址 
     * @param int $timeout 超时时间
     * @return mix 
     */
    public function Curl_http($urls) {
        $data = array();

        $chs = curl_multi_init(); //创建多个curl语柄

        foreach ($urls as $key => $url) {
            $ch[$key] = curl_init($url); //初始化

            curl_setopt($ch[$key], CURLOPT_TIMEOUT, $this->timeout); //设置超时时间
            curl_setopt($ch[$key], CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($ch[$key], CURLOPT_MAXREDIRS, 7); //HTTP定向级别 ，7最高
            curl_setopt($ch[$key], CURLOPT_HEADER, false); //这里不要header，加快效率
            curl_setopt($ch[$key], CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
            curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上		        
            curl_setopt($ch[$key], CURLOPT_HTTPGET, true);

            curl_multi_add_handle($chs, $ch[$key]);
        }

        //防止死循环耗死cpu 这段是根据网上的写法,php手册上有介绍
        $active = null;
        do {
            $mrc = curl_multi_exec($chs, $active); //当无数据，active=true
        } while ($mrc == CURLM_CALL_MULTI_PERFORM); //当正在接受数据时
        while ($active and $mrc == CURLM_OK) {//当无数据时或请求暂停时，active=true
            if (curl_multi_select($chs) != -1) {
                do {
                    $mrc = curl_multi_exec($chs, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        foreach ($urls as $key => $url) {
            if (!curl_errno($ch[$key])) {
                $data[$key] = curl_multi_getcontent($ch[$key]); //数据转换为array
                $header[$key] = curl_getinfo($ch[$key]); //返回http头信息
                curl_close($ch[$key]); //关闭语柄
                curl_multi_remove_handle($chs, $ch[$key]);   //释放资源 
            } else {
                unset($key, $url);
            }
        }

        curl_multi_close($chs);

        return $data;
    }

    public function getImages(array $urls) {
        ignore_user_abort();
        set_time_limit(0);
        $data = $this->Curl_http($urls, $this->timeout); //获取列表数据

        if (!file_exists($this->savepath)) {
            mkdir($this->savepath, 0777);
        }

        $path = date('Ymd', time());
        if (!file_exists($this->savepath . '/' . $path)) {
            mkdir($this->savepath . '/' . $path, 0777);
        }
        $matches = array();
        $result = array();
        foreach ((array) $data as $key => $value) {
            preg_match_all($this->pattern, $value, $matches[$key]);
            if (count($matches[$key][0]) > 0) {
                $image_data = $this->Curl_http($matches[$key][0], $this->timeout); //全部图片数据二进制
                foreach ((array) $image_data as $k => $val) {
                    if (!empty($val) && (strlen($val) > 1024 * 20)) {
                        $rand = rand(1000, 9999);
                        $basename = time() . "_" . $rand . "." . $matches[$key][1][$k]; //保存为jpg格式的文件
                        $filename = $this->savepath . "/" . $path . "/" . "$basename";
                        file_put_contents($filename, $val);
                        $result[] = $path . "/" . "$basename";
                    } else {
                        unset($k, $val);
                    }
                }
            } else {
                unset($matches);
            }
        }
        return $result;
    }

}