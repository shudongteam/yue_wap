<?php

class ListViewModel extends ViewModel {

    public $viewFields = array(
        'Book' => array('book_id', 'fu_book', 'book_name', 'author_name'),
        'BookStatistical' => array('click_day', 'click_month', 'click_weeks', 'click_total', '_on' => 'Book.book_id=BookStatistical.book_id'),
        'BookContent' => array('content_id', 'title', 'num','attribute', 'time', '_on' => 'Book.chapter=BookContent.num  and  Book.fu_book=BookContent.fu_book')
    );

}
