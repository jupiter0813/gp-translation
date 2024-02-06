( function( $, $gp ) {
jQuery(document).ready(function($) {
    $gp.notices.init();
    if ( ! $('#event-timezone').val() ) {
        selectUserTimezone();
    }
    validateEventDates();

    $('.submit-event').on('click', function(e) {
        e.preventDefault();
        if ( $('#event-end').val() <= $('#event-start').val() ) {
            $gp.notices.error( 'Event end date and time must be later than event start date and time.' );
            return;
        }
        var btnClicked = $(this).data('event-status');
        if ( btnClicked == 'publish' ) {
            var submitPrompt = 'Are you sure you want to publish this event?';
            if ( ! confirm( submitPrompt ) ) {
                return;
            }
        }
        $('#event-form-action').val( btnClicked );
        var $form = $('.translation-event-form');

        $.ajax({
            type: 'POST',
            url: $translation_event.url,
            data:$form.serialize(),
            success: function(response) {
                if ( response.data.eventId ) {
                    history.replaceState('','', '/glotpress/events/edit/' + response.data.eventId)
                    $('#form-name').val('edit_event');
                    $('.event-page-title').text('Edit Event');
                    $('#event-id').val(response.data.eventId);
                    if( btnClicked == 'publish' ) {
                        $('button[data-event-status="draft"]').hide();
                        $('button[data-event-status="publish"]').text('Update Event');
                    }
                    $('#event-url').removeClass('hide-event-url').find('a').attr('href', response.data.eventUrl).text(response.data.eventUrl);
                    $gp.notices.success(response.data.message);
                }
            },
            error: function(error) {
                $gp.notices.error(response.data.message);
            }
        });
    });

    function validateEventDates() {
        var startDateTimeInput = $('#event-start');
        var endDateTimeInput = $('#event-end');
    
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

});
}( jQuery, $gp )
);