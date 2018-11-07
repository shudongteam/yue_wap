<?php

// 本类由系统自动生成，仅供测试用途
class IndexAction extends LoadingAction {
// class IndexAction {

    public function index() {
        
        $cache = A('Cache');
        $cache->chushi("/Html_Cache/NewWap/" . $this->website[web_id] . "/", 'index');
        $cache->read_cache(); //读取缓存
        $this->slide(); 
        //$this->bar();
        $this->rexiao();
        $this->manhot();
        $this->womanhot();
        $this->cailike();
        $this->over();
        // $this->vip();
        $this->bangs();
        $this->display();
        $cache->create_cache(); //生成缓存
    }

    //3个幻灯片
    public function slide() {
        $bookpromote = M('NBookPromote');
        //$where['web_id'] = $this->website[web_id];
        $where['promote_id'] = 17;
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img')->limit(3)->order('xu asc')->select();
        $this->assign('slide', $arr);
    }
    public function get_promote_info($id, $limit = 4){
        $bookpromote = D('NBooktuiView');
        $where['promote_id'] = $id;
        $where['is_show'] = 1;
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,type_id,author_name')->limit($limit)->order('xu asc')->select();
        // echo $bookpromote->getLastSql();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i][book_brief] = mb_substr($arr[$i][book_brief], 0, 32, 'utf-8');
            $arr[$i][book_type] = BooktypeAction::mybooktype($arr[$i][type_id]);
        }
        return $arr;
    }

    //广告
    public function bar() {
        // $where['web_id'] = $this->website[web_id];
        // $where['pc'] = 0;
        // $where['num'] = 1;
        $where['id'] = 1;
        $ban = M('WebBan')->where($where)->field('pic,link')->find();    
        //print_r($ban);
        $this->assign('ban', $ban);
        return $ban;

    }

    //全站最畅销
    public function rexiao() {
        $arr=$this->get_promote_info(1, 3);
        $this->assign('rexiao', $arr);
    }
    

    //男生最当红
    public function manhot() {
        $arr = $this->get_promote_info(2, 3);
        $this->assign('manhot', $arr);
    }

    //女生最言情(周点击2总点击1)
    public function womanhot() {
        $arr = $this->get_promote_info(3, 3);
        $this->assign('womanhot', $arr);
    }

    //完结大推荐
    public function over() {
        $arr=$this->get_promote_info(4, 5);
        $this->assign('list3', $arr);
    }
    
    //猜你喜欢（获取最近浏览的一本书信息来搜索,如果没有书籍则任意显示四条）
    public function cailike() {
        $arr=$this->get_promote_info(6, 5);
        $this->assign('res', $arr);
    }

    //vip大放送
    // public function vip() {
    //     $arr=$this->get_promote_info(5);
    //     $this->assign('list4', $arr);
    // }

    //各种榜
    public function bangs() {
       
        //推荐
        $book = D('NRankinglistView');
        $con['fu_book'] = array('exp', " = Book.book_id");
        $con['book_id'] = undisplay();
        //$con['web_id']= $this->website['web_id'];
        $con['Book.is_show'] = 1;
        $tj = $book->where($con)->order('vote_total desc')->field('book_id,book_name')->limit(10)->select();
        $diamond = $book->where($con)->order('vipvote_total desc')->field('book_id,book_name')->limit(10)->select();
        //新书
        $where['audit'] = 2;
        $where['is_show'] = 1;
        $where['fu_book'] = array('exp', " = book_id");
        $where['book_id'] = undisplay();
        $where['chapter'] = array('gt', 3);
        $newbooks = M('Book')->where($where)->field('book_id,book_name')->order('book_id desc')->limit(10)->select();
        // echo M('Book')->getlastsql();
        //print_r($diamond);
        $this->assign('tj', $tj);
        $this->assign('diamond', $diamond);
        $this->assign('newbooks',$newbooks);
    }

    //联系我们
    public function concat() {
        $this->title = '联系我们';
        $this->display();
    }

}
