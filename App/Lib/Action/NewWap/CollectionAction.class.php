<?php


class CollectionAction extends GlobalAction {
    //收藏
    public function index() {
        $collection = M('NBookCollection');
        $is = $collection->where(array('book_id' => $_POST[bookid], 'user_id' => $_COOKIE[user_id],'save'=>1))->find();
        if (!$is) {
            $data[book_id] = $_POST[bookid];
            $data[user_id] = $_COOKIE[user_id];
            $data[book_name] = $_POST[bookname];
            $data[save] = $_POST[save];
            $data[time] = date('Y-m-d H:i:s', time());
            $isok = $collection->add($data);
            if ($isok) {
                $datas['collection_day'] = array('exp', "collection_day+1");
                $datas['collection_weeks'] = array('exp', "collection_weeks+1");
                $datas['collection_month'] = array('exp', "collection_month+1");
                $datas['collection_total'] = array('exp', "collection_total+1");
                M('BookStatistical')->where(array('book_id' => $_POST[bookid]))->save($datas);
                //删除缓存
                $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $_POST[bookid]));
                $Cache->rm('index');
                echo "1";
            } else {
                echo "收藏失败";
            }
        } else {
            echo "0";
        }
    }

    //删除收藏
    public function dell() {
        $collection = M('NBookCollection');
        $is = $collection->where(array('book_id' => $_POST[bookid], 'user_id' => $_COOKIE[user_id],'save'=>1))->find();
        //$is = $collection->where(array('book_id' => $_POST[bookid]))->find();
        if ($is) {
            $isok = $collection->where(array('id' => $is[id]))->delete();
            if ($isok) {
                $datas['collection_total'] = array('exp', "collection_total-1");
                M('BookStatistical')->where(array('book_id' => $_POST[bookid]))->save($datas);
                //删除缓存
                $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $_POST[bookid]));
                $Cache->rm('index');
                echo "1";
            } else {
                echo "取消收藏失败";
            }
        } else {
            echo "0";
        }
    }

}
