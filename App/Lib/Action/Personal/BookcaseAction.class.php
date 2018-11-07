<?php

//阅读记录
class BookcaseAction extends GlobalAction {

    public function index() {
        $collection = D('CollectionView');
        import('ORG.Util.Page'); // 导入分页类
        $where['user_id'] = $this->to[user_id];
        $count = $collection->where($where)->count(); // 查询满足要求的总记录数    
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记   
        $Page->setConfig('theme', " <span class=\"pager_left\">%upPage%&nbsp;</span> <span class=\"pager_conn\">%nowPage%/%totalPage%</span> <span class=\"pager_right\">&nbsp;%downPage%</span>");
        $books = $collection->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        // echo $collection->getLastSql();
        $this->assign('books', $books);
        $Page->url ='Personal/Bookcase/index/p/';
        $show = $Page->show(); // 分页显示输出
        $this->assign('page', $show);
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
            header("location:/Bookcase/index.html");
        }
    }
}
