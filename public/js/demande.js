jQuery(document).ready(function() {
    $('.js-datepicker').datepicker({
    format: 'yyyy-mm-dd',
    language: "fr",
    todayHighlight: true,
    daysOfWeekDisabled: [1,2,3,4,5,0],
        });




    $(window).scroll( function(){

        /* Check the location of each desired element */
        $('.hideme').each( function(i){

            var bottom_of_object = $(this).offset().top + $(this).outerHeight();
            var bottom_of_window = $(window).scrollTop() + $(window).height();

            /* If the object is completely visible in the window, fade it in */
            if( bottom_of_window > bottom_of_object ){

                $(this).animate({'opacity':'1'},1500);

            }

        });
       
            var target = $(".hideme2").offset().top;
            var interval = setInterval(function() {
                if ($(window).scrollTop() >= target) {
                    $(".hideme2").animate({'opacity': '1'}, 1500);
                    clearInterval(interval);
                }
            });
    });
});