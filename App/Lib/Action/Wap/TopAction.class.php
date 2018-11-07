<?php

//新书 限免
class TopAction extends LoadAction {

    public function index($id) {
        $cache = A('Cache');
        $cache->chushi("/Html_Cache/" . $this->website[web_id] . "/", "Top_index_$id");
        $cache->read_cache(); //读取缓存
        if ($id == 9) {
            $this->assign('title', "新书推荐");
        } else {
            $this->assign('title', "限时免费");
        }
        $bookpromote = D('BooktuiView');
        $where['BookPromote.web_id'] = $this->website[web_id];
        $where['promote_id'] = $id;
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,author_name')->limit(3)->order('xu asc')->select();
        for ($i = 0; $i < count($arr); $i++) {
            $arr[$i][book_brief] = mb_substr($arr[$i][book_brief], 0, 32, 'utf-8');
        }
        $this->assign('arr', $arr);
        $this->display();
        $cache->create_cache(); //生成缓存   
    }

}
