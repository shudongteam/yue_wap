$(function () {
    var page = 2;
    //加载留言
    $("#message-ajax").click(function () {
        $.ajax({
            url: "/Message/zuijia.html",
            type: "get",
            data: 'bookid=' + $("#bookid").val() + '&page=' + page,
            success: function (data) {
                if (data == 1) {
                    $("#xianshi").html("<a href=\"javascript:;\" class=\"more-comment\">没有内容了...</a>");
                } else {
                    $("#zuijia").append(data);
                    page++;
                }
            }
        });
    });
    $("#tijiao").click(function () {
        if($("#content").val()==''){
            Toast("内容不要空了",1000);
            return ;
        }
            $.ajax({
            url: "/Message/tijiao.html",
            type: "post",
            data: 'bookid=' + $("#bookid").val() + '&content=' + $("#content").val(),
            success: function (data) {
            if(data==1){
                Toast("提交成功",3000);
                window.location.reload();
                $("#content").val("");

            }else{
                Toast(data,1000);
            }
            }
        });            
    });
    
    $("#tucao").click(function () { 
        if($("#content").val()==''){
            Toast("内容不要空了",1000);
            return ;
        }
          $.ajax({
            url: "/Message/huifutijiao.html",
            type: "post",
            data: 'mesgid=' + $("#mesgid").val() + '&content=' + $("#content").val(),
            success: function (data) {
            if(data==1){
                Toast("提交成功",3000);
                window.location.reload();
                $("#content").val("");
            }else{
                Toast(data,1000);
            }
            }
        });    
    });
 });
//自定义弹框
function Toast(msg, duration) {
    duration = isNaN(duration) ? 3000 : duration;
    var m = document.createElement('div');
    m.innerHTML = msg;
    m.style.cssText = "width: 60%;min-width: 150px;opacity: 0.7;height: 30px;color: rgb(255, 255, 255);line-height: 30px;text-align: center;border-radius: 5px;position: fixed;top: 40%;left: 20%;z-index: 999999;background: rgb(0, 0, 0);font-size: 12px;";
    document.body.appendChild(m);
    setTimeout(function () {
        var d = 0.5;
        m.style.webkitTransition = '-webkit-transform ' + d + 's ease-in, opacity ' + d + 's ease-in';
        m.style.opacity = '0';
        setTimeout(function () {
            document.body.removeChild(m)
        }, d * 1000);
    }, duration);
}