$(function () {
    //登录
    $("#submits").click(function () {
        var username = $("#username").val();
        var userpass = $("#userpass").val();
        if (username == '') {
            Toast("账户不可为空", 1000);
            $("#username").focus();
            return false;
        }
        if (userpass == '') {
            Toast("密码不可未空", 1000);
            $("#userpass").focus();
            return false;
        }
        $.ajax({
            url: "/Login/login.html",
            type: "post",
            data: 'username=' + username + '&userpass=' + userpass,
            success: function (data) {
                if (data == 1) {
                    var ursl = getUrlParam('backUrl');
                    if (ursl) {
                        location.href = ursl;
                    } else {
                        location.href = '/';
                    }
                } else {
                    Toast(data, 1000);
                }
            }
        });
    });
    //注册
    $("#regsubmits").click(function () {
        var username = $("#username").val();
        var penname = $("#penname").val();
        var userpass = $("#userpass").val();
        if (username == '') {
            Toast("账户不可为空", 1000);
            $("#username").focus();
            return false;
        }
        if (penname == '') {
            Toast("昵称不可未空", 1000);
            $("#penname").focus();
            return false;
        }
        if (userpass == '') {
            Toast("密码不可未空", 1000);
            $("#userpass").focus();
            return false;
        }
        $.ajax({
            url: "/Login/registers.html",
            type: "post",
            data: 'username=' + username + '&penname=' + penname + '&userpass=' + userpass,
            success: function (data) {
                if (data == 1) {
                    var ursl = getUrlParam('backUrl');
                    if (ursl) {
                        location.href = ursl;
                    } else {
                        location.href = '/';
                    }
                } else {
                    Toast(data, 1000);
                }
            }
        });
    });
    //修改密码
    $("#gengxin").click(function () {
        var oldpassword = $("#oldpassword").val();
        var password = $("#password").val();
        var passwords = $("#passwords").val();
        if (oldpassword == '') {
            Toast("请输入原密码", 1000);
            $("#oldpassword").focus();
            return false;
        }
        if (password == '') {
            Toast("请输入新密码", 1000);
            $("#password").focus();
            return false;
        }
        if (password != passwords) {
            Toast("两次密码不一致", 1000);
            $("#passwords").focus();
            return false;
        }
        $.ajax({
            url: "/Login/pass.html",
            type: "post",
            data: 'oldpassword=' + oldpassword + '&password=' + password,
            success: function (data) {
                if (data == 1) {
                    Toast("修改成功请重新登录", 1000);
                    location.href = "/";
                } else {
                    Toast(data, 1000);
                }
            }
        });
    });
});