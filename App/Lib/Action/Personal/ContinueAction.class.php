<?php

//阅读记录
class ContinueAction extends Action {

    //继续阅读
    public function reading() {
        if ($_COOKIE[history]) {
            $a = str_replace("title"," \"title\" ",$_COOKIE[history]);
            $obj = str_replace("url"," \"url\" ",$a);
            $obj = json_decode($obj,TRUE);
            header("location:$obj[url]");
        }  else {
            header("location:/Personal/Bookcase/index.html");
        }
    }
}
