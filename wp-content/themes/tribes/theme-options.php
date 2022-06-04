<?php

function ct_tribes_register_theme_page() {
	add_theme_page( 
		sprintf( esc_html__( '%s Dashboard', 'tribes' ), wp_get_theme() ), 
		sprintf( esc_html__( '%s Dashboard', 'tribes' ), wp_get_theme() ), 
		'edit_theme_options', 
		'tribes-options', 
		'ct_tribes_options_content'
	);
}
add_action( 'admin_menu', 'ct_tribes_register_theme_page' );

function ct_tribes_options_content() {

	$pro_url = 'https://www.competethemes.com/tribes-pro/?utm_source=wp-dashboard&utm_medium=Dashboard&utm_campaign=Tribes%20Pro%20-%20Dashboard';
	?>
	<div id="tribes-dashboard-wrap" class="wrap tribes-dashboard-wrap">
		<h2><?php printf( esc_html__( '%s Dashboard', 'tribes' ), wp_get_theme() ); ?></h2>
		<?php do_action( 'ct_tribes_theme_options_before' ); ?>
		<div class="main">
			<?php if ( function_exists( 'ct_tribes_pro_init' ) ) : ?>
			<div class="thanks-upgrading" style="background-image: url(<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/bg-texture.png'; ?>)">
				<h3>Thanks for upgrading!</h3>
				<p>You can find the new features in the Customizer</p>
			</div>
			<?php endif; ?>
			<?php if ( !function_exists( 'ct_tribes_pro_init' ) ) : ?>
			<div class="getting-started">
				<h3>Get Started with Tribes</h3>
				<p>Follow this step-by-step guide to customize your website with Tribes:</p>
				<a href="https://www.competethemes.com/help/getting-started-tribes/" target="_blank">Read the Getting Started Guide</a>
			</div>
			<div class="pro">
				<h3>Customize More with Tribes Pro</h3>
				<p>Add 13 new customization features to your site with the <a href="<?php echo $pro_url; ?>" target="_blank">Tribes Pro</a> plugin.</p>
				<ul class="feature-list">
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/layouts.png'; ?>" />
						</div>
						<div class="text">
							<h4>New Layouts</h4>
							<p>New layouts help your content look and perform its best. You can switch to new layouts effortlessly from the Customizer, or from specific posts or pages.</p>
							<p>Tribes Pro adds 7 new layouts.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/custom-colors.png'; ?>" />
						</div>
						<div class="text">
							<h4>Custom Colors</h4>
							<p>Custom colors let you match the color of your site with your brand. Point-and-click to select a color, and watch your site update instantly.</p>
							<p>With 49 color controls, you can change the color of any element on your site.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/fonts.png'; ?>" />
						</div>
						<div class="text">
							<h4>New Fonts</h4>
							<p>Stylish new fonts add character and charm to your content. Select and instantly preview fonts from the Customizer.</p>
							<p>Since Tribes Pro is powered by Google Fonts, it comes with 728 fonts for you to choose from.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/font-sizes.png'; ?>" />
						</div>
						<div class="text">
							<h4>Font Sizes</h4>
							<p>Change the size of the fonts used throughout Tribes. Optimize the reading experience for the custom fonts you choose.</p>
							<p>Tribes Pro has font size controls including mobile and desktop specific settings.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/header-image.png'; ?>" />
						</div>
						<div class="text">
							<h4>Flexible Header Image</h4>
							<p>Header images welcome visitors and set your site apart. Upload your image and quickly resize it to the perfect size.</p>
							<p>Display the header image on just the homepage, or leave it sitewide and link it to the homepage.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/featured-videos.png'; ?>" />
						</div>
						<div class="text">
							<h4>Featured Videos</h4>
							<p>Featured Videos are an easy way to share videos in place of Featured Images. Instantly embed a Youtube video by copying and pasting its URL into an input.</p>
							<p>Tribes Pro auto-embeds videos from Youtube, Vimeo, DailyMotion, Flickr, Animoto, TED, Blip, Cloudup, FunnyOrDie, Hulu, Vine, WordPress.tv, and VideoPress.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/featured-sliders.png'; ?>" />
						</div>
						<div class="text">
							<h4>Featured Sliders</h4>
							<p>Featured Sliders are an easy way to share image sliders in place of Featured Images. Quickly add responsive sliders to any page or post.</p>
							<p>Tribes Pro integrates with the free Meta Slider plugin with styling and sizing controls for your sliders.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/background-images.png'; ?>" />
						</div>
						<div class="text">
							<h4>Background Images</h4>
							<p>Background images help you stand out from the rest. Upload a unique image to use as the backdrop for your site.</p>
							<p>Background images are automatically centered and sized to fit the screen.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/background-textures.png'; ?>" />
						</div>
						<div class="text">
							<h4>Background Textures</h4>
							<p>Background textures transform the look and feel of your site. Switch to a textured background with a click.</p>
							<p>Tribes Pro includes 39 bundled textures to choose from.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/featured-image-size.png'; ?>" />
						</div>
						<div class="text">
							<h4>Featured Image Size</h4>
							<p>Set each Featured Image to the perfect size. You can change the aspect ratio for all Featured Images and individual Featured Images with ease.</p>
							<p>Tribes Pro includes twelve different aspect ratios for your Featured Images.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/widget-areas.png'; ?>" />
						</div>
						<div class="text">
							<h4>New Widget Areas</h4>
							<p>Utilize a sidebar and four additional widget areas for greater flexibility. Increase ad revenue and generate more email subscribers by adding widgets throughout your site.</p>
							<p>Tribes Pro adds 4 new widget areas.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/display-controls.png'; ?>" />
						</div>
						<div class="text">
							<h4>Display Controls</h4>
							<p>Display controls let you display the parts of your site you want to show off, and hide the rest. Remove elements with just one click.</p>
							<p>Tribes Pro includes display controls for 10 different elements.</p>
						</div>
					</li>
					<li>
						<div class="image">
							<img src="<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/footer-text.png'; ?>" />
						</div>
						<div class="text">
							<h4>Custom Footer Text</h4>
							<p>Custom footer text lets you further brand your site. Just start typing to add your own text to the footer.</p>
							<p>The footer text supports plain text and full HTML for adding links.</p>
						</div>
					</li>
				</ul>
				<p><a href="<?php echo $pro_url; ?>" target="_blank">Click here</a> to view Tribes Pro now, and see what it can do for your site.</p>
			</div>
			<div class="pro-ad" style="background-image: url(<?php echo trailingslashit(get_template_directory_uri()) . 'assets/images/bg-texture.png'; ?>)">
				<h3>Add Incredible Flexibility to Your Site</h3>
				<p>Start customizing with Tribes Pro today</p>
				<a href="<?php echo $pro_url; ?>" target="_blank">View Tribes Pro</a>
			</div>
			<?php endif; ?>
		</div>
		<div class="sidebar">
			<div class="dashboard-widget">
				<h4>More Amazing Resources</h4>
				<ul>
					<li><a href="https://www.competethemes.com/documentation/tribes-support-center/" target="_blank">Tribes Support Center</a></li>
					<li><a href="https://wordpress.org/support/theme/tribes" target="_blank">Support Forum</a></li>
					<li><a href="https://www.competethemes.com/help/tribes-changelog/" target="_blank">Changelog</a></li>
					<li><a href="https://www.competethemes.com/help/tribes-css-snippets/" target="_blank">CSS Snippets</a></li>
					<li><a href="https://www.competethemes.com/help/child-theme-tribes/" target="_blank">Starter child theme</a></li>
					<li><a href="https://www.competethemes.com/help/tribes-demo-data/" target="_blank">Tribes demo data</a></li>
					<li><a href="<?php echo $pro_url; ?>" target="_blank">Tribes Pro</a></li>
				</ul>
			</div>
			<div class="dashboard-widget">
				<h4>Reset Customizer Settings</h4>
				<p><b>Warning:</b> Clicking this button will erase the Tribes theme's current settings in the Customizer.</p>
				<form method="post">
					<input type="hidden" name="tribes_reset_customizer" value="tribes_reset_customizer_settings"/>
					<p>
						<?php wp_nonce_field( 'tribes_reset_customizer_nonce', 'tribes_reset_customizer_nonce' ); ?>
						<?php submit_button( 'Reset Customizer Settings', 'delete', 'delete', false ); ?>
					</p>
				</form>
			</div>
		</div>
		<?php do_action( 'ct_tribes_theme_options_after' ); ?>
	</div>
<?php }