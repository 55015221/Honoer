<?php

//KindEditor编辑器
function editor($name, $width, $height, $content) {
    $editor = "<script charset=\"utf-8\" src=\"" . __ROOT__ . "/Public/Plugins/kindeditor-4.1.6/kindeditor-all.js\"></script>\n";
    $editor.="<script charset=\"utf-8\" src=\"" . __ROOT__ . "/Public/Plugins/kindeditor-4.1.6/lang/zh_CN.js\"></script>\n";
    $editor.="<script type=\"text/javascript\">\n";
    $editor.="var editor;
    KindEditor.ready(function(K) {
        editor = K.create(\"textarea[name='{$name}']\", {
            uploadJson : '" . U('Upload/upload_file') . "',
            fileManagerJson : '" . U('Upload/file_manager') . "',
            allowFileManager : true,
        });
    });\n";
    $editor.="</script>\n";
    $editor.="<textarea name=\"" . $name . "\" style=\"width:" . $width . "px;height:" . $height . "px;visibility:hidden;\">" . $content . "</textarea>";
    return $editor;
}

//公共函数文件
function saveUserInfo($args) {
    if (is_array($args)) {
        foreach ($args as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
}

//更新缓存
function updateCache() {
    import("@.ORG.Dir");
    $dir = new Dir;
    @unlink(RUNTIME_PATH . '~runtime.php');
    if (is_dir(RUNTIME_PATH . 'Cache')) {
        $dir->delDir(RUNTIME_PATH . 'Cache');
    }
    if (is_dir(RUNTIME_PATH . 'Data')) {
        $dir->delDir(RUNTIME_PATH . 'Data');
    }
    if (is_dir(RUNTIME_PATH . 'Logs')) {
        $dir->delDir(RUNTIME_PATH . 'Logs');
    }
    if (is_dir(RUNTIME_PATH . 'Temp')) {
        $dir->delDir(RUNTIME_PATH . 'Temp');
    }
    if (is_dir(RUNTIME_PATH)) {
        $dir->delDir(RUNTIME_PATH);
    }
    return true;
}

// 数组保存到文件
function arr2file($filename, $arr = '') {
    if (is_array($arr)) {
        $content = var_export($arr, true);
    } else {
        $content = $arr;
    }
    $content = "<?php\nreturn $content;\n?>"; //生成配置文件内容

    $dir = dirname($filename);
    if (!is_dir($dir)) {
        mkdir($dir);
    }
    return @file_put_contents($filename, $content); //写入./config.php中
}

//删除空格
function del_html_tags($str) {
    $str = trim($str);
    $str = strip_tags($str, "");
    $str = str_replace("\t", "", $str);
    $str = str_replace("\r\n", "", $str);
    $str = str_replace("\r", "", $str);
    $str = str_replace("\n", "", $str);
    $str = str_replace(" ", " ", $str);
    return trim($str);
}

//设置SEO信息
function setseo($args) {
    return array(
        'title' => $args[0],
        'Description' => $args[1],
        'Keywords' => $args[2],
    );
}

//格式化时间
function timer($time, $format = 'Y-m-d') {
    empty($time) && $time = time();
    if (is_numeric($time)) {
        return date($format, $time);
    } else {
        return date($format, strtotime($time));
    }
}

//email转成usernick
function emailToNick($email) {
    return substr($email, 0, strpos($email, "@"));
}

function save_user($email) {
    return strstr($email, '@', true);
}

/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = & $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = & $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = & $refer[$parentId];
                    $parent[$child][] = & $list[$key];
                }
            }
        }
    }
    return $tree;
}

function sub_content($data, $field, $length) {
    foreach ($data as $key => $value) {
        if (strlen($value[$field]) > $length) {
            $data[$key][$field] = blog_summary($value[$field], $length, '');
        }
    }
    return $data;
}

