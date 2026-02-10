var testimonialCarousel = $('#testimonial').owlCarousel({
    items:2,
    loop:true,
    margin:30,
    autoplay:true,
    autoplayTimeout:5000,
    autoplayHoverPause:true,
    nav:false,
    dots:false,
    responsive:{
        0:{
            items:1,
            margin:20
        },
        600:{
            items:1,
            margin:20
        },
        768:{
            items:2,
            margin:20
        },
        992:{
            items:2,
            margin:30
        }
    }
});

// Custom navigation buttons
$('#testimonialPrev').click(function() {
    testimonialCarousel.trigger('prev.owl.carousel');
});

$('#testimonialNext').click(function() {
    testimonialCarousel.trigger('next.owl.carousel');
});
let mybutton = document.getElementById("myBtn");
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}
function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}