jQuery(window).ready(function($) {

		//isotope if hentry available
		$('.mp_isotopes_container').isotope({
			// options
			itemSelector : "[id^='post-']",
			layoutMode : 'masonry'
		});
		// filter items when filter link is clicked
		$('.isotopenav').change(function(){
			var selector = $(this).attr('value');
			$('.mp_isotopes_container').isotope({ filter: selector });
			return false;
		});
		
		// filter items when filter link is clicked
		$('.isotopenav a').click(function(){
			var selector = $(this).attr('valuemint');
			$('.mp_isotopes_container').isotope({ filter: selector });
			return false;
		});
		
		$( window ).load(function(){ 
			$('.mp_isotopes_container').isotope('reLayout');
		});
		
		$( document ).ajaxComplete(function() {
			$('.mp_isotopes_container').isotope('reLayout');
		});

});


