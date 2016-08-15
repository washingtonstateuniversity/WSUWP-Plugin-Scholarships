(function ($) {

	'use strict';

	// Hide the show/hide options and column headings.
	$('.wsuwp-scholarships-filters').hide();
	$('.wsuwp-scholarships-header').hide();

	// Retrieve and display a list of scholarships.
	function scholarships_response(data) {
		$('.wsuwp-scholarships-filters').hide().find('input:checkbox').removeAttr('checked');

		$('.wsuwp-scholarships').html('<div class="wsuwp-scholarships-loading"></div>');

		$.post(scholarships.ajax_url, data, function (response) {
			// Make all show/hide options available first, then hide them if they aren't needed.
			$('.wsuwp-scholarships-filters div').show();
			$('.wsuwp-scholarship-major li').show();

			if ($('#wsuwp-scholarship-enrolled').val()) {
				$('#no-enrollment').closest('li').hide();
			}

			if ($('#wsuwp-scholarship-major').val()) {
				$('.wsuwp-scholarship-major').hide();
			}

			if ($('#wsuwp-scholarship-school-year').val()) {
				$('.wsuwp-scholarship-school-year').hide();
			}

			if ($('#wsuwp-scholarship-citizenship').val()) {
				$('.wsuwp-scholarship-citizenship').hide();
			}

			if ($('#wsuwp-scholarship-gender').val()) {
				$('.wsuwp-scholarship-gender').hide();
			}

			if ($('#wsuwp-scholarship-state').val()) {
				$('.wsuwp-scholarship-state').hide();
			}

			if ($('#wsuwp-scholarship-ethnicity').val()) {
				$('.wsuwp-scholarship-ethnicity').hide();
			}

			// Display the show/hide options and column headings.
			$('.wsuwp-scholarships-filters').show();
			$('.wsuwp-scholarships-header').show();

			// Display the list of retrieved scholarships.
			$('.wsuwp-scholarships').html(response);
		});
	}

	// Retrieve scholarships based on the input and selected values.
	$('.wsuwp-scholarships-form').on('submit', function (e) {
		e.preventDefault();

		var data = {
				action: 'set_scholarships',
				nonce: scholarships.nonce,
				age: $('#wsuwp-scholarship-age').val(),
				gpa: $('#wsuwp-scholarship-gpa').val(),
				enrollment: $('#wsuwp-scholarship-enrolled').val(),
				major: $('#wsuwp-scholarship-enrolled').val(),
				year: $('#wsuwp-scholarship-school-year').val(),
				citizenship: $('#wsuwp-scholarship-citizenship').val(),
				gender: $('#wsuwp-scholarship-gender').val(),
				state: $('#wsuwp-scholarship-state').val(),
				ethnicity: $('#wsuwp-scholarship-ethnicity').val()
			};

		scholarships_response(data);
	});

	// Retrieve all scholarships.
	$('.column').on('click', '.wsuwp-scholarships-all', function (e) {
		e.preventDefault();

		// Reset the primary form fields and options.
		$('.wsuwp-scholarships-form').find('option:selected').removeAttr('selected');
		$('.wsuwp-scholarships-form').find('input:not(:submit)').val('');

		var data = {
				action: 'set_scholarships',
				nonce: scholarships.nonce
			};

		scholarships_response(data);
	});

	// Sort scholarships.
	$('.wsuwp-scholarships-header a').on('click', function (e) {
		e.preventDefault();

		var link = $(this),
			scholarships = $('.wsuwp-scholarships article'),
			selected = link.html().toLowerCase();

		// Add classes for showing which column the scholarships are being sorted by.
		if (link.hasClass('sorted')) {
			link.toggleClass('asc');
		} else {
			$('.wsuwp-scholarships-header a').removeClass('sorted asc');
			link.addClass('sorted');
		}

		scholarships.sort(function (a, b) {
			var an = a.getAttribute('data-' + selected),
				bn = b.getAttribute('data-' + selected);

			if ('scholarship' === selected) {
				an = b.getAttribute('data-' + selected);
				bn = a.getAttribute('data-' + selected);
			}

			if (link.hasClass('asc')) {
				return an - bn;
			}

			return bn - an;
		});

		scholarships.detach().appendTo($('.wsuwp-scholarships'));
	});

	// Show/hide scholarships.
	$('.wsuwp-scholarships-filters').on('change', 'input:checkbox', function () {
		var scholarships = $('.wsuwp-scholarships article'),
			selected = [];

		// Build the array of classes to look for.
		$('.wsuwp-scholarships-filters input:checkbox:checked').each(function () {
			selected.push($(this).val());
		});

		// Hide items that don't have the classes in the built array and show those that do.
		if (selected.length > 0) {
			scholarships.not(selected.join('')).hide('fast');
			scholarships.filter(selected.join('')).show('fast');
		} else {
			scholarships.show('fast');
		}
	});
}(jQuery));
