<?php

class UploadAction extends BasicAction {

    private $savePath = null;
    private $saveUrl = null;
    private $uploadObj = null;

    public function __construct() {
        parent::__construct();
        import("@.ORG.Upload");
        $this->savePath = UPLOAD_PATH;
        $this->saveUrl = '/Public/Upload/';
        $this->uploadObj = new Upload($this->savePath, $this->saveUrl);
    }

    
    /**
     * KingEditor 文件上传
     */
    public function upload_file() {
        $rs = $this->uploadObj->upload_json();
    }

    /**
     * KingEditor 文件管理
     */
    public function file_manager() {
        $rs = $this->uploadObj->file_manager_json();
    }

    //图片上传
    public function imageUp() {
        $title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
        $path = htmlspecialchars($_POST['dir'], ENT_QUOTES);
        $config = array(
            "savePath" => $this->savePath,
            "maxSize" => 1000, //单位KB
            "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")
        );
        $up = new Uploader("upfile", $config);
        $info = $up->getFileInfo();
        echo "{'url':'" . $info["url"] . "','title':'" . $title . "','original':'" . $info["originalName"] . "','state':'" . $info["state"] . "'}";
    }

    //文件上传
    public function fileUp() {
        $config = array(
            "savePath" => $this->savePath, //保存路径
            "allowFiles" => array(".rar", ".doc", ".docx", ".zip", ".pdf", ".txt", ".swf", ".wmv"), //文件允许格式
            "maxSize" => 100000 //文件大小限制，单位KB
        );
        $up = new Uploader("upfile", $config);
        $info = $up->getFileInfo();
        echo '{"url":"' . $info["url"] . '","fileType":"' . $info["type"] . '","original":"' . $info["originalName"] . '","state":"' . $info["state"] . '"}';
    }

    //图片管理
    public function imageManager() {
        $paths = array($this->savePath);
        $action = htmlspecialchars($_POST["action"]);
        if ($action == "get") {
            $files = array();
            foreach ($paths as $path) {
                $tmp = self::getfiles($path);
                if ($tmp) {
                    $files = array_merge($files, $tmp);
                }
            }
            if (!count($files))
                return;
            rsort($files, SORT_STRING);
            $str = "";
            foreach ($files as $file) {
                $str .= $file . "ue_separate_ue";
            }
            echo $str;
        }
    }

    private static function getfiles($path, &$files = array()) {
        if (!is_dir($path))
            return null;
        $handle = opendir($path);
        while (false !== ( $file = readdir($handle) )) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . '/' . $file;
                if (is_dir($path2)) {
                    self::getfiles($path2, $files);
                } else {
                    if (preg_match("/\.(gif|jpeg|jpg|png|bmp)$/i", $file)) {
                        $files[] = $path2;
                    }
                }
            }
        }
        return $files;
    }

    //抓取土豆视频API
    public function getMovie() {
        $key = htmlspecialchars($_POST["searchKey"]);
        $type = htmlspecialchars($_POST["videoType"]);
        $html = file_get_contents('http://api.tudou.com/v3/gw?method=item.search&appKey=myKey&format=json&kw=' . $key . '&pageNo=1&pageSize=20&channelId=' . $type . '&inDays=7&media=v&sort=s');
        echo $html;
    }

    //抓取远程图片
    public function getRemoteImage() {
        $config = array(
            "savePath" => "upload/", //保存路径
            "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp"), //文件允许格式
            "maxSize" => 3000                    //文件大小限制，单位KB
        );
        $uri = htmlspecialchars($_POST['upfile']);
        $uri = str_replace("&amp;", "&", $uri);
        self::RemoteImage($uri, $config);
    }

    /**
     * 远程抓取
     * @param $uri
     * @param $config
     */
    private function RemoteImage($uri, $config) {
        set_time_limit(0);
        //ue_separate_ue  ue用于传递数据分割符号
        $imgUrls = explode("ue_separate_ue", $uri);
        $tmpNames = array();
        foreach ($imgUrls as $imgUrl) {
            if (strpos($imgUrl, "http") !== 0) {
                array_push($tmpNames, "error");
                continue;
            }
            $heads = get_headers($imgUrl);
            if (!( stristr($heads[0], "200") && stristr($heads[0], "OK") )) {
                array_push($tmpNames, "error");
                continue;
            }
            $fileType = strtolower(strrchr($imgUrl, '.'));
            if (!in_array($fileType, $config['allowFiles']) || stristr($heads['Content-Type'], "image")) {
                array_push($tmpNames, "error");
                continue;
            }
            ob_start();
            $context = stream_context_create(
                    array(
                        'http' => array(
                            'follow_location' => false // don't follow redirects
                        )
                    )
            );
            //请确保php.ini中的fopen wrappers已经激活
            readfile($imgUrl, false, $context);
            $img = ob_get_contents();
            ob_end_clean();
            $uriSize = strlen($img);
            $allowSize = 1024 * $config['maxSize'];
            if ($uriSize > $allowSize) {
                array_push($tmpNames, "error");
                continue;
            }
            $savePath = $config['savePath'];
            if (!file_exists($savePath)) {
                mkdir("$savePath", 0777);
            }
            $tmpName = $savePath . rand(1, 10000) . time() . strrchr($imgUrl, '.');
            try {
                $fp2 = @fopen($tmpName, "a");
                fwrite($fp2, $img);
                fclose($fp2);
                array_push($tmpNames, $tmpName);
            } catch (Exception $e) {
                array_push($tmpNames, "error");
            }
        }
        echo "{'url':'" . implode("ue_separate_ue", $tmpNames) . "','tip':'远程图片抓取成功！','srcUrl':'" . $uri . "'}";
    }

    public function uploadFiles() {
        import('@.ORG.UploadFile');
        import('@.ORG.Image');
        $upload = new UploadFile(); // 实例化上传类
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->savePath = $this->savePath; // 设置附件上传目录
        $upload->thumb = true;
        $upload->thumbMaxWidth = '140';
        $upload->thumbMaxHeight = '98';
        if (!$upload->upload()) {// 上传错误提示错误信息
            //return $upload->getErrorMsg();
            return false;
        } else {
            // 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
        }
        return current($info);
    }

}

?>
