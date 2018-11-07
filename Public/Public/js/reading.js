$(function () {
    //调用样式
    yangshi();
    //打开菜单
    $("#menu").click(function () {
        $('.zongcaidan').toggle();
    });
    //上一章
    $("#ona").click(function () {
        window.location.href = $("#preid").val();
    });
    //返回作品页
    $("#zuopin").click(function () {
        window.location.href = "/books/" + $("#bookid").val() + ".html";
    });
    //返回目录
    $("#mulus").click(function () {
        window.location.href = "/showclist/" + $("#bookid").val() + ".html";
    });
    //跳转到评论
    $("#pinglun").click(function () {
        window.location.href = "/Message/release/bookid/" + $("#bookid").val() + ".html";
    });
    //跳转到打赏
    $("#dashang").click(function () {
        window.location.href = "/Exceptional/index/book/" + $("#bookid").val() + "/cpi/1.html";
    });
    //收藏
    $("#shou").click(function () {
        if ($.cookie("user_id") != null) {
            $.ajax({
                url: "/Collection/index.html",
                type: "post",
                data: 'bookid=' + $("#bookid").val() + '&bookname=' + booksname,
                success: function (data) {
                    if (data == 1) {
                        alert("收藏成功！");
                    } else {
                       alert("您已收藏！");
                    }
                }
            });
        } else {
            location.href = "/Login/index.html";
        }
    });    
    //选择到底用什么字体大小
    $(".ziti_ziti li").click(function () {
        //遍历删除所有元素的样式
        $(".ziti_ziti li").each(function () {
            $(this).removeClass("on");
        });
        //单独给元素增加样式
        $(this).addClass("on");
        var fontsize = Number($(this).attr("id"));
        $(".content p").css("font-size", fontsize);
        $.cookie("axyx_fontsize", fontsize, {expires: 7, path: '/'});
    });
    //选择背景什么颜色
    $(".ziti_beijing li").click(function () {
//遍历删除所有元素的样式
        $(".ziti_beijing li").each(function () {
            $(this).removeClass("on");
        });
        //单独给元素增加样式
        $(this).addClass("on");
        var beijing = $(this).attr("id");
        backgrounds(beijing);
        $.cookie("axyx_bgcolor", beijing, {expires: 7, path: '/'});
    });
    //遍历用户字体大小
    var sizes = Number($.cookie("axyx_fontsize"));
    $(".ziti_ziti li").each(function () {
        if (sizes == $(this).attr("id")) {
            $(this).addClass("on");
        } else {
            $(this).removeClass("on");
        }
    });
    //遍历用户背景颜色
    var beijing = $.cookie("axyx_bgcolor");
    $(".ziti_beijing li").each(function () {
        if (beijing == $(this).attr("id")) {
            $(this).addClass("on");
        } else {
            $(this).removeClass("on");
        }
    });
});
function yangshi() {
    //是否第一次打开
    if (!$.cookie("axyx_diyi")) {
        $('.zongcaidan').toggle();
        $.cookie("axyx_diyi", 1, {expires: 2, path: '/'});
    }
    //字体大小
    if ($.cookie("axyx_fontsize")) {
        var sizes = Number($.cookie("axyx_fontsize"));
        $(".content p").css("font-size", sizes);
    } else {
        $.cookie("axyx_fontsize", 16, {expires: 7, path: '/'});
    }
    //字体背景
    if ($.cookie("axyx_bgcolor")) {
        backgrounds($.cookie("axyx_bgcolor"));
    } else {
        backgrounds("bai");
        $.cookie("axyx_bgcolor", 'bai', {expires: 7, path: '/'});
    }
    $("body").css("display", "block");
}
//背景颜色到底该怎么处理
function backgrounds(beijing) {
    if (beijing == "bai") {
        $("body").css("background-image", "url(/Public/Wap/images/bg.gif)");
        $(".content p,.title").css("color", "#666");
    } else if (beijing == "hei") {
        $("body").css("background-color", "#000000");
        $("body").css("background-image", "none");
        $(".content p,.title").css("color", "#666666");
    } else {
        $("body").css("background-color", "#f4eed9");
        $("body").css("background-image", "none");
        $(".content p,.title").css("color", "#666666");
    }
}