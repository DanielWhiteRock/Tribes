<?php
/**
 * Widgets
 *
 * @package     GamiPress\Social_Share\Widgets
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

require_once GAMIPRESS_SOCIAL_SHARE_DIR .'includes/widgets/social-share-widget.php';

// Register plugin widgets
function gamipress_social_share_register_widgets() {
    register_widget( 'gamipress_social_share_widget' );
}
add_action( 'widgets_init', 'gamipress_social_share_register_widgets' );