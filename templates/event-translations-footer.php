</div>
<div class="clear"></div>
<script type="text/javascript">
jQuery( function($) {
<?php foreach ( $editor_options as $translation_set_id => $options ) {
	?>
		$('#translations_<?php echo esc_html( $translation_set_id ); ?>' ).click( function() {
		$gp_editor_options = <?php echo wp_json_encode( $options ); ?>;
		$( '#translation' ).attr('id', null);
		$( '#translations_<?php echo esc_html( $translation_set_id ); ?> table' ).attr( 'id', 'translations' );
		$gp.editor.init( $( '#translations_<?php echo esc_html( $translation_set_id ); ?>' ) );
	} );
<?php } ?>
} );
</script>
<?php gp_tmpl_footer(); ?>
