<!DOCTYPE html>

<!--[if gte IE 9]>
<html class="ie9" <?php language_attributes(); ?>>
<![endif]-->
<html <?php language_attributes(); ?>>

<head>
	<?php wp_head(); ?>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-1Q6PYPJV8R"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	
	  gtag('config', 'G-1Q6PYPJV8R');
	</script>
</head>

<body id="<?php print get_stylesheet(); ?>" <?php body_class(); ?>>
<?php do_action( 'ct_tribes_body_top' ); ?>
<?php 
if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
	} else {
			do_action( 'wp_body_open' );
} ?>
<a class="skip-content" href="#main"><?php esc_html_e( 'Press "Enter" to skip to content', 'tribes' ); ?></a>
<div id="overflow-container" class="overflow-container">
	<div id="theme-container" class="theme-container">
		<div id="max-width" class="max-width">
			<?php do_action( 'ct_tribes_before_header' ); ?>
			<?php
			// Elementor `header` location
			if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) :
			?>
			<header class="site-header" id="site-header" role="banner">
				<div id="title-container" class="title-container">
					<?php get_template_part( 'logo' ) ?>
				</div>
				<?php 
					$site_url = get_site_url();
				?>
					<ul class="<?php echo is_user_logged_in() ? 'tribe-icons-menu' : 'tribe-icons-menu-logged-out' ?>">
						<a href="<?php echo $site_url; ?>/coin_rewards"><li class="dashicons dashicons-awards" style=""></li></a>
						<a href="<?php echo $site_url; ?>/halls/"><li class="dashicons dashicons-tag" style=""></li></a>
						<a href="<?php echo $site_url; ?>/post/"><li class="dashicons dashicons-welcome-write-blog" style=""></li></a>
						<?php if (is_user_logged_in()){ ?>
							<a href="<?php echo $site_url; ?>/wallet"><li class="dashicons dashicons-vault" style=""></li></a>
							<?php
							}
						?>
					</ul>

				
				<button id="toggle-navigation" class="toggle-navigation" name="toggle-navigation" aria-expanded="false">
					<span class="screen-reader-text"><?php echo esc_html_x( 'open menu', 'verb: open the menu', 'tribes' ); ?></span>
					<?php echo ct_tribes_svg_output( 'toggle-navigation' ); ?>
				</button>
	
				<div id="menu-primary-container" class="menu-primary-container">
					<div class="max-width">

						<div id="scroll-container" class="scroll-container">
							<?php if ( get_bloginfo( 'description' ) ) {
								echo '<p class="tagline">' . esc_html( get_bloginfo( 'description' ) ) . '</p>';
							} ?>
							<?php get_template_part( 'menu', 'primary' ); ?>
							<?php get_template_part( 'content/search-bar' ); ?>
							<?php ct_tribes_social_icons_output(); ?>
						</div>
					</div>
				</div>
			</header>
			<?php endif; ?>
			<?php do_action( 'ct_tribes_after_header' ); ?>
			<section id="main" class="main" role="main">
				<?php do_action( 'ct_tribes_main_top' );
				if ( function_exists( 'yoast_breadcrumb' ) ) {
					yoast_breadcrumb( '<p id="breadcrumbs">', '</p>' );
				}
