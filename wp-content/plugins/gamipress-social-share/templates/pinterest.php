<?php
/**
 * Pinterest template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/social-share/pinterest.php
 */
global $gamipress_social_share_template_args;

// Shorthand
$a = $gamipress_social_share_template_args; ?>

<div class="gamipress-social-share-button gamipress-social-share-pinterest">

    <a href="https://www.pinterest.com/pin/create/button/"
       data-pin-do="buttonPin"
       data-pin-url="<?php echo $a['url']; ?>"
       data-pin-media="<?php echo $a['media']; ?>"
       data-pin-description="<?php echo $a['description']; ?>"
       <?php if ( $a['round'] === 'yes' ) : ?>data-pin-round="true"<?php endif; ?>
       <?php if ( $a['tall'] === 'yes' ) : ?>data-pin-tall="true"<?php endif; ?>
       <?php if ( $a['count'] !== 'none' ) : ?>data-pin-count="<?php echo $a['count']; ?>"<?php endif; ?>
       ></a>

</div>
