
function vipvote(){
    //钻石票
    var uid= $("#uid").val();
    if (!uid) {
        window.location.href = "/Login/index.html";
        return;
    }
    $.ajax({
        url: "/Vote/vipvote.html",
        type: "post",
        data: 'bookid=' + $("#bookid").val(),
        success: function (data) {
            if (data == "1") {
                Toast("感谢赠送",3000);
            } else {
                Toast("阅明珠不足请充值赠送", 1000);
            }
        }
    });
}

function dashang(){
    var uid= $("#uid").val();
    if (!uid) {
        window.location.href = "/Login/index.html";
        return;
    }
    var bookid = $('#bookid').val(); 
    var type = $('#type').val();
    var bookname = $('#bookname').val();
    //关闭层
    layer.closeAll();
    //初始化礼物
    $('#type').val(1);
    $.ajax({
        url: "/Exceptional/index/bookid/" + bookid + "/type/" + type + "/bookname/" + bookname,
        type: "get",
        success: function (data) {
            if (data == 1) {
                Toast("礼物赠送成功", 1000);
            } else {
                Toast(data, 1000);
            }
        }
    });
}
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

//收藏方法
function shou(is) {
    var uid= $("#uid").val();
    if (!uid) {
        window.location.href = "/Login/index.html";
        return;
    }
    if (is == 1) {
        $.ajax({
            url: "/Collection/index.html",
            type: "post",
            data: 'bookid=' + $("#bookid").val() + '&bookname=' +$("#bookname").val()+'&save=1',
            success: function (data) {
                if (data == 1) {
                    $("#shouchang_flag").html('取消收藏');
                    $("#shouchang_flag").attr('onClick', 'shou(2)');
                    //$("#genduid").html(parseInt($("#genduid").html()) + 1);
                    // $("#shoucangid").html("<a class=\"lan start\" href=\"javascript:void(0)\" onClick=\"shou(2)\" >-取消收藏</a>");
                } else {
                    Toast(data, 3000);
                }
            }
        });
    } else if (is == 2) {
        $.ajax({
            url: "/Collection/dell.html",
            type: "post",
            data: 'bookid=' + $("#bookid").val() + '&bookname=' +$("#bookname").val()+'&save=1',
            success: function (data) {
                if (data == 1) {
                    $("#shouchang_flag").html('点击收藏');
                    $("#shouchang_flag").attr('onClick', 'shou(1)');
                    //$("#genduid").html(parseInt($("#genduid").html()) - 1);
                    //$("#shoucangid").html("<a class=\"bai start\" href=\"javascript:void(0)\" onClick=\"shou(1)\" >点击收藏</a>");
                } else {
                    Toast(data, 3000);
                    //Toast("系统错误", 1000);
                   // $("#shoucangid").html("<a class=\"bai start\" href=\"javascript:void(0)\" onClick=\"shou(1)\" >点击收藏</a>");
                }
            }
        });
    }

}
$(function () {
    //打赏选择器
    $(document).on("click", ".user-list-item", function () {
        //遍历删除所有元素的样式
        $(".user-list-item").removeClass("on");
        //单独给元素增加样式
        $(this).addClass("on");
        $("#type").val($(this).attr("id"));
    });
});