function cn_substr($str, $length, $suffix = '...', $start = 0, $charset = 'utf8') {
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    $re['utf8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    preg_match_all($re[strtolower(str_replace('-', '', $charset))], $str, $match);
    $l = count($match[0]);
    $more = false;
    if (0 < $l) {
        $StringLast = array();
        $length -= (($suffix ? strlen($suffix . '') : 0) - $start);
        for ($i = $start; $i < $length; $i++) {
            $targetStr = isset($match[0][$i]) ? $match[0][$i] : null;
            if (ord($targetStr) >= 192) {
                $length--;
            }
            $StringLast[] = $targetStr;
        }
        $c = count($StringLast);
        if ($c < $l) {
            $more = true;
            if ($suffix && $c + 1 == $l) {
                $StringLast[] = $match[0][$i];
                $more = false;
            }
        }
    }
    $text = (empty($StringLast) ? '' : implode('', $StringLast)) . ((false !== $suffix && $more) ? $suffix : '');
    return $text;
}

/* * ****************************** 
 * 生成摘要 
 * @param (string) $body 
 *  正文 
 * @param (int) $size 
 *  摘要长度 
 * @param (int) $format 
 *  输入格式 id 
 * ***************************** */

function blog_summary($body, $size, $format = '...') {
    $_size = mb_strlen($body, 'utf-8');
    if ($_size <= $size)
        return $body;
    $strlen_var = strlen($body);
    // 不包含 html 标签 
    if (strpos($body, '<') === false) {
        return mb_substr($body, 0, $size);
    }
    // 包含截断标志，优先 
    if ($e = strpos($body, '<!-- break -->')) {
        return mb_substr($body, 0, $e);
    }
    // html 代码标记 
    $html_tag = 0;
    // 摘要字符串 
    $summary_string = '';
    /**
     * 数组用作记录摘要范围内出现的 html 标签 
     * 开始和结束分别保存在 left 和 right 键名下 
     * 如字符串为：<h3><p><b>a</b></h3>，假设 p 未闭合 
     * 数组则为：array('left' => array('h3', 'p', 'b'), 'right' => 'b', 'h3'); 
     * 仅补全 html 标签，<? <% 等其它语言标记，会产生不可预知结果 
     */
    $html_array = array('left' => array(), 'right' => array());
    for ($i = 0; $i < $strlen_var; ++$i) {
        if (!$size) {
            break;
        }
        $current_var = substr($body, $i, 1);
        if ($current_var == '<') {
            // html 代码开始 
            $html_tag = 1;
            $html_array_str = '';
        } else if ($html_tag == 1) {
            // 一段 html 代码结束 
            if ($current_var == '>') {
                // 去除首尾空格，如 <br /  > < img src="" / > 等可能出现首尾空格 
                $html_array_str = trim($html_array_str);
                //判断最后一个字符是否为 /，若是，则标签已闭合，不记录 
                if (substr($html_array_str, -1) != '/') {
                    // 判断第一个字符是否 /，若是，则放在 right 单元 
                    $f = substr($html_array_str, 0, 1);
                    if ($f == '/') {
                        // 去掉 / 
                        $html_array['right'][] = str_replace('/', '', $html_array_str);
                    } else if ($f != '?') {
                        // 判断是否为 ?，若是，则为 PHP 代码，跳过 
                        //判断是否有半角空格，若有，以空格分割，第一个单元为 html 标签 如 <h2> <p>
                        if (strpos($html_array_str, ' ') !== false) {
                            // 分割成2个单元，可能有多个空格，如：<h2 class="" id=""> 
                            $html_array['left'][] = strtolower(current(explode(' ', $html_array_str, 2)));
                        } else {
                            //若没有空格，整个字符串为 html 标签，如：<b> <p> 等 统一转换为小写 
                            $html_array['left'][] = strtolower($html_array_str);
                        }
                    }
                }
                // 字符串重置 
                $html_array_str = '';
                $html_tag = 0;
            } else {
                //将< >之间的字符组成一个字符串 用于提取 html 标签 
                $html_array_str .= $current_var;
            }
        } else {
            // 非 html 代码才记数 
            --$size;
        }
        $ord_var_c = ord($body{$i});
        switch (true) {
            case (($ord_var_c & 0xE0) == 0xC0):
                // 2 字节 
                $summary_string .= substr($body, $i, 2);
                $i += 1;
                break;
            case (($ord_var_c & 0xF0) == 0xE0):
                // 3 字节 
                $summary_string .= substr($body, $i, 3);
                $i += 2;
                break;
            case (($ord_var_c & 0xF8) == 0xF0):
                // 4 字节 
                $summary_string .= substr($body, $i, 4);
                $i += 3;
                break;
            case (($ord_var_c & 0xFC) == 0xF8):
                // 5 字节 
                $summary_string .= substr($body, $i, 5);
                $i += 4;
                break;
            case (($ord_var_c & 0xFE) == 0xFC):
                // 6 字节 
                $summary_string .= substr($body, $i, 6);
                $i += 5;
                break;
            default:
                // 1 字节 
                $summary_string .= $current_var;
        }
    }
    if ($html_array['left']) {
        //比对左右 html 标签，不足则补全 
        /**
         * 交换 left 顺序，补充的顺序应与 html 出现的顺序相反 
         * 如待补全的字符串为：<h2>abc<b>abc<p>abc 
         * 补充顺序应为：</p></b></h2> 
         */
        $html_array['left'] = array_reverse($html_array['left']);
        foreach ($html_array['left'] as $index => $tag) {
            // 判断该标签是否出现在 right 中 
            $key = array_search($tag, $html_array['right']);
            if ($key !== false) {
                // 出现，从 right 中删除该单元 
                unset($html_array['right'][$key]);
            } else {
                // 没有出现，需要补全 
                $summary_string .= '</' . $tag . '>';
            }
        }
    }
    return $summary_string . $format;
}

//QQ登录用到的方法
function do_post($url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_URL, $url);
    $ret = curl_exec($ch);
    curl_close($ch);
    return $ret;
}

function get_url_contents($url) {
    if (ini_get("allow_url_fopen") == "1")
        return file_get_contents($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function get_user_info() {
    $get_user_info = "https://graph.qq.com/user/get_user_info?"
            . "access_token=" . $_SESSION['access_token']
            . "&oauth_consumer_key=" . C("QQ_APPID")
            . "&openid=" . $_SESSION["openid"]
            . "&format=json";

    $info = get_url_contents($get_user_info);
    $arr = json_decode($info, true);
    return $arr;
}

function get_info() {
    $get_info = "https://graph.qq.com/user/get_info?"
            . "&access_token=" . $_SESSION['access_token']
            . "&oauth_consumer_key=" . C("QQ_APPID")
            . "&openid=" . $_SESSION['openid']
            . "&format=json";
    $info = get_url_contents($get_info);
    $arr = json_decode($info, true);

    return $arr;
}

?>
