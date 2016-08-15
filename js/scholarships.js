(function ($) {

	'use strict';

	$('.wsuwp-scholarships-header').hide();

	// Retrieve a list of scholarships.
	$('.wsuwp-scholarships-form').on('submit', function (e) {
		e.preventDefault();

		$('.wsuwp-scholarships').html('<div class="wsuwp-scholarships-loading"></div>');

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

		$.post(scholarships.ajax_url, data, function (response) {
			if ($('[name=wsuwp-scholarship-enrolled]').is(':checked')) {
				$('#enrolled').closest('li').hide();
			}

			if ($('[name=wsuwp-scholarship-resident]').is(':checked')) {
				$('#resident').closest('li').hide();
			}

			$('.wsuwp-scholarships-header').show();
			$('.wsuwp-scholarships-filters').show();
			$('.wsuwp-scholarships').html(response);
		});
	});

	// Sort scholarships.
	$('.wsuwp-scholarships-header a').on('click', function (e) {
		e.preventDefault();

		var link = $(this),
			scholarships = $('.wsuwp-scholarships article'),
			selected = link.html().toLowerCase();

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
	$('.wsuwp-scholarship-eligibility').on('change', 'input:checkbox', function () {
		var scholarships = $('.wsuwp-scholarships article'),
			selected = [];

		$('.wsuwp-scholarship-eligibility input:checkbox:checked').each(function () {
			selected.push('.eligibility-' + $(this).val());
		});

		if (selected.length > 0) {
			scholarships.not(selected.join('')).hide('fast');
			scholarships.filter(selected.join('')).show('fast');
		} else {
			scholarships.show('fast');
		}
	});
}(jQuery));
