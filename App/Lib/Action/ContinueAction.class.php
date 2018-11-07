<?php

//阅读记录
class ContinueAction extends Action {

    //继续阅读
    public function reading() {
        echo 111;
        die();
        if ($_COOKIE[history]) {
            echo 1;
            exit();
            $a = str_replace("title"," \"title\" ",$_COOKIE[history]);
            $obj = str_replace("url"," \"url\" ",$a);
            $obj = json_decode($obj,TRUE);
            header("location:$obj[url]");
        }  else {
            echo 2;
            exit();
            header("location:/Personal/Bookcase/index.html");
        }
    }
}
