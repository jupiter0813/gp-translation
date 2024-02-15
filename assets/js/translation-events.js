(
	function ( $, $gp ) {
		jQuery( document ).ready(
			function ( $ ) {
				$gp.notices.init();
				const timezoneElement = $( '#event-timezone' );
				if ( timezoneElement.length && ! timezoneElement.val() ) {
					selectUserTimezone();
				}
				validateEventDates();
				convertToUserLocalTime();

				$( '.submit-event' ).on(
					'click',
					function ( e ) {
						e.preventDefault();
						if ( $( '#event-end' ).val() <= $( '#event-start' ).val() ) {
							$gp.notices.error( 'Event end date and time must be later than event start date and time.' );
							return;
						}
						const btnClicked = $( this ).data( 'event-status' );
						if ( btnClicked === 'publish' && '' === $( '#event-id' ).val() ) {
							const submitPrompt = 'Are you sure you want to publish this event?';
							if ( ! confirm( submitPrompt ) ) {
								return;
							}
						}
						$( '#event-form-action' ).val( btnClicked );
						const $form = $( '.translation-event-form' );
						const $is_creation = $('#form-name').val() === 'create_event';

						$.ajax(
							{
								type: 'POST',
								url: $translation_event.url,
								data:$form.serialize(),
								success: function(response) {
									if ( response.data.eventId ) {
										history.replaceState( '', '', response.data.eventEditUrl );
										$('#form-name').val('edit_event');
										$('.event-page-title').text('Edit Event');
										$('#event-id').val(response.data.eventId);
										if( btnClicked === 'publish' ) {
											$('button[data-event-status="draft"]').hide();
											$('button[data-event-status="publish"]').text('Update Event');
										}
										if( btnClicked === 'draft' ) {
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
							}
						);
					}
				);

				$('.delete-event').on('click', function(e) {
					e.preventDefault();
					if ( ! confirm( 'Are you sure you want to delete this event?' ) ) {
						return;
					}
					const $form = $('.translation-event-form');
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
			}
		);

		function validateEventDates() {
			const startDateTimeInput = $('#event-start');
			const endDateTimeInput = $('#event-end');
			if ( ! startDateTimeInput.length || ! endDateTimeInput.length ) {
				return;
			}

			startDateTimeInput.add( endDateTimeInput ).on(
				'input',
				function () {
					endDateTimeInput.prop('min', startDateTimeInput.val());
					if (endDateTimeInput.val() < startDateTimeInput.val()) {
						endDateTimeInput.val(startDateTimeInput.val());
					}
				}
			);
		}
		function selectUserTimezone() {
			const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
			// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceBefore
			// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
			document.querySelector( `#event-timezone option[value="${timezone}"]` ).selected = true
			// phpcs:enable
		}

		function convertToUserLocalTime() {
			const timeElements = document.querySelectorAll('time.event-utc-time');
			if ( timeElements.length === 0 ) {
				return;
			}
			timeElements.forEach(
				function ( timeEl ) {
					const eventDateObj = new Date( timeEl.getAttribute('datetime') );
					const userTimezoneOffset = new Date().getTimezoneOffset();
					const userTimezoneOffsetMs = userTimezoneOffset * 60 * 1000;
					const userLocalDateTime = new Date(eventDateObj.getTime() - userTimezoneOffsetMs);

					const options = {
						weekday: 'short',
						year: 'numeric',
						month: 'long',
						day: 'numeric',
						hour: 'numeric',
						minute: 'numeric',
						hour12: true,
						timeZoneName: 'short'
					};

					timeEl.textContent = userLocalDateTime.toLocaleString('en-US', options);
				}
			);
		}
	}( jQuery, $gp )
);
