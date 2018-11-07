<?php

    class NRankinglistViewModel extends ViewModel {

        public $viewFields = array(
            'Book' => array('book_id','fu_book','book_name','upload_img','author_name','type_id','book_brief','is_show','gender','web_id'),
            'BookStatistical' => array('click_day','click_total','click_month','vote_total','vipvote_total', '_on' => 'Book.book_id=BookStatistical.book_id')
        );

    }