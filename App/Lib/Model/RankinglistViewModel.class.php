<?php

    class RankinglistViewModel extends ViewModel {

        public $viewFields = array(
            'Book' => array('book_id', 'book_name','upload_img'),
            'BookStatistical' => array('click_day,click_month,click_total,click_weeks', '_on' => 'Book.book_id=BookStatistical.book_id'),
        );

    }