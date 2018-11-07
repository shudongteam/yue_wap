<?php

class WxPayConfig {

    //基本信息
    const APPID = 'wxe7a8aca4ca70d19b';
    const APPSECRET = 'bc900e0e2cdfdad48f508351be990e69';
    const MCHID = '1497773872';
    const KEY = '1A9CA4C36C3C9AFFBB9787F8DF612242';
    //证书目录
    const SSLCERT_PATH = '../cert/apiclient_cert.pem';
    const SSLKEY_PATH = '../cert/apiclient_key.pem';
    //安全验证
    const CURL_PROXY_HOST = "0.0.0.0";
    const CURL_PROXY_PORT = 0;
    //上报信息
    const REPORT_LEVENL = 1;
    //提交地址
    #const Submit = "http://w.ymzww.cn/Personal/Payinterface";
    //反馈地址
    const Notify = "http://pay.ymzww.cn/Notify/weixin/";
    //失败地址
    const Shibai = "http://text.nw.ymzww.cn/Pay/err.html";
    //成功地址
    const Chenggong = "http://text.nw.ymzww.cn/Pay/ok/";
    //检测地址
    const Jiance = "http://text.nw.ymzww.cn/Pay/payresult/trade/";   

}
