$(function () {
    var page = 2;
    //展开
    $(".text_spread").click(function () {
        $(".text_spread").css("display", "none");
        $(".text_flod").css("display", "block");
        $(".jianjie").css("height", "auto");
    });
    //影藏
    $(".text_flod").click(function () {
        $(".text_flod").css("display", "none");
        $(".text_spread").css("display", "block");
        $(".jianjie").css("height", "100px");
    });

    //加载留言
    $("#message-ajax").click(function () {
        $.ajax({
            url: "/Message/index.html",
            type: "get",
            data: 'bookid=' + $("#bookid").val() + '&page=' + page,
            success: function (data) {
                if (data == 1) {
                    $("#message-ajax").remove();
                    $("#zuijia").append("<a href=\"javascript:;\" class=\"more-comment\">没有内容了...</a>");
                } else {
                    $("#zuijia").append(data);
                    page++;
                }
            }
        });
    });
    //鲜花
    $("#vote").click(function () {
        if ($.cookie("user_id") != null) {
            $.ajax({
                url: "/Vote/vote.html",
                type: "post",
                data: 'bookid=' + $("#bookid").val(),
                success: function (data) {
                    if (data != 999) {
                        $(".tui").html(parseInt($(".tui").html()) + 1);
                        Toast("鲜花赠送成功,剩余" + data + "票", 1000);
                    } else {
                        Toast("鲜花不足", 1000);
                    }
                }
            });
        } else {
            location.href = "/Login/index.html";
        }
    });
    //钻石票
    $("#vipvote").click(function () {
        if ($.cookie("user_id") != null) {
            $.ajax({
                url: "/Vote/vipvote.html",
                type: "post",
                data: 'bookid=' + $("#bookid").val(),
                success: function (data) {
                    if (data != 999) {
                        $(".zuan").html(parseInt($(".zuan").html()) + 1);
                        Toast("红钻赠送成功,剩余" + data + "颗", 1000);
                    } else {
                        Toast("红钻不足充值赠送", 1000);
                    }
                }
            });
        } else {
            alert("请先登录");
        }
    });
    //打赏选择器
    $(".user-list a").click(function () {
        //遍历删除所有元素的样式
        $(".user-list a").each(function () {
            $(this).removeClass("on");
        });
        //单独给元素增加样式
        $(this).addClass("on");
        $("#cpi").val($(this).attr("id"));
    });
    //打赏
    $("#dashang").click(function () {
        var num = $("#num").val();
        if (num == '') {
            Toast("数量不可未空", 1000);
            $("#num").focus();
            return false;
        }
        $.ajax({
            url: "/Exceptional/add/book/" + $("#book").val() + "/cpi/" + $("#cpi").val(),
            type: "post",
            data: 'num=' + num + '&message=' + $("#message").val(),
            success: function (data) {
                if (data == 1) {
                    Toast("礼物赠送成功", 1000);
                } else {
                    Toast(data, 1000);
                }
            }
        });

    });
    //发表评论
    $("#submits").click(function () {
        var title = $("#title").val();
        var content = $("#content").val();
        if ($.trim(title) == '') {
            Toast("标题不可为空", 1000);
            $("#title").focus();
            return false;
        }
        if ($.trim(content) == '') {
            Toast("内容不可为空", 1000);
            $("#content").focus();
            return false;
        }
        var flag = true;
        $.ajax({
                url: "/Public/check_keyword",
                type: "post",
                async:false,
                data: {content:content, title:title},
                success: function (data) {
                    if (data == "false") {
                        Toast("评论含有敏感词,请文明发言");
                        flag = false;
                    }
                }
            }); 
            if (!flag) {
                return false;
            }
        $.ajax({
            url: "/Message/tijiao/bookid/" + $("#bookid").val(),
            type: "post",
            data: 'title=' + title + '&content=' + content,
            success: function (data) {
                if (data == 1) {
                    Toast("发表成功", 1000);
                    $("#title").val("");
                    $("#content").val("");
                } else {
                    Toast(data, 1000);
                }

            }
        });
    });
    //显示影藏
    $("#zi-ajax").click(function () {
        if ($.cookie("user_id") != null) {
            $("#zikuang").css("display", "block");
            $("#zi-ajax").css("display", "none");
        } else {
            location.href = "/Login/index.html";
        }
    });
    //回复内容
    $("#qurenhuifu").click(function () {
        var content = $("#content").val();
        if ($.trim(content) == '') {
            Toast("内容不可为空", 1000);
            $("#content").focus();
            return false;
        }
        var flag = true;
        $.ajax({
                url: "/Public/check_keyword",
                type: "post",
                async:false,
                data: {content:content},
                success: function (data) {
                    if (data == "false") {
                        Toast("评论含有敏感词,请文明发言");
                        flag = false;
                    }
                }
            }); 
        if (!flag) {
            return false;
        }

        $.ajax({
            url: "/Message/replys/id/" + $("#ids").val(),
            type: "post",
            data: 'bookid=' + $("#bookid").val() + '&content=' + content,
            success: function (data) {
                if (data == 1) {
                    $("#content").val("");
                    var mydate = new Date();
                    var t = mydate.toLocaleString();
                    var neirong = "<div class=\"comment-item\">";
                    neirong += " <div class=\"comment-content\">" + content + "</div>";
                    neirong += "      <div class=\"wrap-comment-author\"> <span>" + $.cookie("pen_name") + "</span><span>" + t + "</span> </div>";
                    neirong += "</div>";
                    $("#zuijia").prepend(neirong);
                    $("#zikuang").css("display", "none");
                    $("#zi-ajax").css("display", "block");
                } else {
                    Toast(data, 1000);
                }
            }
        });
    });
});
//收藏方法
function shou(is) {
    if (is == 1) {
        if ($.cookie("user_id") != null) {
            $.ajax({
                url: "/Collection/index.html",
                type: "post",
                data: 'bookid=' + $("#bookid").val() + '&bookname=' + $(".right h2").html(),
                success: function (data) {
                    if (data == 1) {
                        $("#genduid").html(parseInt($("#genduid").html()) + 1);
                        $("#shoucangid").html("<a class=\"lan start\" href=\"javascript:void(0)\" onClick=\"shou(2)\" >-取消收藏</a>");
                    } else {
                        Toast("该书您已收藏", 1000);
                    }
                }
            });
        } else {
            location.href = "/Login/index.html";
        }
    } else if (is == 2) {
        if ($.cookie("user_id") != null) {
            $.ajax({
                url: "/Collection/dell.html",
                type: "post",
                data: 'bookid=' + $("#bookid").val(),
                success: function (data) {
                    if (data == 1) {
                        $("#genduid").html(parseInt($("#genduid").html()) - 1);
                        $("#shoucangid").html("<a class=\"bai start\" href=\"javascript:void(0)\" onClick=\"shou(1)\" >点击收藏</a>");
                    } else {
                        Toast("系统错误", 1000);
                    }
                }
            });
        } else {
            location.href = "/Login/index.html";
        }
    }

}

