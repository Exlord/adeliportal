/**
 * Created with PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/9/14
 * Time: 3:58 PM
 */
$(document).ready(function () {

    $('#admin_nav li.has-child').each(function () {
        $(this).prepend("<span class='arrow'></span>");
    });

    $('#admin_nav').removeClass('no-script');
    $('#admin_nav li').hoverIntent({
        over: function () {
            $(this).children('ul').show(200);
        },
        out: function () {
            $(this).children('ul').hide(300);
        },
        timeout: 50
    });

    $('.language-switcher-box li').hoverIntent(
        function () {
            $(this).children('ul').show(300);
        },
        function () {
            $(this).children('ul').hide(200);
        }
    );
    $('.header, #admin_nav > ul > li > a, #admin_nav > ul > li, #admin_nav > ul > li.has-child > .arrow, #admin_nav, #header-logo', $('.header-sticky-bar')).addClass('sticky-header-transition');
    $(window).scroll(function () {
        compactStickyHeader();
    });
    compactStickyHeader();

    $(document).click(function () {
        closeDropDowns();
    });

    $('body')
        .on('click', '.nav.navbar-nav .dropdown .dropdown-toggle', function (e) {
            e.preventDefault();
            e.stopPropagation();
            closeDropDowns();
            $(this).siblings('.dropdown-menu').toggleClass('open');
            $(this).parent('.dropdown').toggleClass('open');
        })
        .on('click', '.nav.navbar-nav .dropdown .ajax_page_load', function () {
            $(this).blur();
        });

    //,#admin_nav a[href="/fa/admin/contents"]
    $('#admin_nav a[href="/fa/admin/configs"]').siblings('ul').addClass('column-2');

    $(document).on('afterPageLoad', function () {
        gridFilterColumn();
    });
    gridFilterColumn();

});
function closeDropDowns() {
    $('.nav.navbar-nav .dropdown.open')
        .toggleClass('open')
        .children('.dropdown-menu')
        .toggleClass('open');
}
function compactStickyHeader() {
    var scrollTop = $(window).scrollTop();
    var stickyHeader = $('.header-sticky-bar');
    if (!stickyHeader.hasClass('compact') && scrollTop > 5) {
        stickyHeader.addClass('compact');
    }

    if (scrollTop < 20)
        stickyHeader.removeClass('compact');
}

function gridFilterColumn() {
    var filterColumn = $('.grid-wrapper .grid-filter-column');
    var grid = $('.grid-has-filter-column');
    var btn = $("<button type='button' id='gridFilterCollapse' class='btn btn-default btn-xs'><span class='glyphicon glyphicon-cog'></span></button>");
    btn.click(function (e) {
        filterColumn.toggleClass('collapsed');
        grid.toggleClass('filter-collapsed')
    }).appendTo(filterColumn).click();
}