<?php

//作品
class BooksAction extends LoadingAction {

    public function _empty() {
        echo '页面不存在';
    }

    #首页
    public function index($bookid) {
        //header("Content-type: text/html; charset=utf-8");
        //设置缓存方式跟时间/文件缓存/时间3600秒/缓存路径
        $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $bookid));
        $bookfile = $Cache->get('index');
        if (!$bookfile) {
            $bookfile = $this->books($bookid);
            $Cache->set('index', $bookfile);
        }
        $this->assign('books', $bookfile);
        $this->statistical($bookid);
        // $this->collection($bookid);
        // $this->dashang($bookid);
        $this->display();
    }

    #作品信息

    public function books($bookid) {
        $where['book_id'] = $bookid;
        //$where['fu_book'] = $bookid;
        //$where['fu_book'] = array('exp', " = book_id");
        //$where['web_id'] = $this->website[web_id];
        $where['is_show'] = 1;
        $books = M('Book')->where($where)->field('book_id,fu_book,book_name,author_name,upload_img,state,chapter,book_brief,words,type_id')->find();

        if (!is_array($books)) {
            $this->error("没有找到该书");
            exit();
        }
        //修改作品简介样式
        $books['book_brief'] = strip_tags($books[book_brief]);
        
        //同款推荐(根据type_id 查寻点击数最高的15本, 随机出去三本书)
        $tongkuan = D('NRankinglistView');
        $con['fu_book'] = array('exp', " = Book.book_id");
        $con['book_id'] = undisplay();
        $con['Book.type_id']= $books['type_id'];
        $con['Book.is_show'] = 1;
        $tk = $tongkuan->where($con)->order('click_total desc')->field('book_id,book_name,upload_img')->limit(15)->select();
        $books['tongkuan'] = array();
        if ($tk) {
            shuffle($tk);
            $books['tongkuan'] = array_slice($tk, -5);
        }
        //分类名称
        $books['type_id'] = get_type($books['type_id']);
        
        //书籍统计表
        //$bookinfo = M('BookStatistical')->where($where)->field('click_total,collection_total,exceptional_total,vote_total,vipvote_total')->find();
        //$books = array_merge($books, $bookinfo);
        
        //最新更新章节
        $conn = M('BookContent');
        $newcontnet = $conn->where(array('fu_book' => $books[fu_book]))->field('content_id,title,num,time,the_price')->order('num desc')->find();
        
        //初始化显示5个章节目录
        $newlinst = $conn->where(array('fu_book' => $books[fu_book]))->field('content_id,title,num,time')->order('num asc')->limit(5)->select();
        $books['newcontnet'] = $newcontnet;
        $books['newlinst'] = $newlinst;
        
        //粉丝
        //$bookfans = M('NBookFans')->where(array('book_id' => $bookid))->order('fan_value desc')->limit(3)->select();
        //$books['bookfans'] = $bookfans;
        
        //初始化礼物表
        $exceptional = M('NBookExceptional')->where(array('book_id' => $bookid))->find();
        if (is_array($exceptional)) {
            $books['exceptional'] = $exceptional;
        } else {
            $data['book_id'] = $bookid;
            M('NBookExceptional')->add($data);
            $exceptional['num']=0;
            $exceptional['gift_1']=0;
            $exceptional['gift_2']=0;
            $exceptional['gift_3']=0;
            $exceptional['gift_4']=0;
            $exceptional['gift_5']=0;
            $exceptional['gift_6']=0;
            $books['exceptional'] = $exceptional;
        }
        return $books;
    }


    //更新用户点击
    public function statistical($bookid) {
        // $data['click_day'] = array('exp', "click_day+1");
        // $data['click_weeks'] = array('exp', "click_weeks+1");
        // $data['click_month'] = array('exp', "click_month+1");
        // $data['click_total'] = array('exp', "click_total+1");
        // M("BookStatistical")->where(array('book_id' => $bookid))->save($data);

        //延迟更新, 5分钟
        $BookStatistical = M("BookStatistical");
        $BookStatistical->switchModel('Adv')->where(array('book_id' => $bookid))->setLazyInc("click_day", 1, 300);
        $BookStatistical->switchModel('Adv')->where(array('book_id' => $bookid))->setLazyInc("click_weeks", 1, 300);
        $BookStatistical->switchModel('Adv')->where(array('book_id' => $bookid))->setLazyInc("click_month", 1, 300);
        $BookStatistical->switchModel('Adv')->where(array('book_id' => $bookid))->setLazyInc("click_total", 1, 300);

    }

    //判断用户是否收藏
    protected function collection($bookid) {
        if ($_COOKIE[user_id]) {
            $where['book_id'] = $bookid;
            $where['user_id'] = $_COOKIE[user_id];
            $where['save'] = 1;
            $isok = M('NBookCollection')->where($where)->field('id')->find();
            if (is_array($isok)) {
                //已收藏
                $this->assign('collection', 1);
            } else {
                $this->assign('collection', 0);
            }
        } else {
            $this->assign('collection', 0);
        }
    }

    //打赏初始查询3条
    public function dashang($bookid){
         header("Content-type:text/html;charset=utf-8");

            $cont = M('n_user_consumerecord');
            $num = $cont->where(array('book_id' => $bookid,"type"=>2))->count();
            $data = D('DashangView')->where(array('NUserConsumerecord.book_id' => $bookid,"NUserConsumerecord.type"=>2))->order('time desc')->limit(3)->select();
            //print_r($arr);
            $this->assign('dashang', $data);
            $this->assign('num', $num);
    }
}
