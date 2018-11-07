<?php

//新书 限免
class TopAction extends LoadingAction {

    public function index($id = 6) {
        $cache = A('Cache');
        $cache->chushi("/Html_Cache/NewWap/" . $this->website[web_id] . "/Topindex/", "Top_index_id_$id");
        $cache->read_cache(); //读取缓存
        if ($id == 1) {
            $title = '全站最畅销';
        } elseif($id == 2) {
            $title = '男生最当红';
        } elseif($id == 3) {
            $title = '女生最言情';
        } elseif($id == 4) {
            $title = '完结大推荐';
        } elseif($id == 10) {
            $title = '新书畅读';
        } else {
            $title = '猜你喜欢';
        }
        $bookpromote = D('NBooktuiView');
        $where['promote_id'] = $id;
        $count = $bookpromote->where($where)->count();
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,author_name')->limit(20)->order('xu asc')->group('book_id')->select();
        $this->assign('arr', $arr);
        $this->assign('title', $title);
        $this->display();
        $cache->create_cache(); //生成缓存   
    }

}
