<?php

header('Content-Type: text/html; charset=utf-8');
set_time_limit(0);

/**
 * curl 多线程 
 *  
 * @param array $array 并行网址 
 * @param int $timeout 超时时间
 * @return mix 
 */
function Curl_http($array, $timeout = '15') {
    $res = array();

    $mh = curl_multi_init(); //创建多个curl语柄

    foreach ($array as $k => $url) {
        $conn[$k] = curl_init($url); //初始化

        curl_setopt($conn[$k], CURLOPT_TIMEOUT, $timeout); //设置超时时间
        curl_setopt($conn[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($conn[$k], CURLOPT_MAXREDIRS, 7); //HTTP定向级别 ，7最高
        curl_setopt($conn[$k], CURLOPT_HEADER, false); //这里不要header，加快效率
        curl_setopt($conn[$k], CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($conn[$k], CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上		        
        curl_setopt($conn[$k], CURLOPT_HTTPGET, true);

        curl_multi_add_handle($mh, $conn[$k]);
    }
    //防止死循环耗死cpu 这段是根据网上的写法
    do {
        $mrc = curl_multi_exec($mh, $active); //当无数据，active=true
    } while ($mrc == CURLM_CALL_MULTI_PERFORM); //当正在接受数据时
    while ($active and $mrc == CURLM_OK) {//当无数据时或请求暂停时，active=true
        if (curl_multi_select($mh) != -1) {
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }

    foreach ($array as $k => $url) {
        if (!curl_errno($conn[$k])) {
            $data[$k] = curl_multi_getcontent($conn[$k]); //数据转换为array
            $header[$k] = curl_getinfo($conn[$k]); //返回http头信息
            curl_close($conn[$k]); //关闭语柄
            curl_multi_remove_handle($mh, $conn[$k]);   //释放资源 
        } else {
            unset($k, $url);
        }
    }

    curl_multi_close($mh);

    return $data;
}

//url ='http://image.baidu.com/i?tn=listjson&word=liulan&oe=utf-8&ie=utf8&tag1=%E6%91%84%E5%BD%B1&tag2=%E5%85%A8%E9%83%A8&sorttype=0&pn='+str(p*i)+'&rn=60&requestType=1&'+str(random.random())
//搜狗美女
$urlarray = array(
    'http://www.xuany.cn/a/hot/20124778_7.html',
);
$data = Curl_http($urlarray, '100'); //列表数据
if (!file_exists('./img/')) {
    mkdir('./img/', 0777);
}
$path = './img/' . date('Ymd', time());
if (!file_exists($path)) {
    mkdir($path, 0777);
}
$matches = array();
foreach ((array) $data as $key => $value) {
    preg_match_all("/(?:http?|https?):\/\/(?:[^\.\/\(\)\?]+)\.(?:[^\.\/]+)\.(?:com|cn|net|org)\/(?:[^\.:\"\'\(\)\?]+)\.(jpg|png|gif)/i", $value, $matches[$key]);
    if (count($matches[$key][0]) > 0) {
        $image_data = Curl_http($matches[$key][0], '100'); //全部图片数据二进制
        $j = 0;
        foreach ((array) $image_data as $k => $val) {
            if ($val != '') {
                $rand = rand(1000, 9999);
                $basename = time() . "_" . $rand . "." . $matches[$key][1][$k]; //保存为jpg格式的文件
                $filename = $path . "/" . "$basename";
                file_put_contents($filename, $val);
                $j++;
                if (filesize($filename) < 1024 * 20) {
                    unlink($filename);
                }
                echo $j . "/" . $filename . " Size:" . filesize($filename) . " <br/>";
            } else {
                unset($k, $val);
            }
        }
    } else {
        unset($matches);
    }
}
exit;
?>
