<?php

//搜索
class SelectAction extends LoadingAction {

    public function index() {     
        if ($this->isPost()) {
            if ($_POST['bookname']) {
                //特殊字符过滤
                $keyword = do_keyword($_POST[bookname]);
                $cache_key = md5($keyword.'newwap');
                $Cache = Cache::getInstance('File', array('expire' => '36000', 'temp' => RUNTIME_PATH . 'Temp/Select'));
                $arr = $Cache->get($cache_key);
                if (!$arr) {
                    $con['book_name'] = array('like', "%$keyword%");
                    $con['author_name'] = array('like', "%$keyword%");
                    $con['_logic'] = 'OR';
                    $where['_complex'] = $con;
                    $where['is_show'] = 1;
                    $where['fu_book'] = array('exp', " = book_id");
                    $where['book_id'] = array('neq', 109522);
                    //$where['book_id'] = undisplay();
                    $$where['book_id'] = block_book();
                    $arr = M('Book')->where($where)->field('book_id,book_name,upload_img,book_brief,author_name')->select();
                    $Cache->set($cache_key, $arr);
                }

                $this->assign('shuliang', count($arr));
                $this->assign('jieguo', $arr);
            }else{
                $this->assign('sou', 1);
            } 
        }else {
            $this->assign('sou', 1);
        }
        $this->sousuo();
        $this->display();
    }

    //热门推荐
    public function sousuo() {
        $arr = S('rmtuijian');
        if (!$arr) {
            $bookpromote = D('NBooktuiView');
            $where['web_id'] = $this->website[web_id];
            $where['promote_id'] = 14;
            $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,author_name')->limit(3)->order('xu asc')->select();
            S('rmtuijian', $arr, 36000);
        }
        $this->assign('sousuo', $arr);
    }

}
