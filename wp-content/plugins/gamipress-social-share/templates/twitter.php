<?php
/**
 * Facebook template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/social-share/twitter.php
 */
global $gamipress_social_share_template_args;

// Shorthand
$a = $gamipress_social_share_template_args; ?>

<div class="gamipress-social-share-button gamipress-social-share-twitter">

    <a href="https://twitter.com/share"
       class="twitter-share-button"
       data-count="<?php echo $a['count_box']; ?>"
       data-size="<?php echo $a['button_size']; ?>"
       data-counturl="<?php echo $a['url']; ?>"
       data-url="<?php echo $a['url']; ?>"
       data-text="<?php echo $a['text']; ?>"
       data-via="<?php echo $a['username']; ?>"
       data-related=""></a>

</div>