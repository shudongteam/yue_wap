<?php

//章节LoadingAction列表
class ShowclistAction extends LoadingAction {

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
        import('ORG.Util.Page'); // 导入分页类           
        $book = M('Book');
        $content = M('BookContent');
        $where['book_id'] = $bookid;
        //$where['web_id'] = $this->website[web_id];
        $info = $book->where($where)->field('book_id,fu_book,book_name')->find();
        $map['fu_book'] = $info[fu_book];
        $map['attribute'] = array('lt', date('Y-m-d H:i:s'));
        $count = $content->where($map)->field('book_id')->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数           
        $content_arr = $content->where($map)->field('content_id,title,num,the_price')->limit($Page->firstRow . ',' . $Page->listRows)->order("num $paixu")->select();
       $Page->setConfig('theme', 
            "<div style=\"margin-top:20px;font-size:12px;\"> 
            <div style=\"float:left;line-height:42px;text-align:right;width:55%;\">
            <span class=\"pager_left\">%upPage%</span>
            <span class=\"pager_conn\">%nowPage%/%totalPage%</span>
            <span class=\"pager_right\">%downPage%</span>
            </div>
            <div style=\"float:left;\">
            <form action=\"\"  method=\"get\" style=\"margin-top:10px;\">
                <span class=\"\">跳到
                <input name=\"p\" type=\"text\" style=\"border: 1px solid #ddd; height: 26px;  width: 30px; text-align: center;\">页
                <input class=\"btnis\" type=\"submit\" value=\"跳转\" style=\"padding: 5px 12px; background-color: #00a19e; color: #fff; border-radius: 3px;\" />
            </form>
            </span>
            </div>
            </div>");
        $show = $Page->show(); // 分页显示输出
        $this->assign('chapterlist', $content_arr);
        $this->assign('page', $show);
        $this->assign('bookinfo', $info);
    }

    //好书推荐
    public function haoshu() {
        $bookpromote = M('NBookPromote');
        $where['promote_id'] = 10;
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img')->limit(4)->order('xu asc')->select();
        $this->assign('potential', $arr);
    }
}
