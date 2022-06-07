<?php
/**
 * Facebook template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/social-share/facebook.php
 */
global $gamipress_social_share_template_args;

// Shorthand
$a = $gamipress_social_share_template_args; ?>

<div class="gamipress-social-share-button gamipress-social-share-facebook">

    <?php // Like/Recommend button ?>
    <?php if( in_array( $a['action'], array( 'like', 'recommend' ) ) ) : ?>
        <div class="fb-like"
             data-href="<?php echo $a['url']; ?>"
             data-send="true"
             data-action="<?php echo $a['action']; ?>"
             data-layout="<?php echo $a['button_layout']; ?>"
             data-size="<?php echo $a['button_size']; ?>"
             data-share="false"
             data-width=""
             data-show-faces="false"></div>
    <?php endif; ?>

    <?php // Share button ?>
    <?php if( $a['action'] === 'share' || $a['share'] === 'yes' ) : ?>
        <div class="gamipress-social-share-facebook-share"
             data-href="<?php echo $a['url']; ?>"
             data-layout="<?php echo $a['button_layout']; ?>"
             data-size="<?php echo $a['button_size']; ?>">
            <span><?php _e( 'Share', 'gamipress-social-share' ); ?></span>
        </div>
    <?php endif; ?>

</div>