<?php
/**
 * Widgets
 *
 * @package     GamiPress\Submissions\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_SUBMISSIONS_DIR .'includes/widgets/submission-widget.php';

// Register plugin widgets
function gamipress_submissions_register_widgets() {

    register_widget( 'gamipress_submission_widget' );

}
add_action( 'widgets_init', 'gamipress_submissions_register_widgets' );