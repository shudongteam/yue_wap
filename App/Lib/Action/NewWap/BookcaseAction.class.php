<?php

//阅读记录
class BookcaseAction extends LoadingAction {

    public function index($r=0) {
        //判断r,0浏览记录，1收藏
        if($r == 1){
            $where['save'] = 1;
            $where['user_id'] = $_COOKIE['user_id'];
            $books = M('NBookCollection')->where($where)->order('id desc')->limit(50)->select();
        } else {
            $key = 'collect';
            $path = RUNTIME_PATH.'collect/' . $_COOKIE['user_id'];
            $collect = F($key, '', $path);
            $books = array();
            if ($collect) {
                foreach ($collect as $k => $v) {
                    $books[] = F($key.'_'.$v, '', $path);
                }
            }
            krsort($books);
        }
        $this->assign('books', $books);
        $this->assign('uid', $_COOKIE['user_id']);
        $this->assign('r', $r);
        $this->assign('title', "我的书架");
        $this->display();
    }
    
    //继续阅读
    public function reading() {
        if ($_COOKIE[history]) {
            $a = str_replace("title"," \"title\" ",$_COOKIE[history]);
            $obj = str_replace("url"," \"url\" ",$a);
            $obj = json_decode($obj,TRUE);
            header("location:$obj[url]");
        }  else {
           header("location:/Bookcase/index/r/0.html");
        }
    }

    //删除收藏书本
    public function del() {
        if($_POST['r']==0){
            $save = 0;
        }else{
            $save = 1;
        }
        $id = isset($_POST[id]) ? intval($_POST[id]) : 0;
        $bookid = isset($_POST[bookid]) ? intval($_POST[bookid]) : 0;
        $status = "1";
        if (!$save) {
            $key = 'collect';
            $path = RUNTIME_PATH.'collect/' . $_COOKIE['user_id'];
            $collect = F($key, '', $path);
            if ($collect) {
                foreach ($collect as $k => $value) {
                    if ($value == $bookid) {
                        unset($collect[$k]);
                        $collect = array_values($collect);
                        break;
                    }
                }
            }
            F($key, $collect, $path);
            F($key.'_'.$bookid, null, $path);
            $status = "0";
        } else {
            $collection = M('NBookCollection');
            $is = $collection->where(array('id' => $id))->find();
            if ($is) {
                if ($is['user_id'] == $_COOKIE[user_id] && $is['book_id'] == $bookid) {
                    $isok = $collection->where(array('id' => $id))->delete();
                    if ($isok) {
                        $datas['collection_total'] = array('exp', "collection_total-1");
                        M('BookStatistical')->where(array('book_id' => $bookid))->save($datas);
                        //删除缓存
                        $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $bookid));
                        $Cache->rm('newindex');
                        $status = "0";
                    }
                }
            }
        }
        echo $status;
    }
}
