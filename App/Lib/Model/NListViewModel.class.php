<?php

class NListViewModel extends ViewModel {

    public $viewFields = array(
        'Book' => array('book_id', 'fu_book', 'book_name', 'author_name','words','gender','is_show','state','upload_img','book_brief','type_id','cp_name'),
        'BookStatistical' => array('collection_total','click_day', 'click_month', 'click_weeks', 'click_total','buy_total','buy_day','buy_weeks','vote_total','vipvote_total', '_on' => 'Book.book_id=BookStatistical.book_id'),
        'BookContent' => array('content_id', 'title', 'num', 'time', '_on' => 'Book.chapter=BookContent.num  and  Book.fu_book=BookContent.fu_book')
    );

}
