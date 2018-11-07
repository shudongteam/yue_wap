$(function(){
  //默认字体大小
  var fontsize = 19;
  //默认背景颜色
  var bglist = ['#F5DCE4', '#DFF5DC', '#38658C', '#F4EED9', '/Public/Wap/images/bg.gif'];

  //加载设置的字体大小
  if ($.cookie("axyx_fontsize")) {
      fontsize = Number($.cookie("axyx_fontsize"));
      $(".content p").css("font-size", fontsize);
      $('.font_size').html(fontsize);
  }
  //加载设置的字体背景
  show_bg_color($.cookie("axyx_bgcolor"));

  //字体缩小
  $(".font_reduce").click(function () {
      fontsize -=1 ;
      if (fontsize >= 12) {
        $(".content p").css("font-size", fontsize);
        $('.font_size').html(fontsize);
      }
      $.cookie("axyx_fontsize", fontsize, {expires: 7, path: '/'});
  });

  //字体放大
  $(".font_add").click(function () {
      fontsize +=1 ;
      if (fontsize <= 32) {
        $(".content p").css("font-size", fontsize);
        $('.font_size').html(fontsize);
      }
      $.cookie("axyx_fontsize", fontsize, {expires: 7, path: '/'});
  });
  //字体恢复默认
  $(".font_default").click(function () {
      fontsize = 19 ;
      $(".content p").css("font-size", fontsize);
      $('.font_size').html(fontsize);
      $.cookie("axyx_fontsize", fontsize, {expires: 7, path: '/'});
  });

  //背景颜色选择
  $(".t_menu_bg span:not('.t_menu_bg span:first')").click(function(){
      $('.t_menu_bg span').removeClass('t_on_background');
      $(this).addClass('t_on_background');
      var index = $(this).index() -1;
      //清除颜色
      $(".all2").removeAttr("style");
      //还原字体颜色
      $(".content p, .p_dashang").css("color", "#666");
      $(".y_title, .y_book_name").css("color", "#636164");
      if (index == 4) {
          $(".all2").css("background-image", "url(/Public/Wap/images/bg.gif)");
      } else {
          if (index == 2) {
            $(".content p, .p_dashang, .y_title, .y_book_name").css("color", "#e7e7e7");
          }
          $(".all2").css("background-color", bglist[index]);
      }
      $.cookie("axyx_bgcolor", index, {expires: 7, path: '/'});
      //恢复成夜间按钮
      $('.detail_yz').css("background-image", "url(/Public/NewWap/images/t_yj.png)").html('夜间');
      //清空上一次颜色
      $.removeCookie("last_axyx_bgcolor", {expires: -1, path: '/'});
  });

  //夜间/日间模式
  $('.detail_yz').click(function(){ 
      var bg = 5;
      var ryj_img = '/Public/NewWap/images/t_yj.png';
      var text = '夜间';
      if ($.cookie("axyx_bgcolor") == 5) {
        //日间模式
        bg = $.cookie("last_axyx_bgcolor") ? $.cookie("last_axyx_bgcolor") : 4;
        // bg = 4;
        show_bg_color($.cookie("last_axyx_bgcolor"));
        // show_bg_color(bg);
      } else {
          //夜间模式
          $(".all2").removeAttr("style");
          $(".content p, .p_dashang, .y_title, .y_book_name").css("color", "#838383");
          $(".all2").css("background-color", "#333");
          ryj_img = '/Public/NewWap/images/t_rj.png';
          text = '日间';
          //记录上一次样式
          $.cookie("last_axyx_bgcolor", $.cookie("axyx_bgcolor"), {expires: 7, path: '/'});
      }
      $('.detail_yz').css("background-image", "url("+ryj_img+")").html(text);
      $.cookie("axyx_bgcolor", bg, {expires: 7, path: '/'});
  });

  //点击屏幕中间出现 菜单
  var cen = 0;
  $('.c_middle').click(function () {
    if ($('.layui-layer-shade').length > 0) {
      layer.close(cen);
      return;
    }
    cen = layer.open({
        type: 1,
        title: false,
        offset: 'lb',
        skin: 'layui-layer-demo', //样式类名
        closeBtn: 0, //不显示关闭按钮
        anim: 2,
        area: '100%',
        shadeClose: true, //开启遮罩关闭
        content: $('.t_menu')
    });
  });

  //打赏
  $('.p_dashang_img').click(function () {
    layer.open({
      type: 1,
      title: ['打赏', 'text-align:center;padding:0;'],
      offset: 'lb',
      skin: 'layui-layer-demo', //样式类名
      closeBtn: 0, //不显示关闭按钮
      anim: 2,
      area: '100%',
      shadeClose: true, //开启遮罩关闭
      content: '<div class="con_tishi"><div class="mod-content"><div class="user-list"><a href="javascript:;"class="user-list-item on"id="1"><span class="img"><img src="/Public/Gift/jinbi.png"></span><span class="number">金币</span><span class="number">100阅读币</span></a><a href="javascript:;"class="user-list-item"id="2"><span class="img"><img src="/Public/Gift/hongjiu.png"></span><span class="number">红酒</span><span class="number">200阅读币</span></a><a href="javascript:;"class="user-list-item"id="3"><span class="img"><img src="/Public/Gift/dangao.png"></span><span class="number">蛋糕</span><span class="number">500阅读币</span></a><a href="javascript:;"class="user-list-item"id="4"><span class="img"><img src="/Public/Gift/qiche.png"></span><span class="number">汽车</span><span class="number">1000阅读币</span></a><a href="javascript:;"class="user-list-item"id="5"><span class="img"><img src="/Public/Gift/feiji.png"></span><span class="number">飞机</span><span class="number">5000阅读币</span></a><a href="javascript:;"class="user-list-item"id="6"><span class="img"><img src="/Public/Gift/tianshi.png"></span><span class="number">天使</span><span class="number">10000阅读币</span></a></div><div class="clear"></div><div class="supportform"><div class="mt15"><input type="submit"value="确认打赏"class="btn01" onclick="dashang()"/></div></div></div></div>'
    });
  });

  function  show_bg_color(bgcolor){
      if (bgcolor == 0 || bgcolor == 1 || bgcolor == 3) {
          $(".all2").css("background-color", bglist[bgcolor]);
      } else if (bgcolor == 2) {
          $(".content p, .p_dashang, .y_title, .y_book_name").css("color", "#e7e7e7");
          $(".all2").css("background-color", bglist[bgcolor]);
      } else if (bgcolor == 4) {
          $(".all2").css("background-image", "url(/Public/Wap/images/bg.gif)");
      } else if (bgcolor == 5) {
          $(".content p, .p_dashang, .y_title, .y_book_name").css("color", "#838383");
          $(".all2").css("background-color", "#333");
          //恢复成日间按钮
          $('.detail_yz').css("background-image", "url(/Public/NewWap/images/t_rj.png)").html('日间');
      } else {
        $(".all2").css("background-image", "url(/Public/Wap/images/bg.gif)");
      }
      // 选中的背景颜色加"√"
      $('.t_menu_bg span').removeClass('t_on_background');
      var ss;
      if ($.cookie("last_axyx_bgcolor")) {
          //存在上一次 背景颜色
           ss = parseInt($.cookie("last_axyx_bgcolor")) + 1;
      } else {
          ss = typeof(bgcolor) == 'undefined' ? 5 : parseInt(bgcolor) + 1;
      }
      if (ss > 5) {
        ss = 5;
      }
      $('.t_menu_bg span').eq(ss).addClass('t_on_background');
  }
});  