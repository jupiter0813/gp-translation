<?php
/**
 * Template for event form.
 */
namespace Wporg\TranslationEvents\Templates;

use Wporg\TranslationEvents\Event\Event;
use Wporg\TranslationEvents\Event_Text_Snippet;
use Wporg\TranslationEvents\Templates;
use Wporg\TranslationEvents\Urls;

/** @var bool $is_create_form */
/** @var Event $event */

$page_title = $is_create_form ? 'Create Event' : 'Edit Event';

Templates::header(
	array(
		'html_title'  => __( 'Translation Events' ) . ' - ' . esc_html( $page_title . ' - ' . $event->title() ),
		'page_title'  => $page_title,
		'breadcrumbs' => array( esc_html( $page_title ) ),
	),
);
?>

<div class="event-page-wrapper">
	<?php Templates::part( 'event-form', compact( 'is_create_form', 'event' ) ); ?>
</div>

<?php if ( $event->id() ) : ?>
	<div class="event-edit-right">
		<a class="manage-attendees-btn button is-primary" href="<?php echo esc_url( Urls::event_attendees( $event->id() ) ); ?>"><?php esc_html_e( 'Manage Attendees', 'gp-translation-events' ); ?></a>
	</div>
<?php endif; ?>

<?php Templates::footer(); ?>
