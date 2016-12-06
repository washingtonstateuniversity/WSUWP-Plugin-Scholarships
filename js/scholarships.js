( function( $, scholarships ) {

	"use strict";

	var $form = $( ".wsuwp-scholarships-form" ),
		$grade = $( "#wsuwp-scholarship-grade-level" ),
		$gpa = $( "#wsuwp-scholarship-gpa" ),
		$citizenship = $( "#wsuwp-scholarship-citizenship" ),
		$state = $( "#wsuwp-scholarship-state" ),
		$show_after_submit = $( ".display-after-submit" ),
		$filter_toggle = $( ".wsuwp-scholarships-toggle-filters" ),
		$filters = $( ".wsuwp-scholarships-filters" ),
		$count = $( ".wsuwp-scholarships-count span" ),
		$header = $( ".wsuwp-scholarships-header" ),
		$tools = $( ".wsuwp-scholarships-tools" ),
		$scholarships_container = $( ".wsuwp-scholarships" ),
		$scholarships = "",
		$back_to_top = $( ".back-to-top" );

	// Hide filter options and table header.
	$filters.hide();
	$header.hide();

	// Check if storage is available.
	function storage_available( type ) {
		try {
			var storage = window[ type ],
				x = "__storage_test__";
			storage.setItem( x, x );
			storage.removeItem( x );
			return true;
		} catch ( e ) {
			return false;
		}
	}

	// Display the form field values.
	function display_values() {
		var form_data = $.parseJSON( sessionStorage.getItem( "form_data" ) );

		$grade.val( form_data.grade );
		$gpa.val( form_data.gpa );
		$citizenship.val( form_data.citizenship );
		$state.val( form_data.state );
	}

	// Store the form field values.
	function store_values() {
		var form_data = {
			grade: $grade.val(),
			gpa: $gpa.val(),
			citizenship: $citizenship.val(),
			state: $state.val()
		};

		sessionStorage.setItem( "form_data", JSON.stringify( form_data ) );

		display_values();
	}

	// Show the number of displayed scholarships.
	function update_count() {
		$count.html( $scholarships.filter( ":visible" ).length );
	}

	// Display the retrieved scholarships.
	function display_results( response ) {
		var response_data = $.parseJSON( response );

		// Display filter visibility toggler, results count, and column headings.
		$show_after_submit.show();

		// Display the list of retrieved scholarships.
		$scholarships_container.html( "" ).append( response_data );

		$scholarships = $scholarships_container.find( "article" );

		update_count();
	}

	// Retrieve a list of scholarships.
	function scholarships_response( data ) {
		$filters.find( "input:checkbox" ).removeAttr( "checked" );

		$scholarships_container.html( "<div class=\"wsuwp-scholarships-loading\"></div>" );

		$.post( scholarships.ajax_url, data, function( response ) {
			sessionStorage.setItem( "results", response );
			display_results( response );
		} );
	}

	// Handle table sorting and filtering.
	function sorted_or_filtered( callback ) {

		// Sort scholarships.
		$header.on( "click", "a", function( e ) {
			e.preventDefault();

			var link = $( this ),
				selected = link.html().toLowerCase();

			// Add classes for showing which column the scholarships are being sorted by.
			if ( link.hasClass( "sorted" ) ) {
				link.toggleClass( "asc" );
			} else {
				$header.find( "a" ).removeClass( "sorted asc" );
				link.addClass( "sorted" );
			}

			$scholarships.sort( function( a, b ) {
				var an = a.getAttribute( "data-" + selected ),
					bn = b.getAttribute( "data-" + selected );

				if ( "scholarship" === selected ) {
					an = b.getAttribute( "data-" + selected );
					bn = a.getAttribute( "data-" + selected );
				}

				if ( link.hasClass( "asc" ) ) {
					return an - bn;
				}

				return bn - an;
			} );

			$scholarships.detach().appendTo( $scholarships_container );

			callback();
		} );

		// Show/hide scholarships.
		$filters.on( "change", "input:checkbox", function() {
			var selected = [],
				selected_ids = [];

			// Build the array of classes to look for.
			$filters.find( "input:checkbox:checked" ).each( function() {
				selected_ids.push( $( this ).attr( "id" ) );
				selected.push( $( this ).val() );
			} );

			// Store ids of checked boxes.
			sessionStorage.setItem( "filters", JSON.stringify( selected_ids ) );

			// Hide items that don"t have the classes in the built array and show those that do.
			if ( selected.length > 0 ) {
				$scholarships.not( selected.join( "" ) ).hide( 0 );
				$scholarships.filter( selected.join( "" ) ).show( 0 );
			} else {
				$scholarships.show( 0 );
			}

			callback();
		} );
	}

	// Fire actions that need to happen once the document is ready.
	$( document ).ready( function() {

		// Handling for when the page has been arrived at via the search shortcode.
		if ( -1 !== window.location.href.indexOf( "?" ) ) {
			sessionStorage.removeItem( "form_data" );
			sessionStorage.removeItem( "results" );
			sessionStorage.removeItem( "filters" );

			$form.trigger( "submit" );
		}

		// If storage is available and items are stored, display the form field values.
		if ( storage_available( "sessionStorage" ) ) {
			var results = sessionStorage.getItem( "results" ),
				filters = JSON.parse( sessionStorage.getItem( "filters" ) );

			if ( sessionStorage.getItem( "form_data" ) ) {
				display_values();
			}

			if ( results ) {
				display_results( results );
			}

			if ( filters ) {
				$.each( filters, function() {
					$( "#" + this ).trigger( "click" );
				} );
			}
		}
	} );

	// Toggle the visibility of the filters.
	$filter_toggle.on( "click", "a", function( e ) {
		e.preventDefault();

		if ( $filter_toggle.hasClass( "close-filters" ) ) {
			$filter_toggle.removeClass( "close-filters" );
			$filters.slideUp();
		} else {
			$filter_toggle.addClass( "close-filters" );
			$filters.slideDown();
		};
	} );

	// Retrieve scholarships when the form is submitted.
	$form.on( "submit", function( e ) {
		e.preventDefault();

		var data = {
				action: "set_scholarships",
				nonce: scholarships.nonce,
				grade: $grade.val(),
				gpa: $gpa.val(),
				citizenship: $citizenship.val(),
				state: $state.val()
			};

		store_values();
		scholarships_response( data );
	} );

	// Retrieve all scholarships.
	$( ".column" ).on( "click", ".wsuwp-scholarships-all", function( e ) {
		e.preventDefault();

		// Reset the primary form fields and options.
		$form.find( "option:selected" ).removeAttr( "selected" );
		$form.find( "input:not(:submit)" ).val( "" );

		// Clear session storage.
		sessionStorage.removeItem( "form_data" );
		sessionStorage.removeItem( "results" );
		sessionStorage.removeItem( "filters" );

		var data = {
				action: "set_scholarships",
				nonce: scholarships.nonce
			};

		scholarships_response( data );
	} );

	// Re-stripe after sorting or filtering.
	sorted_or_filtered( function() {
		$scholarships_container.find( "article:visible:odd" ).css( "background-color", "#fff" );
		$scholarships_container.find( "article:visible:even" ).css( "background-color", "#eff0f1" );
		update_count();
	} );

	// Toggle visibility of the back to top button based on scroll position.
	$( document ).on( "scroll", function() {
		if ( $( window ).scrollTop() >= $scholarships_container.offset().top ) {
			$tools.show();
		} else {
			$tools.hide();
		}
	} );

	// Jump to the top of the page when the back to top button is clicked.
	// (This is here only to prevent a hash from being appended to the URL.)
	$back_to_top.on( "click", function( e ) {
		e.preventDefault();

		$( "html, body" ).scrollTop( 0 );
	} );
}( jQuery, scholarships ) );
