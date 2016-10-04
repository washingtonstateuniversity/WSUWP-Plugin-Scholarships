(function ($, scholarships) {

	'use strict';

	// Hide the show/hide options and column headings.
	var $scholarships_container = $('.wsuwp-scholarships'),
		$scholarships = '',
		$filters = $('.wsuwp-scholarships-filters'),
		$header = $('.wsuwp-scholarships-header');

	$filters.hide();
	$header.hide();

	// Retrieve a list of scholarships.
	function scholarships_response(data) {
		$filters.hide().find('input:checkbox').removeAttr('checked');

		$scholarships_container.html('<div class="wsuwp-scholarships-loading"></div>');

		$.post(scholarships.ajax_url, data, function (response) {
			sessionStorage.setItem('results', response);
			display_results(response);
		});
	}

	// Display the retrieved scholarships.
	function display_results(response) {
		var response_data = $.parseJSON(response);

		// Make all show/hide options available first, then hide them if they aren't needed.
		$filters.find('div').show();
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
		$filters.show();
		$header.show();

		// Display the list of retrieved scholarships.
		$scholarships_container.html('').append(response_data);

		$scholarships = $scholarships_container.find('article');
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

	function sorted_or_filtered(callback) {

		// Sort scholarships.
		$header.on('click', 'a', function (e) {
			e.preventDefault();

			var link = $(this),
				selected = link.html().toLowerCase();

			// Add classes for showing which column the scholarships are being sorted by.
			if (link.hasClass('sorted')) {
				link.toggleClass('asc');
			} else {
				$header.find('a').removeClass('sorted asc');
				link.addClass('sorted');
			}

			$scholarships.sort(function (a, b) {
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

			$scholarships.detach().appendTo($scholarships_container);

			callback();
		});

		// Show/hide scholarships.
		$filters.on('change', 'input:checkbox', function () {
			var selected = [];

			// Build the array of classes to look for.
			$filters.find('input:checkbox:checked').each(function () {
				selected.push($(this).val());

			});

			// Hide items that don't have the classes in the built array and show those that do.
			if (selected.length > 0) {
				$scholarships.not(selected.join('')).hide(0);
				$scholarships.filter(selected.join('')).show(0);
			} else {
				$scholarships.show(0);
			}

			callback();
		});
	}

	// Re-stripe after sorting or filtering.
	sorted_or_filtered(function () {
		$scholarships_container.find('article:visible:odd').css('background-color', '#fff');
		$scholarships_container.find('article:visible:even').css('background-color', '#eff0f1');
	});

	var $scholarships_container_top = $scholarships_container.offset().top,
		$tools = $('.wsuwp-scholarships-tools');

	// Toggle visibility of the back to top button based on scroll position.
	$(document).on('scroll', function () {
		if ($(window).scrollTop() >= $scholarships_container_top) {
			$tools.show();
		} else {
			$tools.hide();
		}
	});

	// Jump to the top of the page when the back to top button is clicked.
	// (This is here only to prevent a hash from being appended to the URL.)
	$('.back-to-top').on('click', function (e) {
		e.preventDefault();

		$('html, body').scrollTop(0);
	});

	// Check if storage is available.
	function storage_available(type) {
		try {
			var storage = window[type],
				x = '__storage_test__';
			storage.setItem(x, x);
			storage.removeItem(x);
			return true;
		} catch(e) {
			return false;
		}
	}

	// If storage is available and items are stored, display the form field values.
	if (storage_available('sessionStorage')) {
		var results = sessionStorage.getItem('results');

		if (sessionStorage.length) {
			display_values();
		}

		if (results) {
			display_results(results);
		}
	}

	// Store the form field values.
	function store_values() {
		sessionStorage.setItem('age', $('#wsuwp-scholarship-age').val());
		sessionStorage.setItem('gpa', $('#wsuwp-scholarship-gpa').val());
		sessionStorage.setItem('enrollment', $('#wsuwp-scholarship-enrolled').val());
		sessionStorage.setItem('major', $('#wsuwp-scholarship-major').val());
		sessionStorage.setItem('year', $('#wsuwp-scholarship-school-year').val());
		sessionStorage.setItem('citizenship', $('#wsuwp-scholarship-citizenship').val());
		sessionStorage.setItem('gender', $('#wsuwp-scholarship-gender').val());
		sessionStorage.setItem('state', $('#wsuwp-scholarship-state').val());
		sessionStorage.setItem('ethnicity', $('#wsuwp-scholarship-ethnicity').val());

		display_values();
	}

	// Display the form field values.
	function display_values() {
		var age = sessionStorage.getItem('age'),
			gpa = sessionStorage.getItem('gpa'),
			enrollment = sessionStorage.getItem('enrollment'),
			major = sessionStorage.getItem('major'),
			year = sessionStorage.getItem('year'),
			citizenship = sessionStorage.getItem('citizenship'),
			gender = sessionStorage.getItem('gender'),
			state = sessionStorage.getItem('state'),
			ethnicity = sessionStorage.getItem('ethnicity');

		$('#wsuwp-scholarship-age').val(age);
		$('#wsuwp-scholarship-gpa').val(gpa);
		$('#wsuwp-scholarship-enrolled').val(enrollment);
		$('#wsuwp-scholarship-major').val(major);
		$('#wsuwp-scholarship-school-year').val(year);
		$('#wsuwp-scholarship-citizenship').val(citizenship);
		$('#wsuwp-scholarship-gender').val(gender);
		$('#wsuwp-scholarship-state').val(state);
		$('#wsuwp-scholarship-ethnicity').val(ethnicity);
	}

	// Store field values when the form is submitted.
	$('.wsuwp-scholarships-form').on('submit', function() {
		store_values();
	})
}(jQuery, scholarships));
