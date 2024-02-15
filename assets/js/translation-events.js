( function( $, $gp ) {
	jQuery(document).ready(function($) {
		$gp.notices.init();
		if ( $('#event-timezone').length && ! $('#event-timezone').val() ) {
			selectUserTimezone();
		}
		validateEventDates();
		convertToUserLocalTime();

		$('.submit-event').on('click', function(e) {
			e.preventDefault();
			if ( $('#event-end').val() <= $('#event-start').val() ) {
				$gp.notices.error( 'Event end date and time must be later than event start date and time.' );
				return;
			}
			var btnClicked = $(this).data('event-status');
			if ( btnClicked == 'publish' && '' == $('#event-id').val() ) {
				var submitPrompt = 'Are you sure you want to publish this event?';
				if ( ! confirm( submitPrompt ) ) {
					return;
				}
			}
			$('#event-form-action').val( btnClicked );
			var $form = $('.translation-event-form');
			var $is_creation = $('#form-name').val() == 'create_event' ? true : false;

			$.ajax({
				type: 'POST',
				url: $translation_event.url,
				data:$form.serialize(),
				success: function(response) {
					if ( response.data.eventId ) {
						history.replaceState('','', response.data.eventEditUrl)
						$('#form-name').val('edit_event');
						$('.event-page-title').text('Edit Event');
						$('#event-id').val(response.data.eventId);
						if( btnClicked == 'publish' ) {
							$('button[data-event-status="draft"]').hide();
							$('button[data-event-status="publish"]').text('Update Event');
						}
						if( btnClicked == 'draft' ) {
							$('button[data-event-status="draft"]').text('Update Draft');
						}
						$('#event-url').removeClass('hide-event-url').find('a').attr('href', response.data.eventUrl).text(response.data.eventUrl);
						if ( $is_creation ) {
							$('#delete-button').toggle();
						}
						$gp.notices.success(response.data.message);
					}
				},
				error: function(error) {
					$gp.notices.error(response.data.message);
				}
			});
		});

		$('.delete-event').on('click', function(e) {
			e.preventDefault();
			if ( ! confirm( 'Are you sure you want to delete this event?' ) ) {
				return;
			}
			var $form = $('.translation-event-form');
			$('#form-name').val('delete_event');
			$('#event-form-action').val('delete');
			$.ajax({
				type: 'POST',
				url: $translation_event.url,
				data:$form.serialize(),
				success: function(response) {
					window.location = response.data.eventDeleteUrl;
				},
				error: function(error) {
					$gp.notices.error(response.data.message);
				},
			});
		});
		function validateEventDates() {
			var startDateTimeInput = $('#event-start');
			var endDateTimeInput = $('#event-end');
			if ( ! startDateTimeInput.length || ! endDateTimeInput.length ) {
				return;
			}

			startDateTimeInput.add(endDateTimeInput).on('input', function () {
				endDateTimeInput.prop('min', startDateTimeInput.val());
				if (endDateTimeInput.val() < startDateTimeInput.val()) {
					endDateTimeInput.val(startDateTimeInput.val());
				}
			});
		}
		function selectUserTimezone() {
			document.querySelector(`#event-timezone option[value="${Intl.DateTimeFormat().resolvedOptions().timeZone}"]`).selected = true
		}

		function convertToUserLocalTime() {
			var timeElements = document.querySelectorAll('time.event-utc-time');
			if ( timeElements.length === 0 ) {
				return;
			}
			timeElements.forEach(function(timeEl) {
				var eventDateObj = new Date( timeEl.getAttribute('datetime') );
				var userTimezoneOffset = new Date().getTimezoneOffset();
				var userTimezoneOffsetMs = userTimezoneOffset * 60 * 1000;
				var userLocalDateTime = new Date(eventDateObj.getTime() - userTimezoneOffsetMs);

				var options = { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true, timeZoneName: 'short' };

				timeEl.textContent = userLocalDateTime.toLocaleString('en-US', options);
			});
		}

	});
	}( jQuery, $gp )
);
