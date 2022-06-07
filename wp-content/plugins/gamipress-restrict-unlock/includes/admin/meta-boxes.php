<?php
/**
 * Meta Boxes
 *
 * @package     GamiPress\Restrict_Unlock\Admin\Meta_Boxes
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function gamipress_restrict_unlock_meta_boxes() {

    $prefix = '_gamipress_restrict_unlock_';

    $post_types = array_merge( array( 'points-type' ), gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() );

    gamipress_add_meta_box(
        'gamipress-restrict-unlock',
        __( 'Restrict Unlock', 'gamipress-restrict-unlock' ),
        $post_types,
        array(

            $prefix . 'restrict' => array(
                'name' => __( 'Restrict unlock', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Check this option to restrict unlock.', 'gamipress-restrict-unlock' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'post_type' => array_merge( gamipress_get_achievement_types_slugs(), gamipress_get_rank_types_slugs() ),
                'show_on_cb' => 'gamipress_restrict_unlock_show_on_post_type',
            ),

            // Points type fields

            $prefix . 'restrict_points_awards' => array(
                'name' => __( 'Restrict points awards unlock', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Check this option to restrict points awards unlock.', 'gamipress-restrict-unlock' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'post_type' => array( 'points-type' ),
                'show_on_cb' => 'gamipress_restrict_unlock_show_on_post_type',
            ),
            $prefix . 'restrict_points_deducts' => array(
                'name' => __( 'Restrict points deducts unlock', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Check this option to restrict points deducts unlock.', 'gamipress-restrict-unlock' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'post_type' => array( 'points-type' ),
                'show_on_cb' => 'gamipress_restrict_unlock_show_on_post_type',
            ),

            // Achievement fields

            $prefix . 'restrict_steps' => array(
                'name' => __( 'Restrict steps unlock', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Check this option to restrict steps unlock.', 'gamipress-restrict-unlock' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'post_type' => gamipress_get_achievement_types_slugs(),
                'show_on_cb' => 'gamipress_restrict_unlock_show_on_post_type',
            ),

            // Rank fields

            $prefix . 'restrict_rank_requirements' => array(
                'name' => __( 'Restrict rank requirements unlock', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Check this option to restrict rank requirements unlock.', 'gamipress-restrict-unlock' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch',
                'post_type' => gamipress_get_rank_types_slugs(),
                'show_on_cb' => 'gamipress_restrict_unlock_show_on_post_type',
            ),

            // Common fields

            $prefix . 'unlock_by' => array(
                'name' => __( 'Unlock access by', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Choose how users can get access to this post.', 'gamipress-restrict-unlock' ),
                'type' => 'select',
                'options' => apply_filters( 'gamipress_restrict_unlock_unlock_by_options', array(
                    'complete-requirements' => __( 'Completing requirements', 'gamipress-restrict-unlock' ),
                    'expend-points'         => __( 'Expending Points', 'gamipress-restrict-unlock' ),
                ) )
            ),
            $prefix . 'access_with_points' => array(
                'name' => __( 'Allow get access expending points', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Check this option to allow users to optionally get access without completing the requirements by expending an amount of points.', 'gamipress-restrict-unlock' ),
                'type' => 'checkbox',
                'classes' => 'gamipress-switch'
            ),
            $prefix . 'points_to_access' => array(
                'name' => __( 'Points to get access', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Amount of points needed to get access to this post.', 'gamipress-restrict-unlock' ),
                'type' => 'gamipress_points',
                'points_type_key' => $prefix . 'points_type_to_access',
                'default' => '0',
            ),
            $prefix . 'requirements' => array(
                'name' => __( 'Requirements', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Configure the requirements that users need to meet to unlock the access to this post.', 'gamipress-restrict-unlock' ),
                'type' => 'group',
                'options'     => array(
                    'add_button'    => __( 'Add Requirement', 'gamipress-restrict-unlock' ),
                    'remove_button' => __( 'Remove Requirement', 'gamipress-restrict-unlock' ),
                ),
                'fields' => array(

                    // Requirement type

                    $prefix . 'type' => array(
                        'name' => __( 'When:', 'gamipress-restrict-unlock' ),
                        'type' => 'select',
                        'options' => array(
                            'points-balance'        => __( 'Reach a points balance', 'gamipress-restrict-unlock' ),
                            'earn-rank'             => __( 'Reach a specific rank', 'gamipress-restrict-unlock' ),
                            'specific-achievement'  => __( 'Unlock a specific achievement of type', 'gamipress-restrict-unlock' ),
                            'any-achievement'       => __( 'Unlock any achievement of type', 'gamipress-restrict-unlock' ),
                            'all-achievements'     	=> __( 'Unlock all achievements of type', 'gamipress-restrict-unlock' ),
                        ),
                    ),

                    // Requirement points

                    $prefix . 'points' => array(
                        'name' => __( 'Points:', 'gamipress-restrict-unlock' ),
                        'type' => 'text',
                        'attributes' => array(
                            'type' => 'number',
                            'min' => '1'
                        ),
                        'default' => 1,
                    ),
                    $prefix . 'points_type' => array(
                        'type' => 'select',
                        'option_all'  => false,
                        'option_none' => true,
                        'options_cb' => 'gamipress_options_cb_points_types',
                    ),

                    // Requirement achievement

                    $prefix . 'achievement_type' => array(
                        'name' => __( 'Achievement:', 'gamipress-restrict-unlock' ),
                        'type' => 'select',
                        'option_all'  => false,
                        'option_none' => true,
                        'options_cb' => 'gamipress_options_cb_achievement_types',
                    ),
                    $prefix . 'achievement' => array(
                        'name' => __( 'Achievement:', 'gamipress-restrict-unlock' ),
                        'type' => 'advanced_select',
                        'options_cb' => 'gamipress_options_cb_posts',
                        'classes' 	        => 'gamipress-post-selector',
                        'attributes' 	    => array(
                            'data-post-type' => implode( ',',  gamipress_get_achievement_types_slugs() ),
                            'data-placeholder' => __( 'Select an achievement', 'gamipress-restrict-unlock' ),
                        ),
                    ),

                    $prefix . 'count' => array(
                        'desc' => __( 'time(s)', 'gamipress-restrict-unlock' ),
                        'type' => 'text',
                        'attributes' => array(
                            'type' => 'number',
                            'min' => '1'
                        ),
                        'default' => 1
                    ),

                    // Requirement rank

                    $prefix . 'rank' => array(
                        'name' => __( 'Rank:', 'gamipress-restrict-unlock' ),
                        'type' => 'advanced_select',
                        'options_cb' => 'gamipress_options_cb_posts',
                        'classes' 	        => 'gamipress-post-selector',
                        'attributes' 	    => array(
                            'data-post-type' => implode( ',',  gamipress_get_rank_types_slugs() ),
                            'data-placeholder' => __( 'Select a rank', 'gamipress-restrict-unlock' ),
                        ),
                    ),

                    // Requirement label

                    $prefix . 'label' => array(
                        'name' => __( 'Label:', 'gamipress-restrict-unlock' ),
                        'type' => 'text',
                    ),


                )
            ),

            $prefix . 'informational_text' => array(
                'name' => __( 'Informational Text', 'gamipress-restrict-unlock' ),
                'desc' => __( 'Text to show to users that don\'t meet the requirements. Available tags:', 'gamipress-restrict-unlock' )
                    . gamipress_restrict_unlock_get_pattern_tags_html(),
                'type' => 'wysiwyg',
                'default_cb' => 'gamipress_restrict_unlock_informational_text_default_cb',
            ),

            // Roles

            $prefix . 'restricted_roles' => array(
                'name'          => __( 'Restricted Roles', 'gamipress-restrict-unlock' ),
                'desc'          => __( 'Restrict unlock to users by role.', 'gamipress-restrict-unlock' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'classes'       => 'gamipress-selector',
                'options_cb'    => 'gamipress_restrict_unlock_get_roles_options',
            ),
            $prefix . 'granted_roles' => array(
                'name'          => __( 'Granted Roles', 'gamipress-restrict-unlock' ),
                'desc'          => __( 'Allow unlock to users by role.', 'gamipress-restrict-unlock' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'classes'       => 'gamipress-selector',
                'options_cb'    => 'gamipress_restrict_unlock_get_roles_options',
            ),

            // Users

            $prefix . 'restricted_users' => array(
                'name'          => __( 'Restricted Users', 'gamipress-restrict-unlock' ),
                'desc'          => __( 'Restrict unlock to the users you want.', 'gamipress-restrict-unlock' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'classes'       => 'gamipress-user-selector',
                'options_cb'    => 'gamipress_options_cb_users',
            ),
            $prefix . 'granted_users' => array(
                'name'          => __( 'Granted Users', 'gamipress-restrict-unlock' ),
                'desc'          => __( 'Allow unlock to the users you want.', 'gamipress-restrict-unlock' ),
                'type'          => 'advanced_select',
                'multiple'      => true,
                'classes'       => 'gamipress-user-selector',
                'options_cb'    => 'gamipress_options_cb_users',
            ),
        ),
        array(
            'vertical_tabs' => true,
            'tabs' => array(
                'restrictions' => array(
                    'icon' => 'dashicons-lock',
                    'title' => __( 'Restrictions', 'gamipress-restrict-unlock' ),
                    'fields' => array(
                        $prefix . 'restrict',
                        $prefix . 'restrict_points_awards',
                        $prefix . 'restrict_points_deducts',
                        $prefix . 'restrict_points_steps',
                        $prefix . 'restrict_points_rank_requirements',
                        $prefix . 'unlock_by',
                        $prefix . 'access_with_points',
                        $prefix . 'points_to_access',
                        $prefix . 'requirements',
                        $prefix . 'informational_text',
                    ),
                ),
                'users' => array(
                    'icon' => 'dashicons-admin-users',
                    'title' => __( 'Users', 'gamipress-restrict-unlock' ),
                    'fields' => array(
                        $prefix . 'restricted_roles',
                        $prefix . 'granted_roles',
                        $prefix . 'restricted_users',
                        $prefix . 'granted_users',
                    ),
                ),
            )
        )
    );

}
add_action( 'cmb2_admin_init', 'gamipress_restrict_unlock_meta_boxes' );

/**
 * Show field on specific post types
 *
 * @since 1.0.0
 *
 * @param  object $cmb CMB2 object
 *
 * @return bool        True/false whether to show the field
 */
function gamipress_restrict_unlock_show_on_post_type( $cmb ) {

    $post_types = $cmb->prop( 'post_type', array() );

    // Check if object post type is on allowed post types
    return in_array( get_post_type( $cmb->object_id() ), $post_types, true );

}

/**
 * Default text callback for the informational text field
 *
 * @since 1.0.0
 *
 * @param  object $field    CMB2_Field object
 * @param  object $cmb      CMB2 object
 *
 * @return string
 */
function gamipress_restrict_unlock_informational_text_default_cb( $field, $cmb ) {

    $post_type = get_post_type( $cmb->object_id() );

    // Points type
    $label = gamipress_get_points_type_singular( $post_type );

    // Achievement type
    if( ! $label )
        $label = gamipress_get_achievement_type_singular( $post_type );

    // Rank type
    if( ! $label )
        $label = gamipress_get_rank_type_singular( $post_type );

    if( ! $label )
        $label = '';

    return sprintf( __( 'To unlock this %s you need to meet the next requirements:', 'gamipress-restrict-unlock' ), $label )
        . "\n" . '{requirements}';

}