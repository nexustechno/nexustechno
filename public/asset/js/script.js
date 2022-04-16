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
                var mediasize = 1199;
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
    $("#myModal, #myStatus, #myAddPlayer").modal({
        show: false,
        backdrop: 'static'
    });
});

$('.but_active').click(function () {
    $(this).toggleClass("active");
    $(".but_locked").removeClass("active");
    $(".but_suspend").removeClass("active");
});

$('.but_suspend').click(function () {
    $(this).toggleClass("active");
    $(".but_locked").removeClass("active");
    $(".but_active").removeClass("active");
});

$('.but_locked').click(function () {
    $(this).toggleClass("active");
    $(".but_suspend").removeClass("active");
    $(".but_active").removeClass("active");
});

$("#downline-yes").click(function () {
    $("#downline-table").show();
});

$("#downlind-today").click(function () {
    $("#downline-table").hide();
});

$("#market-yes").click(function () {
    $("#market-table").show();
});

$("#market-today").click(function () {
    $("#market-table").hide();
});

$(document).ready(function () {
    if($('#example').is(":visible")){
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv',
            ]
        });
    }
    if($('#example1').is(":visible")){
        $('#example1').DataTable();
    }
    if($('#example2').is(":visible")){
        $('#example2').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'csv',
                'pdf',
            ]
        });
    }
});

$(".depositebtn").click(function () {
    $(this).addClass("selectD");
    $(".withdrawbtn").removeClass("selectW");
   // $("#fullBtn").removeClass("activeFull");
   // $("#btnEdit0").addClass("activeFull");
    $(".amount-deposit input[type='number']").removeClass("typeW");
});

$(".withdrawbtn").click(function () {
    $(this).addClass("selectW");
    $(".depositebtn").removeClass("selectD");
    //$("#fullBtn").addClass("activeFull");
    //$("#btnEdit0").addClass("activeFull");
    $(".amount-deposit input[type='number']").addClass("typeW");
});

/*$(".creditEdit").click(function () {
    $(".creditEdit").html($(".creditEdit").html() == 'Edit' ? 'Cancel' : 'Edit');
    $(".credit-amount .text-color-blue-light").toggle();
    $(".credit-amount input").toggle();
});*/



