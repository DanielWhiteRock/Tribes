<?php do_action( 'ct_tribes_main_bottom' ); ?>
</section> <!-- .main -->

<?php do_action( 'ct_tribes_after_main' ); ?>

<?php 
// Elementor `footer` location
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) :
?>
<footer id="site-footer" class="site-footer" role="contentinfo">
    <?php do_action( 'ct_tribes_footer_top' ); ?>
    <div class="design-credit">
        <span>
          <a href="https://TheTribes.io"> TheTribes.io </a> | <a href="<?php echo get_site_url()  ?>/earn-coins-tokens-and-nfts/"> How to Earn Coins </a> | <a href="https://discord.gg/J4yxUDcR3z"> Discord </a> | <a href="<?php echo get_site_url()  ?>/road-map-2022/"> Road Map 2022</a> | <a href="<?php echo get_site_url()  ?>/white-paper/"> White Paper </a>
        </span>
    </div>
</footer>
<?php endif; ?>
</div><!-- .max-width -->
</div><!-- .theme-container -->
</div><!-- .overflow-container -->

<?php do_action( 'ct_tribes_body_bottom' ); ?>

<?php wp_footer(); ?>
<script type="text/javascript" src="<?php echo get_site_url()  ?>/wp-content/themes/tribes-child/js/tribe-scripts.js"></script>
</body>
</html>