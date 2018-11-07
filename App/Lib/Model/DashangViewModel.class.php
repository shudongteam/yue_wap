<?php

    class DashangViewModel extends ViewModel {

        public $viewFields = array(
            'NUserConsumerecord' => array('user_id', 'book_id','type','dosomething','time', '_type'=>'LEFT'),
            'NUserInfo' => array('pen_name','portrait', '_on' => 'NUserConsumerecord.user_id=NUserInfo.user_id'),
        );

    }