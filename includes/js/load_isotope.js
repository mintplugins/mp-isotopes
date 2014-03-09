jQuery(window).ready(function($) {
	
	$(window).load(function(){ 

		//isotope if hentry available
		$('.moveplugins_isotopes_container').isotope({
			// options
			itemSelector : "[id^='post-']",
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

});


