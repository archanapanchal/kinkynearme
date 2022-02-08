$(document).ready(function(){
     $(".sld2").owlCarousel({
      dots: true,
      navigation:false,
      infinite: true,
      speed: 300,
      loop: true,  
      dots:true,
      autoplay:true,
      items: 1,
      margin: 20,
      responsiveClass: true,
      autoHeight: true,
      nav: true,
     autoplayHoverPause:true,
     responsive: {
        0: {
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
        },

        1366: {
          items: 1
        }
      }
    });
  });


autosize();
function autosize(){
    var text = $('.edit-content textarea');

    text.each(function(){
        $(this).attr('rows',1);
        resize($(this));
    });

    text.on('input', function(){
        resize($(this));
    });
    
    function resize ($text) {
        $text.css('height', 'auto');
        $text.css('height', $text[0].scrollHeight+'px');
    }
}

$(document).on('click','.close-search-popup', function(){
    $(".advance-search-v2").slideUp();
    $(".commmon-search-tab").slideUp();
    // $('.advance-search-v2').css("display","none");
})


$(document).ready(function() {


 /********step-form start **/

$('.skip-link').click(function(){
});       

$('.back-link').click(function(){
});  


    


 /********step-form end **/
var i, items = $('.pro-step'), pane = $('.step-img'), pancon = $('.set-active');
            // next
    $('.skip-link').on('click', function(){
        for(i = 0; i < items.length; i++){
            if($(items[i]).hasClass('active') == true){
                break;
            }
           /* if (i == items.length) {
                alert('i');
            } */
        }
        var isLastItem = (i == (items.length - 1));
        if(i < items.length - 1){
            // for tab
            $(items[i]).removeClass('active');
            $(items[i+1]).addClass('active');
            // for pane
            $(pane[i]).removeClass('active');
            $(pane[i+1]).addClass('active');
            // for pancon
            $(pancon[i]).removeClass('active');
            $(pancon[i+1]).addClass('active');
        }

        if (i == (items.length - 2)) {
            $('.action-button').addClass('aaaaa').hide();
            $('.action-submit').addClass('bbbb').show();

        } 
        if(isLastItem == true){
            $(".action-submit").trigger("click");
        }

    });

    $('.back-link').on('click', function(){
                for(i = 0; i < items.length; i++){
                    if($(items[i]).hasClass('active') == true){
                        break;
                    }
                }
                if(i != 0){
                    // for tab
                    $(items[i]).removeClass('active');
                    $(items[i-1]).addClass('active');
                    // for pane
                    $(pane[i]).removeClass('show active');
                    $(pane[i-1]).addClass('show active');

                    $(pancon[i]).removeClass('active');
                    $(pancon[i-1]).addClass('active');
                }

                $('.action-submit').hide();
                $('.action-button').removeAttr('style');
            });






$(function () {
$(window).on('scroll', function () {
if ( $(window).scrollTop() > 10 ) {
$('.navbar').addClass('active');
} else {
$('.navbar').removeClass('active');
}
});
});

});

 

 // *********2-12-2021**********

 $(document).ready(function(){

var current_fs, next_fs, previous_fs; //fieldsets
var opacity;

$(".next").click(function(){

current_fs = $(this).parent();
next_fs = $(this).parent().next();

//Add Class Active
$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

//show the next fieldset
next_fs.show();
//hide the current fieldset with style
current_fs.animate({opacity: 0}, {
step: function(now) {
// for making fielset appear animation
opacity = 1 - now;

current_fs.css({
'display': 'none',
'position': 'relative'
});
next_fs.css({'opacity': opacity});
},
duration: 600
});
});

$(".previous").click(function(){

current_fs = $(this).parent();
previous_fs = $(this).parent().prev();

//Remove class active
$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

//show the previous fieldset
previous_fs.show();

//hide the current fieldset with style
current_fs.animate({opacity: 0}, {
step: function(now) {
// for making fielset appear animation
opacity = 1 - now;

current_fs.css({
'display': 'none',
'position': 'relative'
});
previous_fs.css({'opacity': opacity});
},
duration: 600
});
});

$(".submit").click(function(){
return false;
})

});

// load-more
$(document).ready(function(){
  $(".load-profile").slice(0, 12).show();
  $(".loadMore").on("click", function(e){
    e.preventDefault();
    $(".load-profile:hidden").slice(0, 4).slideDown();
    if($(".load-profile:hidden").length == 0) {
      $(".loadMore").text("No Content").addClass("noContent");
    }
  });
  
})

$(document).ready(function(){
  $(".load-matches").slice(0, 12).show();
  $(".loadMore").on("click", function(e){
    e.preventDefault();
    $(".load-matches:hidden").slice(0, 4).slideDown();
    if($(".load-matches:hidden").length == 0) {
      $(".loadMore").text("No Content").addClass("noContent");
    }
  });
  
})

$(document).ready(function(){
  $(".load-favorite").slice(0, 12).show();
  $(".loadMore").on("click", function(e){
    e.preventDefault();
    $(".load-favorite:hidden").slice(0, 4).slideDown();
    if($(".load-favorite:hidden").length == 0) {
      $(".loadMore").text("No Content").addClass("noContent");
    }
  });
  
});

 // ******************




