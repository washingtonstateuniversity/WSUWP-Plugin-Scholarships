(function ($) {

	'use strict';

	// Retrieve a list of scholarships.
	$('.wsuwp-scholarships-form').on('submit', function (e) {
		e.preventDefault();

		$('.wsuwp-scholarships-form').after('<div class="wsuwp-scholarships-loading"></div>');

		var data = {
				action: 'set_scholarships',
				nonce: scholarships.nonce,
				age: $('#wsuwp-scholarship-age').val(),
				gpa: $('#wsuwp-scholarship-gpa').val(),
				enrolled: $('[name=wsuwp-scholarship-enrolled]:checked').val(),
				resident: $('[name=wsuwp-scholarship-resident]:checked').val()
			};

		$.post( scholarships.ajax_url, data, function (response) {
			$('.wsuwp-scholarships-loading').after('<p>Based on the information you entered, you should be eligible for the following scholarships. Some may have additional requirements, so please be sure to read the details for a scholarship before applying.</p>').remove();
			$('.wsuwp-scholarships-filters').show();
			$('.wsuwp-scholarships').html(response);
		} );
	});
}(jQuery));
