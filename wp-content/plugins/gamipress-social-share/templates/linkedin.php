<?php
/**
 * LinkedIn template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/social-share/linkedin.php
 */
global $gamipress_social_share_template_args;

// Shorthand
$a = $gamipress_social_share_template_args; ?>

<div class="gamipress-social-share-button gamipress-social-share-linkedin">
    <script type="IN/Share"
            data-counter="<?php echo $a['counter']; ?>"
            <?php // Added those attributes for maximum backward compatibility ?>
            data-success="gamipress_social_share_linkedin"
            data-onsuccess="gamipress_social_share_linkedin"
            data-onSuccess="gamipress_social_share_linkedin"
            data-url="<?php echo $a['url']; ?>"></script>
</div>