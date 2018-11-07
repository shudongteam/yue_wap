<?php

//排行榜
class RankinglistAction extends LoadAction {

    public function _empty() {
        //该方法即为空操作
        echo '当前操作不存在';
    }

    public function index() {
        $cache = A('Cache');
        $cache->chushi("/Html_Cache/" . $this->website[web_id] . "/", 'Rankinglist');
        $cache->read_cache(); //读取缓存        
        $this->dingyue();
        $this->dianji();
        $this->collection();
        $this->display();
        $cache->create_cache(); //生成缓存          
    }

    //订阅
    public function dingyue() {
        //模型
        $list = D('RankinglistView');
        //$con['gender'] = 2;
        $con['is_show'] = 1;
        $con['web_id'] = $this->website[web_id];
        $con['fu_book'] =  block_book();
        //日
        $arr = $list->where($con)->field('book_id,book_name,upload_img')->order('buy_day DESC')->limit(10)->select();
        $this->assign('dingyue', $arr);
    }

    //点击
    public function dianji() {
        //模型
        $list = D('RankinglistView');
        //$con['gender'] = 2;        
        $con['is_show'] = 1;
        $con['web_id'] = $this->website[web_id];
        $con['fu_book'] =  block_book();
        //日
        $arr = $list->where($con)->field('book_id,book_name,upload_img')->order('click_total DESC')->limit(10)->select();
        $this->assign('dianji', $arr);
    }

    //收藏
    public function collection() {
        //模型
        $list = D('RankinglistView');
        //$con['gender'] = 2;        
        $con['is_show'] = 1;
        $con['web_id'] = $this->website[web_id];
        $con['fu_book'] =  block_book();
        //日
        $arr = $list->where($con)->field('book_id,book_name,upload_img')->order('collection_day DESC')->limit(10)->select();
        $this->assign('collection', $arr);
    }

    // //屏蔽的ID
    // private function undisplay(){
    //     return array('not in','10277,6326,7967,4111,9576,6444,6297,9535,5787,9920,10343,99605,78579,78569,99597,10210');
    // }
}
