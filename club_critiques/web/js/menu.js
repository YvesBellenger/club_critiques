$(function() {

    // HIDE MENU ON SCROLL

    var prev = 0;
    var $window = $(window);
    var navbar = $('.navbar');

    $window.on('scroll', function(){
        var scrollTop = $window.scrollTop();
        navbar.toggleClass('hide-menu', scrollTop > prev);
        $('.float-menu').toggleClass('fix-top', scrollTop > prev);

        prev = scrollTop;
    });

});
