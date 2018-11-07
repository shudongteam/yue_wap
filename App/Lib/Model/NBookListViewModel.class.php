<?php

class NBookListViewModel extends ViewModel {

    public $viewFields = array(
        'Book' => array('book_id', 'book_name', 'author_name','words','upload_img','type_id'),
        'BookContent' => array('title', '_on' => 'Book.chapter=BookContent.num  and  Book.fu_book=BookContent.fu_book'),
        'BookStatistical' => array('click_day','click_total','click_month','vote_total','vipvote_total','buy_total','buy_month', '_on' => 'Book.book_id=BookStatistical.book_id'),
    );

}
