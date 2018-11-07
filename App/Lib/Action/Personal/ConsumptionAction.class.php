<?php

//消费记录
    class ConsumptionAction extends GlobalAction {

        public function index() {
            import('ORG.Util.Page');
            $cont = M('UserConsumerecord');
            $count = $cont->where(array('user_id' => $this->to[user_id]))->count(); // 查询满足要求的总记录数   
            $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数  

            $Page->setConfig('theme', " <span class=\"pager_left\">%upPage%&nbsp;</span> <span class=\"pager_conn\">%nowPage%/%totalPage%</span> <span class=\"pager_right\">&nbsp;%downPage%</span>");
            //内容显示
            $xiaofei = $cont->where(array('user_id' => $this->to[user_id]))->limit($Page->firstRow . ',' . $Page->listRows)->order('time desc')->select();
            // echo $cont->getLastSql();
            // var_dump($xiaofei);
            // $xiaofei['dosomething'] = str_replace('够买', '购买', $xiaofei['dosomething']);
            // if (strpos($xiaofei['dosomething'], "够买")!== false) {

            //     $xiaofei['dosomething'] = str_replace('够买', '购买', $xiaofei['dosomething']);
            // }
            $this->assign('xiaofei', $xiaofei);
            $Page->url ='Personal/Consumption/index/p/';
            // 分页显示输出
            $show = $Page->show();
            $this->assign('page', $show);
            $this->display();
        }

    }
    