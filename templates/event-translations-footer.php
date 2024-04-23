</div>
<div class="clear"></div>
<script type="text/javascript">
jQuery( function($) {
<?php foreach ( $editor_options as $translation_set_id => $options ) {
	?>
	$('#translations_<?php echo esc_html( $translation_set_id ); ?>' ).click( set_translation_table_<?php echo esc_html( $translation_set_id ); ?> );
	$('#translations_<?php echo esc_html( $translation_set_id ); ?>' ).mousemove( function() {
		if ( ! $( '#translations', this ).length ) {
			set_translation_table_<?php echo esc_html( $translation_set_id ); ?>();
		}
	});

	function set_translation_table_<?php echo esc_html( $translation_set_id ); ?>() {
		$gp_editor_options = <?php echo wp_json_encode( $options ); ?>;
		$( '#translations' ).attr( 'id', null );
		$( '#translations_<?php echo esc_html( $translation_set_id ); ?> table' ).attr( 'id', 'translations' );
		$gp.editor.init( $( '#translations_<?php echo esc_html( $translation_set_id ); ?>' ) );
		$gp_translation_helpers_editor = $gp_translation_helpers_editor_<?php echo esc_html( $translation_set_id ); ?>;
		}
<?php } ?>
} );
</script>
<?php
gp_enqueue_script( 'wporg-translate-editor' );
gp_tmpl_footer(); ?>
