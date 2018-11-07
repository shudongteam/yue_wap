<?php

//消费记录
    class ConsumptionAction extends GlobalAction {

        public function index() {
            header("Content-type:text/html;charset=utf-8");
            import('ORG.Util.Page');
            $user_money = M('n_user_money')->where(array('user_id' => $_COOKIE['user_id']))->find();
            $cont = M('n_user_consumerecord');
            $count = $cont
            ->where(array('user_id' => $_COOKIE['user_id']))
            ->count();  
            $Page = new Page($count, 20); // 实例化分页类 传入总记录数和每页显示的记录数  
             $Page->setConfig('theme', 
            "<div style=\"margin-top:20px\"> 
            <div style=\"line-height:42px;width:40%;text-align:right\">
            <span style=\"width:auto;\" class=\"pager_left\">%upPage%</span>
            <span style=\"width:auto;\" class=\"pager_conn\">%nowPage%/%totalPage%</span>
            <span style=\"width:auto;\" class=\"pager_right\">%downPage%</span>
            </div>
            <div style=\"\">
            <form action=\"\"  method=\"get\" style=\"margin-top:10px;\">
                <span style=\"width:auto;\">&nbsp;跳到
                <input name=\"p\" type=\"text\" style=\"border: 1px solid #ddd; height: 26px;  width: 34px; text-align: center;\">页
                <input class=\"btnis\" type=\"submit\" value=\"跳转\" style=\"padding: 5px 12px; background-color: #00a19e; color: #fff; border-radius: 3px;\" />
                </span>
            </form>
            </div>
            </div>");
            $show = $Page->show();
            $xiaofei = $cont
            ->where(array('user_id' => $_COOKIE['user_id']))
            ->limit($Page->firstRow . ',' . $Page->listRows)->order('time desc')->select();
            // echo $cont->getLastSql();
             //var_dump($xiaofei);
            // $xiaofei['dosomething'] = str_replace('够买', '购买', $xiaofei['dosomething']);
            // if (strpos($xiaofei['dosomething'], "够买")!== false) {

            //     $xiaofei['dosomething'] = str_replace('够买', '购买', $xiaofei['dosomething']);
            // }
            $this->assign('page', $show);
            $this->assign('title', "消费记录");
            $this->assign('xiaofei', $xiaofei);
            $this->assign('user_money', $user_money);
            $this->display();
        }

    }
    