<?php
/**
 * Admin
 *
 * @package GamiPress\Submissions\Admin
 * @since 1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Add admin bar menu
 *
 * @since 1.0.0
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function gamipress_submissions_admin_bar_menu( $wp_admin_bar ) {

    // - Submissions
    $wp_admin_bar->add_node( array(
        'id'     => 'gamipress-submissions',
        'title'  => __( 'Submissions', 'gamipress-submissions' ),
        'parent' => 'gamipress',
        'href'   => admin_url( 'admin.php?page=gamipress_submissions' )
    ) );

}
add_action( 'admin_bar_menu', 'gamipress_submissions_admin_bar_menu', 150 );

/**
 * Shortcut function to get plugin options
 *
 * @since  1.0.0
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed
 */
function gamipress_submissions_get_option( $option_name, $default = false ) {

    $prefix = 'gamipress_submissions_';

    return gamipress_get_option( $prefix . $option_name, $default );
}

/**
 * GamiPress Submissions Email Settings meta boxes
 *
 * @since  1.0.0
 *
 * @param array $meta_boxes
 *
 * @return array
 */
function gamipress_submissions_email_settings_meta_boxes( $meta_boxes ) {

    $prefix = 'gamipress_submissions_';

    $meta_boxes['gamipress-submissions-email-settings'] = array(
        'title' => gamipress_dashicon( 'upload' ) . __( 'Submissions: Pending submission email', 'gamipress-submissions' ),
        'fields' => apply_filters( 'gamipress_submissions_email_settings_fields', array(
            $prefix . 'disable_pending_submission_email' => array(
                'name' => __( 'Disable pending submission emails', 'gamipress-submissions' ),
                'desc' => __( 'Check this option to do not receive emails about pending submissions.', 'gamipress-submissions' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
            ),
            $prefix . 'pending_submission_subject' => array(
                'name' => __( 'Email subject', 'gamipress-submissions' ),
                'desc' => __( 'The email subject.', 'gamipress-submissions' ),
                'type' => 'text',
                'default' => __( 'New submission #{id}', 'gamipress-submissions' ),
            ),
            $prefix . 'pending_submission_content' => array(
                'name' => __( 'Email content', 'gamipress-submissions' ),
                'desc' => __( 'The email content. Available tags:', 'gamipress-submissions' )
                    . gamipress_submissions_get_pattern_tags_html(),
                'type' => 'wysiwyg',
                'default' => __( '{user} sent a new submission for {post_link}.', 'gamipress-submissions' )
                    .  "\n" . __( 'Additional notes: {notes}', 'gamipress-submissions' ),
            ),
        ) ),
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_email_meta_boxes', 'gamipress_submissions_email_settings_meta_boxes' );

/**
 * Register meta boxes
 *
 * @since 1.0.0
 */
function gamipress_submissions_meta_boxes() {

    // Start with an underscore to hide fields from custom fields list
    $prefix = '_gamipress_submissions_';

    // Submissions
    gamipress_add_meta_box(
        'submissions',
        __( 'Submissions', 'gamipress-submissions' ),
        array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() ),
        array(
            $prefix . 'enable' => array(
                'name' 	    => __( 'Allow unlock through submission', 'gamipress-submissions' ),
                'desc' 	    => __( 'Check this option to allow users unlock through an admin reviewed submission.', 'gamipress-submissions' ),
                'type' 	    => 'checkbox',
                'classes' 	=> 'gamipress-switch',
            ),
            $prefix . 'toggle_button_text' => array(
                'name' 	    => __( 'Submission toggle button text', 'gamipress-submissions' ),
                'desc' 	    => __( 'The submission toggle button text. By default, "Show submission form". Leave empty to disable the toggle button.', 'gamipress-submissions' )
                . "<br>" . __( '<strong>Note:</strong> The toggle button will be displayed only if notes are active.', 'gamipress-submissions' ),
                'type' 	    => 'text',
                'default' 	=> __( 'Show submission form', 'gamipress-submissions' ),
            ),
            $prefix . 'button_text' => array(
                'name' 	    => __( 'Submission submit button text', 'gamipress-submissions' ),
                'desc' 	    => __( 'The submission submit button text. By default, "Submit".', 'gamipress-submissions' ),
                'type' 	    => 'text',
                'default' 	=> __( 'Submit', 'gamipress-submissions' ),
            ),
            $prefix . 'notes' => array(
                'name' 	    => __( 'Allow submission notes', 'gamipress-submissions' ),
                'desc' 	    => __( 'Check this option to allow users to enter notes with their submission.', 'gamipress-submissions' ),
                'type' 	    => 'checkbox',
                'classes' 	=> 'gamipress-switch',
            ),
            $prefix . 'notes_label' => array(
                'name' 	    => __( 'Notes input label', 'gamipress-submissions' ),
                'desc' 	    => __( 'The notes input label. By default, "Describe why you should earn this item:".', 'gamipress-submissions' ),
                'type' 	    => 'text',
                'default' 	=> __( 'Describe why you should earn this item:', 'gamipress-submissions' ),
            ),
            $prefix . 'auto_approve' => array(
                'name' 	    => __( 'Auto-approve submissions', 'gamipress-submissions' ),
                'desc' 	    => __( 'Check this option to approve submission automatically without need to review them.', 'gamipress-submissions' ),
                'type' 	    => 'checkbox',
                'classes' 	=> 'gamipress-switch',
            ),
        ),
        array(
            'context' => 'side',
            'priority' => 'low',
        )
    );

}
add_action( 'gamipress_init_meta_boxes', 'gamipress_submissions_meta_boxes' );

/**
 * GamiPress Submissions Licensing meta box
 *
 * @since  1.0.0
 *
 * @param $meta_boxes
 *
 * @return mixed
 */
function gamipress_submissions_licenses_meta_boxes( $meta_boxes ) {

    $meta_boxes['gamipress-submissions-license'] = array(
        'title' => __( 'GamiPress Submissions', 'gamipress-submissions' ),
        'fields' => array(
            'gamipress_submissions_license' => array(
                'name' => __( 'License', 'gamipress-submissions' ),
                'type' => 'edd_license',
                'file' => GAMIPRESS_SUBMISSIONS_FILE,
                'item_name' => 'Submissions',
            ),
        )
    );

    return $meta_boxes;

}
add_filter( 'gamipress_settings_licenses_meta_boxes', 'gamipress_submissions_licenses_meta_boxes' );

/**
 * GamiPress Submissions automatic updates
 *
 * @since  1.0.0
 *
 * @param array $automatic_updates_plugins
 *
 * @return array
 */
function gamipress_submissions_automatic_updates( $automatic_updates_plugins ) {

    $automatic_updates_plugins['gamipress-submissions'] = __( 'Submissions', 'gamipress-submissions' );

    return $automatic_updates_plugins;
}
add_filter( 'gamipress_automatic_updates_plugins', 'gamipress_submissions_automatic_updates' );