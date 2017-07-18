$(function() {

    $('select').change(function () {
        if ($(this).children('option:first-child').is(':selected')) {

            $(this).addClass('placeholder');
        } else {
            $(this).removeClass('placeholder');
        }
    });

});