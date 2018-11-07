<?php

//排行榜
class RankinglistAction extends LoadingAction {

    public function _empty() {
        //该方法即为空操作
        echo '当前操作不存在';
    }

    //追书榜
    public function tuijian(){
        $cache = A('Cache');
        $guizhe = "zhuishu";
        $cache->chushi("/Html_Cache/NewWap/" . $this->website[web_id] . "/Rankinglist", $guizhe); 
        $arr = $this->get_ranking('vote_total DESC');
        $this->assign('tui', $arr);
        $this->assign('title', "追书榜");
        $this->display();
        $cache->create_cache();
    }
    //订阅榜
    public function addread(){
        $cache = A('Cache');
        $guizhe = "dingyue";
        $cache->chushi("/Html_Cache/NewWap/" . $this->website[web_id] . "/Rankinglist", $guizhe);        
        $cache->read_cache(); //读取缓存  
        $arr = $this->get_ranking('buy_total DESC');
        $this->assign('dingyue', $arr);
        $this->assign('title', "销售榜");
        $this->display();
        $cache->create_cache(); 
    }
    //点击榜
    public function click(){
        $type = isset($_GET['type']) ? $_GET['type'] : 1;
       // echo $type;
        $cache = A('Cache');
        $guizhe = "click_$type";
        $cache->chushi("/Html_Cache/NewWap/" . $this->website[web_id] . "/Rankinglist", $guizhe);        
        $cache->read_cache(); //读取缓存  
        if($type == 1){
            $order = "click_weeks DESC";
        }elseif($type == 2){
            $order = "click_month DESC";
        }elseif($type == 3){
            $order = "click_total DESC";
        }else{
            $order = "click_weeks DESC";
        }
        $arr = $this->get_ranking($order);
      //  print_r($arr);
        $this->assign('arr', $arr);
        $this->assign('type', $type);
        $this->assign('title', "点击榜");
        $this->display();
        $cache->create_cache();  
    }

    private function get_ranking($order) {
        $list = D('NListView'); 
       // $con['cp_name'] = array("eq","阅明");
        $con['is_show'] = 1;
        $con['fu_book'] = array('exp', " = Book.book_id");
        $con['book_id'] = undisplay();
        $arr = $list->where($con)->order($order)->limit(10)->select();
        return $arr;
    }
}
