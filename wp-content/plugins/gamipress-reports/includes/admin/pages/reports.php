<?php
/**
 * Admin Reports Page
 *
 * @package     GamiPress\Reports\Admin\Reports
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register points reports page.
 *
 * @since  1.0.0
 *
 * @param string $page_id
 * @param string $page_title
 *
 * @return void
 */
function gamipress_register_reports_page( $page_id, $page_title ) {

    $tabs = array();
    $boxes = array();

    $is_reports_page = ( isset( $_GET['page'] ) && $_GET['page'] === "gamipress_reports_{$page_id}" );

    if( $is_reports_page ) {

        // Loop points reports sections
        foreach( gamipress_get_reports_sections( $page_id ) as $section_id => $section ) {

            $meta_boxes = array();

            /**
             * Filter: gamipress_reports_{$page_id}_{$section_id}_meta_boxes
             *
             * @param array $meta_boxes
             *
             * @return array
             */
            $meta_boxes = apply_filters( "gamipress_reports_{$page_id}_{$section_id}_meta_boxes", $meta_boxes );

            if( ! empty( $meta_boxes ) ) {

                // Loop points reports section meta boxes
                foreach( $meta_boxes as $meta_box_id => $meta_box ) {

                    // Let's register meta boxes without title
                    if( ! isset( $meta_box['title'] ) ) {
                        $meta_box['title'] = $meta_box_id;
                    }

                    // Shortcut for single content field
                    if( isset( $meta_box['content'] ) || isset( $meta_box['content_cb'] ) ) {

                        if( ! isset( $meta_box['fields'] ) ) {
                            $meta_box['fields'] = array();
                        }

                        $meta_box['fields'][$meta_box_id] = array(
                            'type' => 'html',
                        );

                        if( isset( $meta_box['content'] ) ) {

                            $meta_box['fields'][$meta_box_id]['content'] = $meta_box['content'];
                            unset( $meta_box['content'] );

                        } else if( isset( $meta_box['content_cb'] ) ) {

                            $meta_box['fields'][$meta_box_id]['content_cb'] = $meta_box['content_cb'];
                            unset( $meta_box['content_cb'] );

                        }

                    }

                    // Only add points reports meta box if has fields
                    if( isset( $meta_box['fields'] ) && ! empty( $meta_box['fields'] ) ) {

                        // Loop meta box fields
                        foreach( $meta_box['fields'] as $field_id => $field ) {

                            $field['id'] = $field_id;

                            $meta_box['fields'][$field_id] = $field;

                        }

                        $meta_box['id'] = $meta_box_id;

                        $meta_box['display_cb'] = false;
                        $meta_box['admin_menu_hook'] = false;
                        $meta_box['priority'] = 'high'; // Fixes issue with CMB2 2.9.0

                        $meta_box['show_on'] = array(
                            'key'   => 'options-page',
                            'value' => array( "gamipress_reports_{$page_id}" ),
                        );

                        $box = new_cmb2_box( $meta_box );

                        $box->object_type( 'options-page' );

                        $boxes[] = $box;

                    }
                }

                $tabs[] = array(
                    'id'    => $section_id,
                    'title' => ( ( isset( $section['icon'] ) ) ? '<i class="dashicons ' . $section['icon'] . '"></i>' : '' ) . $section['title'],
                    'desc'  => '',
                    'boxes' => array_keys( $meta_boxes ),
                );
            }
        }

    }

    $minimum_role = gamipress_get_manager_capability();

    // Create the options page
    new Cmb2_Metatabs_Options( array(
        'key'      => "gamipress_reports_{$page_id}",
        'class'    => "gamipress-page gamipress-reports gamipress-reports-{$page_id}",
        'title'    => $page_title,
        'topmenu'  => 'gamipress_reports_dashboard',
        'view_capability' => $minimum_role,
        'cols'     => 1,
        'boxes'    => $boxes,
        'tabs'     => $tabs,
        'menuargs' => array(
            'menu_title'        => $page_title,
            'capability'        => $minimum_role,
            'view_capability'   => $minimum_role,
        ),
        'savetxt' => false,
        'resettxt' => false,
    ) );

}

/**
 * Reports page sections
 *
 * @since  1.0.0
 *
 * @return array
 */
function gamipress_get_reports_sections( $page_id ) {

    $reports_sections = array();

    return apply_filters( "gamipress_reports_{$page_id}_sections", $reports_sections );

}