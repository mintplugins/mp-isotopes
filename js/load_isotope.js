jQuery(document).ready(function($) {
//isotope
$('.mintthemes_isotopes_container').isotope({
 // options
 itemSelector : '.hentry',
 layoutMode : 'fitRows'
});
// filter items when filter link is clicked
$('.isotopenav').change(function(){
 var selector = $(this).attr('value');
 $('.mintthemes_isotopes_container').isotope({ filter: selector });
 return false;
});

// filter items when filter link is clicked
$('.isotopenav a').click(function(){
  var selector = $(this).attr('valuemint');
  $('.mintthemes_isotopes_container').isotope({ filter: selector });
  return false;
});

});


