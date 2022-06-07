<?php
/**
 * Share buttons template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/social-share/share-buttons.php
 */
global $gamipress_social_share_template_args;

// Shorthand
$a = $gamipress_social_share_template_args;

// Original shortcode attributes
$atts = $a['atts'];

// Setup the URL
$url = ( empty( $atts['url'] ) ? get_the_permalink( get_the_ID() ) : $atts['url'] ); ?>

<div class="gamipress-social-share gamipress-social-share-alignment-<?php echo $atts['alignment']; ?>" data-url="<?php echo esc_attr( $url ); ?>">

    <?php if( ! empty( $atts['title'] ) ) : ?>
        <div class="gamipress-social-share-title">
            <h3><?php echo $atts['title']; ?></h3>
        </div>
    <?php endif; ?>

    <div class="gamipress-social-share-buttons">

        <?php if( $atts['twitter'] === 'yes' ) :

            // Remove @ on username
            $atts['twitter_username'] = str_replace( '@', '', $atts['twitter_username'] );

            // Process twitter pattern
            $atts['twitter_text'] = gamipress_social_share_parse_pattern( $atts['twitter_pattern'] );

            // Initialize template args global
            $gamipress_social_share_template_args = gamipress_social_share_get_network_atts( 'twitter', $atts );

            $gamipress_social_share_template_args['url'] = $url;

            /**
             * Filter twitter template args
             *
             * @since 1.1.1
             *
             * @param array $template_args  Template args (based on attributes given)
             * @param array $atts           Shortcode, block or widget given attributes
             *
             * @return array
             */
            $gamipress_social_share_template_args = apply_filters( 'gamipress_social_share_twitter_template_args', $gamipress_social_share_template_args, $atts );

            gamipress_get_template_part( 'twitter' );

        endif;


        if( $atts['facebook'] === 'yes' ) :

            // Initialize template args global
            $gamipress_social_share_template_args = gamipress_social_share_get_network_atts( 'facebook', $atts );

            $gamipress_social_share_template_args['url'] = $url;

            /**
             * Filter facebook template args
             *
             * @since 1.1.1
             *
             * @param array $template_args  Template args (based on attributes given)
             * @param array $atts           Shortcode, block or widget given attributes
             *
             * @return array
             */
            $gamipress_social_share_template_args = apply_filters( 'gamipress_social_share_facebook_template_args', $gamipress_social_share_template_args, $atts );

            gamipress_get_template_part( 'facebook' );

        endif;

        if( $atts['linkedin'] === 'yes' ) :

            // Initialize template args global
            $gamipress_social_share_template_args = gamipress_social_share_get_network_atts( 'linkedin', $atts );

            $gamipress_social_share_template_args['url'] = $url;

            /**
             * Filter LinkedIn template args
             *
             * @since 1.1.1
             *
             * @param array $template_args  Template args (based on attributes given)
             * @param array $atts           Shortcode, block or widget given attributes
             *
             * @return array
             */
            $gamipress_social_share_template_args = apply_filters( 'gamipress_social_share_linkedin_template_args', $gamipress_social_share_template_args, $atts );

            gamipress_get_template_part( 'linkedin' );

        endif;

        if( $atts['pinterest'] === 'yes' ) :

            $thumbnail = ( empty( $atts['pinterest_thumbnail'] ) ?  get_the_post_thumbnail_url( get_the_ID() ) : $atts['pinterest_thumbnail'] );

            if( $thumbnail ) {

                // Process pinterest description pattern
                $atts['pinterest_description'] = gamipress_social_share_parse_pattern( $atts['pinterest_description'] );

                // Initialize template args global
                $gamipress_social_share_template_args = gamipress_social_share_get_network_atts( 'pinterest', $atts );

                $gamipress_social_share_template_args['url'] = $url;
                $gamipress_social_share_template_args['media'] = $thumbnail;

                /**
                 * Filter Pinterest template args
                 *
                 * @since 1.1.1
                 *
                 * @param array $template_args  Template args (based on attributes given)
                 * @param array $atts           Shortcode, block or widget given attributes
                 *
                 * @return array
                 */
                $gamipress_social_share_template_args = apply_filters( 'gamipress_social_share_pinterest_template_args', $gamipress_social_share_template_args, $atts );

                gamipress_get_template_part( 'pinterest' );

            }

        endif; ?>

    </div>

</div>
