<?php

//项目配置文件
return array(
    'APP_GROUP_LIST' => 'Wap,Personal,NewWap',
    'DEFAULT_GROUP' => 'Wap', //默认分组
    'DEFAULT_MODULE' => 'Index', //默认模块
    'URL_MODEL' => '2', //URL模式
    'SESSION_AUTO_START' => true, //是否开启session
    'TMPL_FILE_DEPR' => '_', //模板文件MODULE_NAME与ACTION_NAME之间的分割符
    'DB_TYPE' => 'mysql', // 数据库类型
    'DB_HOST' => '101.132.142.46', // 服务器地址
    'DB_NAME' => 'newhezuo', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => 'shengwen!123', // 密码
    'DB_PORT' => '3306', // 端口    
    'DB_PREFIX' => 'hezuo_', // 数据库表前缀
    'CON_TIME' => 3600, //作品内容内置缓存时间 
    'TMPL_L_DELIM' => '<{',
    'TMPL_R_DELIM' => '}>',
    'ALL_ps' => 'hezuo',//加密
    // 'SHOW_PAGE_TRACE' => true,//调试框
    'TMPL_STRIP_SPACE' => FALSE, //模版打印不去空格
    'APP_SUB_DOMAIN_DEPLOY' => 1, // 开启子域名配置
    'APP_SUB_DOMAIN_RULES' => array(
           '.nw.' => array('NewWap/'),
    ),        
    'URL_ROUTER_ON' => true, //开启路由重写功能
    'URL_ROUTE_RULES' => array(//重新规则
        'books/:bookid' => 'Books/Index',
        'showclist/:bookid' => 'Showclist/Index',
        'chapter/:book/:num' => 'Chapter/Index',
        'chaptertwo/:book/:num' => 'Chaptertwo/Index',   
        'chaptertest/:book/:num' => 'Chaptertest/Index',   
    ),
    //支付宝信息
    'alipay' => array('partner' => '2088511392123573',
        'seller_id' => '2088511392123573',
        'key' => 'pu6kmrhc5yazog8um3pz73sa6kjz5wfb',
        'sign_type' => strtoupper('MD5'),
        'input_charset' => strtolower('utf-8'),
        'cacert' => getcwd() . '\\cacert.pem',
        'transport' => 'http',
        'payment_type' => "1",
        'service' => "alipay.wap.create.direct.pay.by.user",
    )
);
