$(function() {

    // HIDE MENU ON SCROLL

    var prev = 0;
    var $window = $(window);
    var navbar = $('.navbar');

    $window.on('scroll', function(){
        var scrollTop = $window.scrollTop();
        navbar.toggleClass('hide-menu', scrollTop > prev);
        $('.float-menu').toggleClass('fix-top', scrollTop > prev);
        // $('.header').toggleClass('fix-top', scrollTop > prev);
        //
        // $('.fix-top').animate({
        //     top: "0"
        // }, 1000);

        prev = scrollTop;
    });

});
