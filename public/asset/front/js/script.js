jQuery(document).ready(function ($) {
    $('#pills-tab[data-mouse="hover"] a').hover(function () {
        $(this).tab('show');
    });
    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        var target = $(e.relatedTarget).attr('href');
        $(target).removeClass('active');
    })
});

(function ($) {
    $.fn.menumaker = function (options) {
        var cssmenu = $(this),
            settings = $.extend({
                format: "dropdown",
                sticky: false
            }, options);
        return this.each(function () {
            $(this).find(".button").on('click', function () {
                $(this).toggleClass('menu-opened');
                var mainmenu = $(this).next('ul');
                if (mainmenu.hasClass('open')) {
                    mainmenu.slideToggle().removeClass('open');
                } else {
                    mainmenu.slideToggle().addClass('open');
                    if (settings.format === "dropdown") {
                        mainmenu.find('ul').show();
                    }
                }
            });
            cssmenu.find('li ul').parent().addClass('has-sub');
            multiTg = function () {
                cssmenu.find(".has-sub").prepend('<span class="submenu-button"></span>');
                cssmenu.find('.submenu-button, .has-sub a').on('click', function () {
                    if ($(this).siblings('ul').hasClass('open')) {
                        $(this).siblings('ul').removeClass('open').slideToggle();
                    } else {
                        $(this).siblings('ul').addClass('open').slideToggle();
                    }
                });
            };
            if (settings.format === 'multitoggle') multiTg();
            else cssmenu.addClass('dropdown');
            if (settings.sticky === true) cssmenu.css('position', 'fixed');
            resizeFix = function () {
                var mediasize = 992;
                if ($(window).width() > mediasize) {
                    cssmenu.find('ul').show();
                }
                if ($(window).width() <= mediasize) {
                    cssmenu.find('ul').hide().removeClass('open');
                }
            };
            resizeFix();
            return $(window).on('resize', resizeFix);
        });
    };
})(jQuery);

(function ($) {
    $(document).ready(function () {
        $("#cssmenu").menumaker({
            format: "multitoggle"
        });
    });
})(jQuery);

$(document).ready(function () {
    $("#myLoginModal, #myClickBet").modal({
        show: false,
        backdrop: 'static'
    });
});

if ($('.home-carousel').length) {
    $('.home-carousel').owlCarousel({
        loop: true,
        margin: 0,
        items: 1,
        nav: false,
        smartSpeed: 500,
        autoplay: true,
        navText: ['', ''],
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 1
            },
            600: {
                items: 1
            },
            800: {
                items: 1
            },
            1024: {
                items: 1
            }
        }
    });
}


if ($('.andar-carousel').length) {
    $('.andar-carousel').owlCarousel({
        loop: false,
        margin: 0,
        items: 4,
        nav: true,
        dots: false,
        smartSpeed: 500,
        autoplay: false,
        navText: ['‹', '›'],
        responsive: {
            0: {
                items: 4
            },
            480: {
                items: 4
            },
            600: {
                items: 4
            },
            800: {
                items: 4
            },
            1024: {
                items: 4
            }
        }
    });
}

if ($('.bahar-carousel').length) {
    $('.bahar-carousel').owlCarousel({
        loop: false,
        margin: 0,
        items: 4,
        nav: true,
        dots: false,
        smartSpeed: 500,
        autoplay: false,
        navText: ['‹', '›'],
        responsive: {
            0: {
                items: 4
            },
            480: {
                items: 4
            },
            600: {
                items: 4
            },
            800: {
                items: 4
            },
            1024: {
                items: 4
            }
        }
    });
}

$('label[data-target="#myClickBet"]').click(function () {
    $("#bet_stake").show("");
});

$('#editbtn').click(function () {
    $("#editbet_stake").show("");
});

$('#save').click(function () {
    $("#editbet_stake").hide("");
});

$('.betbtn1').click(function () {
    $(this).toggleClass("active");
    $(".betbtn2, .betbtn3, .betbtn4").removeClass("active");
});

$('.betbtn2').click(function () {
    $(this).toggleClass("active");
    $(".betbtn1, .betbtn3, .betbtn4").removeClass("active");
});

$('.betbtn3').click(function () {
    $(this).toggleClass("active");
    $(".betbtn2, .betbtn1, .betbtn4").removeClass("active");
});

$('.betbtn4').click(function () {
    $(this).toggleClass("active");
    $(".betbtn2, .betbtn3, .betbtn1").removeClass("active");
});

$('.edit_stake').click(function () {
    $("#stake_save").show("");
    $("#stake_edit").hide("");
});

$('.okbtn').click(function () {
    $("#stake_edit").show("");
    $("#stake_save").hide("");
});

$(document).ready(function () {
    $('.seetings_btn').click(function () {
        $(".setting-popup").toggle("");
    });
});

$('.period_date1').datepicker({
    dateFormat: "yy-mm-dd"
});
$('.period_date2').datepicker({
    dateFormat: "yy-mm-dd"
});

$('.period_date3').datepicker({
    dateFormat: "yy-mm-dd"
});
$('.period_date4').datepicker({
    dateFormat: "yy-mm-dd"
});
if ($('.bahar-carousel2').length) {
    $('.bahar-carousel2').owlCarousel({
        loop: false,
        margin: 5,
        items: 13,
        nav: true,
        dots: false,
        autoWidth: true,
        smartSpeed: 500,
        autoplay: false,
        navText: ['‹', '›'],
        responsive: {
            0: {
                items: 6
            },
            480: {
                items: 6
            },
            600: {
                items: 13
            },
            800: {
                items: 13
            },
            1024: {
                items: 13
            }
        }
    });
}

$("body").on('click','#openBetsBtn',function () {
    $("#openBetsLeftSide").addClass('sideopen');
});

$(".open_bets_leftside .side_wrap .side_head a.close").click(function () {
    $("#openBetsLeftSide").removeClass('sideopen');
});

$(".res_search").click(function () {
    $("#searchWrap").show('');
});

$("#serback").click(function () {
    $("#searchWrap").hide('');
});

$('#sedit').click(function () {
    $("#ssave_setting").show("");
    $("#sedit_setting").hide("");
});

$('#sok').click(function () {
    $("#sedit_setting").show("");
    $("#ssave_setting").hide("");
});

$("body").on('click','#settingsOpen',function () {
    $("#settingDiv").addClass('sideopen');
});

$(".open_bets_leftside .side_wrap .side_head a.close, .canceldset").click(function () {
    $("#settingDiv").removeClass('sideopen');
});

$("#fancyinfo").click(function () {
    $("#fancypopupinfo").show('');
});

$("#fancyinfo_close").click(function () {
    $("#fancypopupinfo").hide('');
});

// $('#pinrisk').click(function () {
//     $(this).toggleClass("active");
// });
