var user;
if ($.cookie("user_id")) {
	if ($.cookie("portrait") == 'portrait') {
		img_url = '/Upload/upload_portrait/portraita.jpg';
	} else {
		img_url = $.cookie("portrait");
	}
    user = "<li class=\"touxiang\"><a href=\"/User/info.html\" ><img src=\"" + img_url + "\"/ style=\"border-radius: 50%;overflow: hidden;\" width=\"35\" height=\"35\"></a></li>";
    // user += "<li><a href=\"/Personal/Index/index.html\" >" + $.cookie("pen_name") + "</a></li>";
    document.write(user);
} else {
    user = "<a href=\"/Login/register.html?backUrl=" + window.location.href + "\" style=\" color: #07b1a6;\">注册|</a>";
    user += "<a href=\"/Login/index.html?backUrl=" + window.location.href + "\" style=\" color: #07b1a6;\">登录</a>";
    document.write(user);
}