<?php

//章节LoadingAction列表
class ShowclistAction extends LoadAction {

    public function _empty() {
        //该方法即为空操作
        echo '当前操作不存在';
    }

    public function index($bookid, $paixu = "asc") {
        $this->chapterlist($bookid, $paixu);
        if ($paixu == 'asc') {
            $this->assign('xuxie', 1);
        } else {
            $this->assign('xuxie', 2);
        }
        $this->haoshu();
        $this->display();
    }

    public function chapterlist($bookid, $paixu) {
        //date_default_timezone_set('PRC');
        import('ORG.Util.Page'); // 导入分页类           
        $book = D('Book');
        $content = M('BookContent');
        $where['book_id'] = $bookid;
        $where['web_id'] = $this->website[web_id];
        $info = $book->where($where)->field('book_id,fu_book,book_name')->find();
        $map['fu_book'] = $info[fu_book];
        $map['attribute'] = array('lt', date('Y-m-d H:i:s', time()));
        $count = $content->where($map)->field('book_id')->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数           
        $content_arr = $content->where($map)->field('content_id,title,num,the_price')->limit($Page->firstRow . ',' . $Page->listRows)->order("num $paixu")->select();
        //$Page->setConfig('theme', " <span class=\"pager_left\">%upPage%&nbsp;</span> <span class=\"pager_conn\">%nowPage%/%totalPage%</span> <span class=\"pager_right\">&nbsp;%downPage%</span>");

        // $Page->setConfig('theme', " <span class=\"pager_left\">%upPage%&nbsp;</span><span class=\"pager_right\">&nbsp;%downPage%</span><form action=\"\"  method=\"get\"><span class=\"\">跳到&nbsp;&nbsp;<input name=\"p\" type=\"text\" style=\"border: 1px solid #ddd; height: 26px;  width: 44px; text-align: center;\">&nbsp;页&nbsp;<input class=\"btnis\" type=\"submit\" value=\"跳转\" style=\"padding: 3px 12px; background-color: #e563a3; color: #fff; border-radius: 3px;\" /></form></span><span class=\"pager_conn\">&nbsp;&nbsp;%nowPage%/%totalPage%</span>");
        
        $Page->setConfig('theme', " <span class=\"pager_left\">%upPage%&nbsp;</span><span class=\"pager_conn\">&nbsp;&nbsp;%nowPage%/%totalPage%</span><span class=\"pager_right\">&nbsp;%downPage%</span><form action=\"\"  method=\"get\"><span class=\"\">跳到&nbsp;&nbsp;<input name=\"p\" type=\"text\" style=\"border: 1px solid #ddd; height: 26px;  width: 44px; text-align: center;\">&nbsp;页&nbsp;<input class=\"btnis\" type=\"submit\" value=\"跳转\" style=\"padding: 3px 12px; background-color: #e563a3; color: #fff; border-radius: 3px;\" /></form></span>");
        $show = $Page->show(); // 分页显示输出
        $this->assign('chapterlist', $content_arr);
        $this->assign('page', $show);
        $this->assign('bookinfo', $info);
    }

    //好书推荐
    public function haoshu() {
        $bookpromote = M('BookPromote');
        $where['web_id'] = $this->website[web_id];
        $where['promote_id'] = 7;
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img')->limit(3)->order('xu asc')->select();
        $this->assign('potential', $arr);
    }
}
