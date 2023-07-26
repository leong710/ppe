AOS.init();
$(function(){
    // $('.nav-collapse').hide();
    // $('.nav-toggle').click(function(){
        // $('.nav-collapse').toggle(600);
        // $('.nav-collapse').slideToggle();
    //     $('.nav-collapse').toggleClass('active');
    // })
    // $('.modal-btn').click(function(){
    //     let modal = $(this).attr('href');
    //     $(modal).fadeIn();
    //     return false;
    // })
    // $('.modal-close').click(function(){
    //     $('.modal').fadeOut();
    //     return false;
    // })
    // $('.nav-menu').find('a').click(function(e){
    //     if($(this).data('scroll')){
    //         let scroll = $(this).data('scroll');
    //         let offset = $(scroll).offset().top;
    //         $('body').animate({
    //             scrollTop: offset
    //         })
    //         e.preventDefault();
    //     }
    // })
    $('#gotop').click(function(){
        $('html,body').animate({
            scrollTop: 0
        })
    })
    $('#gotop').hide();
    $(window).scroll(function(){
        let h = $(window).scrollTop();
        if(h > 800){
            $('#gotop').fadeIn();
        }else{
            $('#gotop').fadeOut();
        }
    })
    // $('header').owlCarousel({
    //     items: 1,
    //     autoplay: true,
    //     loop: true
    // });

})