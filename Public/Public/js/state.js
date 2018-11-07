var user;
if ($.cookie("user_id")) {
    user = "<li class=\"touxiang\"><a href=\"/Personal/Index/index.html\" ><img src=\"/Upload/upload_portrait/" + $.cookie("portrait") + "a.jpg\"/></a></li>";
    user += "<li><a href=\"/Personal/Index/index.html\" >" + $.cookie("pen_name") + "</a></li>";
    document.write(user);
} else {
    user = "<li><a href=\"/Login/register.html?backUrl=" + window.location.href + "\" >注册</a></li>";
    user += "<li><a href=\"/Login/index.html?backUrl=" + window.location.href + "\" >登录</a></li>";
    document.write(user);
}