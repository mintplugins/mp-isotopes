jQuery(window).ready(function($) {
	
	$(window).load(function(){ 

		//isotope if hentry available
		$('.mintplugins_isotopes_container').isotope({
			// options
			itemSelector : "[id^='post-']",
			layoutMode : 'masonry'
		});
		// filter items when filter link is clicked
		$('.isotopenav').change(function(){
			var selector = $(this).attr('value');
			$('.mintplugins_isotopes_container').isotope({ filter: selector });
			return false;
		});
		
		// filter items when filter link is clicked
		$('.isotopenav a').click(function(){
			var selector = $(this).attr('valuemint');
			$('.mintplugins_isotopes_container').isotope({ filter: selector });
			return false;
		});
		
	});

});


