( function( $, $gp ) {
jQuery(document).ready(function($) {
    $gp.notices.init(),
    $('#submit_event, #edit_translation_event').on('click', function(e) {
        e.preventDefault();
        var $form = $('.translation_event_form');
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

});
}( jQuery, $gp )
);