<?php

//各类常用工具
class GongjuAction extends Action {

    //检查网站是否存在
    public function webssite() {
        //网站加载
        $isweb = $_SERVER['HTTP_HOST'];
        if (include_once "Website/$isweb.php") {
            return $website;
        } else {
            //查找是否存在网站
            $aa = M('Web');
            $myweb = $aa->where(array('web_url' => $isweb))->field('web_id,master_id,web_name,web_url,all_ps,login_url,automatic,preload,webphone,webqq,weixin,beian,qq_id,qq_secret,wb_id,wb_secret,wx_id,wx_secret')->find();
            if (is_array($myweb)) {
                //存在这个网站进行生成
                $file = "Website/$isweb.php";
                $text = '<?php $website=' . var_export($myweb, true) . ';';
                if (false !== fopen($file, 'w+')) {
                    file_put_contents($file, $text);
                    return $myweb;
                } else {
                    $this->error("生成失败");
                }
            } else {
                $this->error("没有该网站");
                exit();
            }
        }
    }
}
