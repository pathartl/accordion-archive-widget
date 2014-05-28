jQuery(function ($) {

	$('li.archive-accordion-year').click(function() {
			// Change CSS of current year
			$('li.archive-accordion-year').not(this).children('ul').slideUp(250);
			$(this).children('ul').slideToggle(250);
		
	});

});