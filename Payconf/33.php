<?php
                class WxPayConfig {
                    //基本信息
                    const APPID = 'wx458729fd34adecf1';
                    const APPSECRET = '9654e0ce321ca9bf659f28f17956784e';
                    const MCHID = '1315396101';
                    const KEY = '22222222222222222222222222222222';
                    //证书目录
                    const SSLCERT_PATH = '../cert/apiclient_cert.pem';
                    const SSLKEY_PATH = '../cert/apiclient_key.pem';
                    //安全验证
                    const CURL_PROXY_HOST = '0.0.0.0';
                    const CURL_PROXY_PORT = 0;
                    //上报信息
                    const REPORT_LEVENL = 1;
                    //提交地址
                    const Submit = 'http://w.ymzww.cn/Personal/Payinterface'; 
                    //反馈地址
                    const Notify = 'http://danghong.ymzww.cn/Personal/Notify/weixin/';
                    //失败地址
                    const Shibai = 'http://danghong.ymzww.cn/Personal/Pay/index.html';   
                    //成功地址
                    const Chenggong = 'http://danghong.ymzww.cn/Personal/Payto/ok/trade/';   
                    //检测地址
                    const Jiance = 'http://danghong.ymzww.cn/Personal/Payinterface/payresult/trade/';   

                }