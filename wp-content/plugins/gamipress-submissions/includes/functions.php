<?php
/**
 * Functions
 *
 * @package GamiPress\Submissions\Functions
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Render the submission form
 *
 * @since 1.0.0
 *
 * @param int $post_id the post ID
 *
 * @return string
 */
function gamipress_submissions_form_markup( $post_id ) {

    global $gamipress_submissions_template_args;

    $prefix = '_gamipress_submissions_';

    // Return if submissions is not enabled
    if( ! (bool) gamipress_get_post_meta( $post_id, $prefix . 'enable', true ) ) {
        return '';
    }

    // Setup the toggle button text
    $toggle_button_text = gamipress_get_post_meta( $post_id, $prefix . 'toggle_button_text', true );

    // Setup the button text
    $button_text = gamipress_get_post_meta( $post_id, $prefix . 'button_text', true );

    if( empty( $button_text ) ) {
        $button_text = __( 'Submit', 'gamipress-submissions' );
    }

    // Setup the notes label
    $notes_label = gamipress_get_post_meta( $post_id, $prefix . 'notes_label', true );

    if( empty( $notes_label ) ) {
        $notes_label = __( 'Describe why you should earn this item:', 'gamipress-submissions' );
    }

    $auto_approve = (bool) gamipress_get_post_meta( $post_id, $prefix . 'auto_approve', true );

    $gamipress_submissions_template_args = array(
        'post_id' => $post_id,
        'notes' => (bool) gamipress_get_post_meta( $post_id, $prefix . 'notes', true ),
        'toggle_button_text' => $toggle_button_text,
        'button_text' => $button_text,
        'notes_label' => $notes_label,
        'auto_approve' => $auto_approve,
    );

    // Render the submission form
    ob_start();
    gamipress_get_template_part( 'submission-form' );
    $output = ob_get_clean();

    return $output;

}
