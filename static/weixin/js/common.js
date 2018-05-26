$(function () {
    $(".type-color").each(function (i) {
        alert($(this).html());
        return false;
    });
    //无订单垂直居中
    var winH = $(window).height();
    var titH = $(".order .title").height();
    var tabH = $(".order .tabTit").height();
    var ftH = $(".order .footBar").height();
    $(".noOrder").css('height', winH - titH - tabH - ftH + 'px');

    //选择城市垂直居中
    vertical();

    //训练人数
    $(".form i").click(function () {
        var thisIdx = $(".form i").index($(this));
        $(".form i").removeClass('active');
        $(this).addClass('active');
        $(this).parent().find('input').attr('value', thisIdx + 1);
    });

    //使用优惠券
    $(".floatTicket li").click(function () {
        var i =0 ;
        if($(this).hasClass('active')){
            i =1 ;
        }
        $(".floatTicket li").removeClass('active');
        if(i==0){
            $(this).addClass('active');
        }
    });
    $(".floatTicket h2 a").click(function () {
        var txt = '';
        $(".floatTicket li").each(function () {
            var mycls = $(this).hasClass('active');
            if (mycls) {
                txt = $(this).find('span').html();
            }
        });
        $(".ticket").html(txt);
        $(".floatTicket").hide();
    });
    $(".ticket").click(function () {
        $(".floatTicket").show();
    });

    //评分
    $(".star_41x33 s").click(function () {
        var thisIdx = $(this).index();
        //console.log(thisIdx);
        $(this).parent().children('s').removeClass('active');
        for (var i = 0; i <= thisIdx; i++) {
            $(this).parent().children('s').eq(i).addClass('active');
        }
        ;
        $(this).parent().next('input').attr('value', thisIdx + 1);
    });

    //意见反馈
    $(".feedback textarea").keyup(function () {
        $(this).parent().parent().find('a').hide();
        $(this).parent().parent().find('button').show();
    });

	//小课培训垂直红线高度
	xkpk();

    //图片背景

    imgBg();

    //评分进度条
    $(".progress div").each(function () {
        var val = $(this).parent().find('span').children('i').html();
        //console.log (val);
        $(this).find('s').animate({
            'width': val + '%'
        })
    });

    //加减
    $(".reducePlus").each(function () {
        //减
        var timer = null;
        $(this).children('b').eq(0).on({
            click: function () {
                var oVal = parseInt($(this).next('input').val());
                if (oVal <= 0) {
                    oVal = 0;
                } else {
                    oVal--;
                }
                $(this).next('input').val(oVal);
            },
            touchstart: function () {
                clearInterval(timer)
                var oVal = parseInt($(this).next('input').val());
                var _this = $(this);
                timer = setInterval(function () {
                    console.log(oVal)
                    if (oVal <= 0) {
                        oVal = 0;
                        clearInterval(timer);
                    } else {
                        oVal--;
                    }
                    _this.next('input').val(oVal);
                }, 100);
                return false;
            },
            touchend: function () {
                clearInterval(timer)
            }
        });
        /////////////////////////////////////

        //加
        $(this).children('b').eq(1).on({
            click: function () {
                var oVal = parseInt($(this).prev('input').val());
                if (oVal >= 100) {
                    oVal = 100;
                } else {
                    oVal++;
                }
                $(this).prev('input').val(oVal);
            },
            touchstart: function () {
                clearInterval(timer)
                var oVal = parseInt($(this).prev('input').val());
                var _this = $(this);
                timer = setInterval(function () {
                    console.log(oVal)
                    if (oVal >= 100) {
                        oVal = 100;
                    } else {
                        oVal++;
                    }
                    _this.prev('input').val(oVal);
                }, 100)
                return false;
            },
            touchend: function () {
                clearInterval(timer)
            }
        });
        //输入
        $(this).find('input').keydown(function () {
            var oVal = parseInt($(this).val());
            if (oVal > 100) {
                $(this).val(100);
            } else {
                if (oVal <= 0) {
                    $(this).val(0);
                } else {
                    $(this).val(oVal);
                }
            }
        });
    });
    //筛查选项
    $(".filterBar li a").click(function (e) {
        $(this).parent().children('a').removeClass('active');
        $(this).addClass('active');
        $(this).parent().hide();
    });
    $(".filterBar li span").click(function (e) {
        var status = $(this).next('div').css('display');
        $(".filterBar li div").hide();
        console.log(status)
        if (status == 'none') {
            $(this).next('div').show();
        } else {
            $(this).next('div').hide();
        }
        e.stopPropagation();
    });
    $(document).click(function () {
        $(".select ul").hide();
        $(".filterBar li").find('div').hide();
    });

    //时间段选择
    var hasActive = 0;
    $(".js_dx li").click(function () {
        $(this).parent().find('li').removeClass('active');
        $(this).addClass('active');
        $(".scrollTime li").removeClass('active');
        hasActive = 0;
    });

    //下拉select
   $("div").delegate(".select span","click",function(e){ 
        e.stopPropagation();
        $(".select").css('z-index', 9);
        $(this).parents().css('z-index', 10);
        var status = $(this).next('ul').css('display');
        if (status == 'none') {
            $(this).next('ul').show();
        } else {
            $(this).next('ul').hide();
        }
    });
    $("div").delegate(".select li","click",function(){ 
        var txt = $(this).html();
        $(this).parent().prev('span').find('s').html(txt);
    });


    //js-fix
    var fixH = $(".js-fix").height();
    $(".js-fix").prev().css("padding-bottom", fixH + 'px');
    $('.js-fix').css({
        position: 'fixed',
        bottom: 0
    })

});


