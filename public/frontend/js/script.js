$('.main-slider').slick({
    dots: true,
	autoplay: true,
    autoplaySpeed: 2000,
    pauseOnDotsHover: true,
  });

$(document).ready(function(){  
$('.devAcrdn ul li:first-child h4').addClass('active');
$('.devAcrdn ul li:first-child .deaLs').show();  
$("body").delegate(".devAcrdn ul li h4", "click", function() {
$('.devAcrdn ul li h4').removeClass('active');  
$('.devAcrdn ul li .deaLs').stop(0,0).slideUp('slow'); 
$(this).addClass('active').closest('li').find('.deaLs').stop(0,0).slideDown('slow'); });});






