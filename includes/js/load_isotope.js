jQuery(window).ready(function($) {
	//isotope
	$('.moveplugins_isotopes_container').isotope({
		// options
		itemSelector : '.hentry',
		layoutMode : 'masonry'
	});
	// filter items when filter link is clicked
	$('.isotopenav').change(function(){
		var selector = $(this).attr('value');
		$('.moveplugins_isotopes_container').isotope({ filter: selector });
		return false;
	});
	
	// filter items when filter link is clicked
	$('.isotopenav a').click(function(){
		var selector = $(this).attr('valuemint');
		$('.moveplugins_isotopes_container').isotope({ filter: selector });
		return false;
	});

});