function setTab(name, cursel, n) {
    for (i = 1; i <= n; i++) {
        var menu = document.getElementById(name + i);
        var con = document.getElementById("con_" + name + "_" + i);
        menu.className = i == cursel ? "active" : "";
        con.style.display = i == cursel ? "block" : "none";
    }
}


function wordLimit() {
    //限制字数
    var maxNum = 200;
    var txtNum = $(".textarea textarea").val().length;
    $(".textarea textarea").attr('maxlength', maxNum);
    $(".textarea p b").html(maxNum - txtNum);
    $(".textarea textarea").keyup(function () {
        txtNum = parseInt($(this).val().length);
        //console.log(txtNum)
        $(this).next("p").children('b').html(maxNum - txtNum);
    });
}

function is_number(e, obj) {
    var char_code = e.charCode ? e.charCode : e.keyCode;
    if (char_code < 48 && keyCode != 46 || char_code > 57) {
        alert("只允许输入数字0-100的数字！");
        obj.value = '';
        return false;
    } else {
        if (obj.value > 100) {
            obj.value = 100
        }
    }
}


//模拟alert
var alert_str, alert_str2, alert_str3;

function alert(txt) {
    alert_str = '<div class="alert">';
    alert_str += '<div class="alertInner">';
    alert_str += '<h2>';
    alert_str2 = '</h2>';
    alert_str2 += '<a href="javascript:;">好</a>';
    alert_str2 += '</div></div>';
    alert_str3 = alert_str + txt + alert_str2;
    $("body").append(alert_str3);
    $(".alert a").on("click", function () {
        $(this).parent().parent().remove();
    })
}

//选择城市垂直居中
function vertical() {
    $(".chooseCity li").each(function () {
        var thisP = $(this).find('p');
        var pHeight = thisP.height();
        thisP.css({
            marginTop: -pHeight / 2 + 'px'
        })
    });
}

//图片背景

function imgBg() {
    $(".imgbg").each(function () {
        var imgSrc = $(this).find('img').attr('src');
        console.log(imgSrc)
        $(this).find('img').hide();
        $(this).css({
            'background': 'url(' + imgSrc + ') no-repeat center center/cover'
        })
    });
}
;


//===========================
//TAB选项卡 开始
//===========================
function setTab(name, cursel, n) {
    for (i = 1; i <= n; i++) {
        var menu = document.getElementById(name + i);
        var con = document.getElementById("con_" + name + "_" + i);
        hasActive = 0;
        menu.className = i == cursel ? "active" : "";
        con.style.display = i == cursel ? "block" : "none";
    }
}
;

function getQueryString(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) return unescape(r[2]);
	return 1;
};
var tabname = getQueryString('t');

//将php时间戳用js格式化
function trans_php_time_to_str(timestamp, n) {
    var update = new Date(timestamp * 1000);//时间戳要乘1000
    var year = update.getFullYear();
    var month = (update.getMonth() + 1 < 10) ? ('0' + (update.getMonth() + 1)) : (update.getMonth() + 1);
    var day = (update.getDate() < 10) ? ('0' + update.getDate()) : (update.getDate());
    var hour = (update.getHours() < 10) ? ('0' + update.getHours()) : (update.getHours());
    var minute = (update.getMinutes() < 10) ? ('0' + update.getMinutes()) : (update.getMinutes());
    var second = (update.getSeconds() < 10) ? ('0' + update.getSeconds()) : (update.getSeconds());
    if (n == 1) {
        return (year + '-' + month + '-' + day + ' ' + hour + ':' + minute);
    } else if (n == 2) {
        return (year + '-' + month + '-' + day);
    } else {
        return (year + '/' + month + '/' + day + ' ' + hour + ':' + minute + ":" + second);
    }
}

function xkpk() {
    $(".xkpx").each(function () {
        var thisH = $(this).height();
        $(this).find('i').css('height', thisH - 45)
    });
}