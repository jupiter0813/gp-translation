( function( $, $gp ) {
jQuery(document).ready(function($) {
    $gp.notices.init();
    selectUserTimezone();
    validateEventDates();
    $('#submit-event, #edit-translation-event').on('click', function(e) {
        if ( $('#event-end').val() <= $('#event-start').val() ) {
            $gp.notices.error( 'Event end date and time must be later than event start date and time.' );
            return;
        }

        e.preventDefault();
        var $form = $('.translation-event-form');
        $.ajax({
            type: 'POST',
            url: $translation_event.url,
            data:$form.serialize(),
            success: function(response) {
                $gp.notices.success(response.data);
            },
            error: function(error) {
                $gp.notices.error(response.data);
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