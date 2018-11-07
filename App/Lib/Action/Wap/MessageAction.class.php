<?php

//留言
class MessageAction extends LoadAction {

    //追加内容
    public function index($bookid, $page) {
        $mesg = D('MessageView');
        $where['z_id'] = 0;
        $where['book_id'] = $bookid;
        $where['audit'] = 2;
        $count = $mesg->where($where)->count(); // 查询满足要求的总记录数      
        $number = 5; //每页条数
        $page = ($page - 1) * $number;
        //内容显示
        $message = $mesg->where($where)->limit($page . ',' . $number)->order('top desc,time desc')->select();
        $shuliang = count($message);
        if ($shuliang) {
            $this->assign('message', $message);
            $this->display();
        } else {
            echo 1;
        }
    }

    //发布主题
    public function release($bookid) {
        if (!cookie('user_id')) {
              login();//调用登录
            exit();
        }
        $where['book_id'] = $bookid;
        $books = M('Book')->where($where)->field('book_id,book_name')->find();
        $this->assign('books', $books);
        $this->display();
    }

    //提交数据
    public function tijiao($bookid) {
        if ($this->isPost()) {
            $mesg = M('BookMessage');
            $data['z_id'] = 0;
            $data['book_id'] = $bookid;
            $data['user_id'] = cookie('user_id');
            $count = $mesg->where($data)->count();
            if ($count > 10) {
                echo "系统错误";
                die;
            }
            $data['title'] = htmlentities($_POST['title']);
            $data['content'] = htmlentities($_POST['content']);
            $data['time'] = date("Y-m-d H:i:s");
            $isok = $mesg->add($data);
            //删除缓存
            $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $bookid));
            $Cache->rm('index');
            if ($isok) {
                echo 1;
            } else {
                echo "系统错误";
            }
        }
    }

    //用户回复
    public function reply($id) {
        $mesg = D('MessageView');
        $mesfes = $mesg->where(array('f_id' => $id, 'audit' => 2))->field('f_id,title,book_id,content,pen_name,time')->find();
        $where['book_id'] = $mesfes[book_id];
        $books = M('Book')->where($where)->field('book_id,book_name')->find();
        $this->assign('books', $books);
        $this->assign('mesfes', $mesfes);
        //子回复
        $zimess = $mesg->where(array('z_id' => $id, 'audit' => 2))->field('content,pen_name,time')->select();
        $this->assign('zimess', $zimess);
        $this->display();
    }

    //字回复
    public function replys($id) {
        if ($this->isPost()) {
            $mesg = M('BookMessage');
            $data['z_id'] = $id;
            $data['book_id'] = $_POST['bookid'];
            $data['user_id'] = $_COOKIE['user_id'];
            $count = $mesg->where($data)->count();
            if ($count > 10) {
                echo "系统错误";
                die;
            }
            $data['title'] = cookie('pen_name');
            $data['content'] = htmlentities($_POST['content']);
            $data['time'] = date("Y-m-d H:i:s");
            //父跟新
            $datas['num'] = array('exp', 'num+1');
            $mesg->where(array('f_id' => $data['z_id']))->save($datas);
            $isok = $mesg->add($data);
            if ($isok) {
                echo 1;
            } else {
                echo "系统错误";
            }
        }
    }

}
