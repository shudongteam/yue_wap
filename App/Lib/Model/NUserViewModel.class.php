<?php
   //统计模型
    class NuserViewModel extends ViewModel {

        public $viewFields = array(
            'NUser' => array('user_id','time', 'user_name', 'user_pass', '_type'=>'LEFT'),
            'NUserInfo' => array('pen_name', 'sex', 'portrait', '_on' => 'NUser.user_id=NUserInfo.user_id'),
            // 'NUserMoney' => array('alance', 'money', 'vip_time', '_on' => 'NUser.user_id=NUserMoney.user_id','_type'=>'LEFT'),
            // 'NAgent' => array('pen_name' => 'agent_name', '_on' => 'NUser.agent_id=NAgent.agent_id'),
        );

    }