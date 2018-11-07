<?php

//作品
class BooksAction extends LoadAction {

    public function _empty() {
        echo '页面不存在';
    }

    public function index($bookid) {
        //设置缓存方式跟时间/文件缓存/时间3600秒/缓存路径
        $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $bookid));
        $bookfile = $Cache->get('index');
        if (!$bookfile) {
            $Databook = $this->books($bookid);
            $Databook['newbook'] = $this->newbook();
            $Databook['message'] = $this->message($bookid);
            $Cache->set('index', $Databook);
            $this->assign('books', $Databook);
        } else {
            $this->assign('books', $bookfile);
        }
        $this->collection($bookid);
        $this->statistical($bookid);
        $this->display();
    }

    public function books($bookid) {
        $where['book_id'] = $bookid;
        $where['web_id'] = $this->website[web_id];
        //$where['is_show'] = 1;
        $books = M('Book')->where($where)->field('book_id,fu_book,book_name,author_name,upload_img,state,chapter,book_brief,words')->find();
        if (!is_array($books)) {
            $this->error("没有找到该书");
            exit();
        }
        //修改作品简介样式
        $books['book_brief'] = str_replace("\n", "</p><p>", str_replace(" ", "", $books[book_brief]));
        //书籍附表
        $bookinfo = M('BookStatistical')->where($where)->field('click_total,collection_total,exceptional_total,vote_total,vipvote_total')->find();
        $books = array_merge($books, $bookinfo);
        //最新更新
        $conn = M('BookContent');
        $newcontnet = $conn->where(array('fu_book' => $books[fu_book], 'attribute' => array('lt', date("Y-m-d H:i:s"))))->field('content_id,title,num,time,attribute,the_price')->order('num desc')->find();
        $newlinst = $conn->where(array('fu_book' => $books[fu_book]))->field('content_id,title,num,time')->order('num asc')->limit(5)->select();
        $books['newcontnet'] = $newcontnet;
        $books['newlinst'] = $newlinst;
        return $books;
    }

    //新书榜单
    public function newbook() {
        $bookpromote = M('BookPromote');
        $where['web_id'] = $this->website[web_id];
        $where['promote_id'] = 21;
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img')->limit(3)->order('xu asc')->select();
        return $arr;
    }

    //评论查看
    public function message($bookid) {
        $mesg = D('MessageView');
        $where['z_id'] = 0;
        $where['book_id'] = $bookid;
        $where['audit'] = 2;        
        //内容显示
        $message = $mesg->where($where)->field('f_id,title,book_id,content,pen_name,num,time')->limit(5)->order('top desc,time desc')->select();
        return $message;
    }

    //判断用户是否收藏
    protected function collection($bookid) {
        if ($_COOKIE[user_id]) {
            $where['book_id'] = $bookid;
            $where['user_id'] = $_COOKIE[user_id];
            $isok = M('BookCollection')->where($where)->field('id')->find();
            if (is_array($isok)) {
                $this->assign('collection', 2);
            } else {
                $this->assign('collection', 1);
            }
        } else {
            $this->assign('collection', 1);
        }
    }

    //更新用户点击
    public function statistical($bookid) {
        $data['click_day'] = array('exp', "click_day+1");
        $data['click_weeks'] = array('exp', "click_weeks+1");
        $data['click_month'] = array('exp', "click_month+1");
        $data['click_total'] = array('exp', "click_total+1");
        M("BookStatistical")->where(array('book_id' => $bookid))->save($data);
    }

}
