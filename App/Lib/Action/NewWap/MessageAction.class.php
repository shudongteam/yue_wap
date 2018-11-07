<?php

//留言
class MessageAction extends LoadingAction {

    //显示内容
    public function index($bookid) {
        $books = M('Book')->where(array('book_id' => $bookid))->field('book_id,book_name')->find();
        $this->assign('books', $books);
        $mesg = M('NBookMessage')->where(array('book_id' => $bookid))->limit(5)->order('top desc,time desc')->select();
        $this->assign('mesg', $mesg);
        $this->display();
    }

    #追加内容

    public function zuijia($bookid, $page) {
        $number = 5; //每页条数
        $page = ($page - 1) * $number;
        //内容显示
        $mesg = M('NBookMessage')->where(array('book_id' => $bookid))->limit($page . ',' . $number)->order('top desc,time desc')->select();
        $shuliang = count($mesg);
        if ($shuliang) {
            $this->assign('mesg', $mesg);
            $this->display();
        } else {
            echo 1;
        }
    }

    #提交数据

    public function tijiao() {
        if ($_COOKIE['user_id']) {
            //添加留言记录
            $mesg = M('NBookMessage');
            $datar['z_id'] = 0;
            $datar['book_id'] = $_POST[bookid];
            $datar['user_id'] = $_COOKIE['user_id'];
            $count = $mesg->where($datar)->count();
            if ($count > 10) {
                echo "系统错误";
                die;
            }
            $datar['pen_name'] = htmlentities($_COOKIE['pen_name']);
            $datar['content'] = htmlentities($_POST[content]);
            $datar['time'] = date('Y-m-d H:i:s', time());
            $isok = $mesg->add($datar);
            if ($isok) {
                echo 1;
            } else {
                echo "系统错误";
            }
        } else {
            echo "请先登录";
        }
    }

    #查看子回复

    public function huifu($id) {
        $mes = M('NBookMessage');
        #主题
        $zhuti = $mes->where(array('f_id' => $id))->order('time desc')->find();
        $this->assign('zhuti', $zhuti);
        #自回复
        $conmesg = $mes->where(array('z_id' => $id))->order('time desc')->select();
        $this->assign('conmesg', $conmesg);
        $this->display();
    }

    #子回复

    public function huifutijiao() {
        if ($_COOKIE['user_id']) {
            $mesg = M('NBookMessage');
            //添加留言记录
            $datar['z_id'] = $_POST[mesgid];
            $datar['book_id'] = 0;
            // $datar['book_id'] = $_POST['bookid'];
            $datar['user_id'] = $_COOKIE['user_id'];
            $count = $mesg->where($datar)->count();
            if ($count > 10) {
                echo "系统错误";
                die;
            }
            $datar['pen_name'] = htmlentities($_COOKIE['pen_name']);
            $datar['content'] = htmlentities($_POST[content]);
            $datar['time'] = date('Y-m-d H:i:s', time());
            $isok = $mesg->add($datar);
            $data['num'] = array('exp', 'num+1');
            $isok2 = $mesg->where(array('f_id' => $datar['z_id']))->save($data);
            if ($isok) {
                echo 1;
            } else {
                echo "系统错误";
            }
        } else {
            echo "请先登录";
        }
    }

}
