<?php

// 本类由系统自动生成，仅供测试用途
class IndexAction extends LoadAction {

    public function index() {       
        $cache = A('Cache');
        $cache->chushi("/Html_Cache/" . $this->website[web_id] . "/", 'index');
        $cache->read_cache(); //读取缓存
        $this->slide();
        $this->hots();
        $this->qianli();
        $this->bar();
        $this->rexiao();
        $this->jingpin();
        $this->display();
        $cache->create_cache(); //生成缓存   
    }

    //3个幻灯片
    public function slide() {
        $bookpromote = M('BookPromote');
        $where['web_id'] = $this->website[web_id];
        $where['promote_id'] = 1;
        $where['book_id'] = $this->undisplay();
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img')->limit(3)->order('xu asc')->select();
        $this->assign('slide', $arr);
    }

    //热门推荐
    public function hots() {
        $bookpromote = D('BooktuiView');
        $where['BookPromote.web_id'] = $this->website[web_id];
        $where['promote_id'] = 2;
        $where['fu_book'] = $this->undisplay();
        //$where['book_id'] = $this->undisplay();
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,author_name')->limit(3)->order('xu asc')->select();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i][book_brief] = mb_substr($arr[$i][book_brief], 0, 32, 'utf-8');
        }
        $this->assign('hots', $arr);
    }

    //潜力新作
    public function qianli() {
        $bookpromote = D('BooktuiView');
        $where['BookPromote.web_id'] = $this->website[web_id];
        $where['promote_id'] = 3;
        //$where['book_id'] = $this->undisplay();
        $where['fu_book'] = $this->undisplay();
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,type_id')->limit(8)->order('xu asc')->select();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i][book_type] = BooktypeAction::mybooktype($arr[$i][type_id]);
        }
        $this->assign('qianli', $arr);
    }

//广告
    public function bar() {
        $where['web_id'] = $this->website[web_id];
        $where['pc'] = 0;
        $where['num'] = 1;
        $ban = M('WebBan')->where($where)->find();
        $this->assign('ban', $ban);
    }

    //热销专区
    public function rexiao() {
        $bookpromote = D('BooktuiView');
        $where['BookPromote.web_id'] = $this->website[web_id];
        $where['promote_id'] = 4;
        //$where['book_id'] = $this->undisplay();
        $where['fu_book'] = $this->undisplay();
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,type_id,author_name')->limit(5)->order('xu asc')->select();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i][book_brief] = mb_substr($arr[$i][book_brief], 0, 32, 'utf-8');
            $arr[$i][book_type] = BooktypeAction::mybooktype($arr[$i][type_id]);
        }
        $this->assign('rexiao', $arr);
    }

    //精品推荐
    public function jingpin() {
        $bookpromote = D('BooktuiView');
        $where['BookPromote.web_id'] = $this->website[web_id];
        $where['promote_id'] = 5;
        //$where['book_id'] = $this->undisplay();
        $where['fu_book'] = $this->undisplay();
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,type_id,author_name')->limit(5)->order('xu asc')->select();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i][book_brief] = mb_substr($arr[$i][book_brief], 0, 32, 'utf-8');
            $arr[$i][book_type] = BooktypeAction::mybooktype($arr[$i][type_id]);
        }
        $this->assign('jingpin', $arr);
    }
    //屏蔽的ID
    private function undisplay(){
         return array('not in','4111,6297,6326,7967,9747,9871,10277,10343,4445,3652,9920,10279,10193,5787,9576,3887,9535,4810,99605,78569,78579,99597,10210,352,3798,3696,4937,9510,5832,10195,4047,4251,6398,9492,9506,9505,5724,107990,107624,6444,109522,4159,114887,109637,9890');
    }
}