<?php
/**
 * Vikinger Template - BuddyPress Register Page
 * 
 * Displays the member registration page
 * 
 * @package Vikinger
 * @since 1.0.0
 * @author Odin Design Themes (https://odindesignthemes.com/)
 * 
 */

  $login_page_default_logo_id = get_theme_mod('vikinger_loginregister_setting_login_logo', false);

  if ($login_page_default_logo_id) {
    $login_page_default_logo_url = wp_get_attachment_image_src($login_page_default_logo_id , 'full')[0];
  } else {
    $login_page_default_logo_url = VIKINGER_URL . '/img/login/logo.png';
  }

  $login_page_default_background_id = get_theme_mod('vikinger_loginregister_setting_login_background', false);

  if ($login_page_default_background_id) {
    $login_page_default_background_url = wp_get_attachment_image_src($login_page_default_background_id , 'full')[0];
  } else {
    $login_page_default_background_url = VIKINGER_URL . '/img/login/background.jpg';
  }

  $login_page_pretitle  = get_theme_mod('vikinger_loginregister_setting_login_pretitle', esc_html_x('Welcome to', 'Login Page - Pre Title', 'vikinger'));
  $login_page_title     = get_theme_mod('vikinger_loginregister_setting_login_title', esc_html_x('Vikinger', 'Login Page - Title', 'vikinger'));
  $login_page_text      = get_theme_mod('vikinger_loginregister_setting_login_text', sprintf(esc_html__('%sThe next generation WordPress+Buddypress social community!%s Connect with your friends with full profiles, reactions, groups, badges, quests, ranks, credits and %smuch more to come!%s', 'vikinger'), '<span class="bold">', '</span>', '<span class="bold">', '</span>'));

?>
<script src='https://www.google.com/recaptcha/api.js'></script>

<div class="vkregister-header" style="background: url('<?php echo esc_url($login_page_default_background_url); ?>') center center / cover no-repeat">
  <?php if ($login_page_pretitle) : ?>
    <p class="vkregister-header-pretitle"><?php echo esc_html($login_page_pretitle); ?></p>
  <?php endif; ?>

  <?php if ($login_page_title) : ?>
    <p class="vkregister-header-title"><?php echo esc_html($login_page_title); ?></p>
  <?php endif; ?>

  <?php if ($login_page_text) : ?>
    <p class="vkregister-header-text"><?php echo $login_page_text; ?></p>
  <?php endif; ?>
  </div>
</div>

<div id="buddypress" data-simplebar>

  <!-- VKREGISTER LOGO -->
  <img class="vkregister-logo" src="<?php echo esc_url($login_page_default_logo_url); ?>" alt="logo">
  <!-- /VKREGISTER LOGO -->

  <!-- VKREGISTER FORM TITLE -->
  <p class="vkregister-form-title"><?php esc_html_e('Welcome!', 'vikinger'); ?></p>
  <!-- /VKREGISTER FORM TITLE -->

	<?php

	/**
	 * Fires at the top of the BuddyPress member registration page template.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_register_page' ); ?>

	<div class="page" id="register-page">

		<form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

		<?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>

			<div id="template-notices" role="alert" aria-atomic="true">
				<?php

				/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
				do_action( 'template_notices' ); ?>

			</div>

			<?php

			/**
			 * Fires before the display of the registration disabled message.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_before_registration_disabled' ); ?>

				<p><?php _e( 'User registration is currently not allowed.', 'buddypress' ); ?></p>

			<?php

			/**
			 * Fires after the display of the registration disabled message.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_after_registration_disabled' ); ?>
		<?php endif; // registration-disabled signup step ?>

		<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

			<div id="template-notices" role="alert" aria-atomic="true">
				<?php

				/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
				do_action( 'template_notices' ); ?>

			</div>

			<p><?php _e( 'Registering for this site is easy. Just fill in the fields below, and we\'ll get a new account set up for you in no time.', 'buddypress' ); ?></p>

			<?php

			/**
			 * Fires before the display of member registration account details fields.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_before_account_details_fields' ); ?>

			<div class="register-section" id="basic-details-section">

				<?php /***** Basic Account Details ******/ ?>

				<h2><?php _e( 'Account Details', 'buddypress' ); ?></h2>

				<label for="signup_username"><?php _e( 'Username', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
				<?php

				/**
				 * Fires and displays any member registration username errors.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_signup_username_errors' ); ?>
				<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value(); ?>" <?php bp_form_field_attributes( 'username' ); ?>/>

				<label for="signup_email"><?php _e( 'Email Address', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
				<?php

				/**
				 * Fires and displays any member registration email errors.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_signup_email_errors' ); ?>
				<input type="email" name="signup_email" id="signup_email" value="<?php bp_signup_email_value(); ?>" <?php bp_form_field_attributes( 'email' ); ?>/>

				<label for="signup_password"><?php _e( 'Choose a Password', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
				<?php

				/**
				 * Fires and displays any member registration password errors.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_signup_password_errors' ); ?>
				<input type="password" name="signup_password" id="signup_password" value="" class="password-entry" <?php bp_form_field_attributes( 'password' ); ?>/>
				<div id="pass-strength-result"></div>

				<label for="signup_password_confirm"><?php _e( 'Confirm Password', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
				<?php

				/**
				 * Fires and displays any member registration password confirmation errors.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_signup_password_confirm_errors' ); ?>
				<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" class="password-entry-confirm" <?php bp_form_field_attributes( 'password' ); ?>/>

				<?php

				/**
				 * Fires and displays any extra member registration details fields.
				 *
				 * @since 1.9.0
				 */
				do_action( 'bp_account_details_fields' ); ?>

			</div><!-- #basic-details-section -->

			<?php

			/**
			 * Fires after the display of member registration account details fields.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_after_account_details_fields' ); ?>

			<?php /***** Extra Profile Details ******/ ?>

			<?php if ( bp_is_active( 'xprofile' ) ) : ?>

				<?php

				/**
				 * Fires before the display of member registration xprofile fields.
				 *
				 * @since 1.2.4
				 */
				do_action( 'bp_before_signup_profile_fields' ); ?>

				<div class="register-section" id="profile-details-section">

					<h2><?php _e( 'Profile Details', 'buddypress' ); ?></h2>

					<?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
					<?php if ( bp_is_active( 'xprofile' ) ) : if ( bp_has_profile( bp_xprofile_signup_args() ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

					<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

						<div<?php bp_field_css_class( 'editfield' ); ?>>
							<fieldset>

							<?php
							$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
							$field_type->edit_field_html();

							/**
							 * Fires before the display of the visibility options for xprofile fields.
							 *
							 * @since 1.7.0
							 */
							do_action( 'bp_custom_profile_edit_fields_pre_visibility' );

							/**
							 * Fires after the display of the visibility options for xprofile fields.
							 *
							 * @since 1.1.0
							 */
							do_action( 'bp_custom_profile_edit_fields' ); ?>

							</fieldset>
						</div>

					<?php endwhile; ?>

					<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

					<?php endwhile; endif; endif; ?>

					<?php

					/**
					 * Fires and displays any extra member registration xprofile fields.
					 *
					 * @since 1.9.0
					 */
					do_action( 'bp_signup_profile_fields' ); ?>

				</div><!-- #profile-details-section -->

				<?php

				/**
				 * Fires after the display of member registration xprofile fields.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_after_signup_profile_fields' ); ?>

			<?php endif; ?>

			<?php if ( bp_get_blog_signup_allowed() ) : ?>

				<?php

				/**
				 * Fires before the display of member registration blog details fields.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_before_blog_details_fields' ); ?>

				<?php /***** Blog Creation Details ******/ ?>

				<div class="register-section" id="blog-details-section">

					<h2><?php _e( 'Blog Details', 'buddypress' ); ?></h2>

					<p><label for="signup_with_blog"><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes, I\'d like to create a new site', 'buddypress' ); ?></label></p>

					<div id="blog-details"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>

						<label for="signup_blog_url"><?php _e( 'Blog URL', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
						<?php

						/**
						 * Fires and displays any member registration blog URL errors.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_signup_blog_url_errors' ); ?>

						<?php if ( is_subdomain_install() ) : ?>
							http:// <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" /> .<?php bp_signup_subdomain_base(); ?>
						<?php else : ?>
							<?php echo home_url( '/' ); ?> <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" />
						<?php endif; ?>

						<label for="signup_blog_title"><?php _e( 'Site Title', 'buddypress' ); ?> <?php _e( '(required)', 'buddypress' ); ?></label>
						<?php

						/**
						 * Fires and displays any member registration blog title errors.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_signup_blog_title_errors' ); ?>
						<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value(); ?>" />

						<fieldset class="register-site">
							<legend class="label"><?php _e( 'Privacy: I would like my site to appear in search engines, and in public listings around this network.', 'buddypress' ); ?></legend>
							<?php

							/**
							 * Fires and displays any member registration blog privacy errors.
							 *
							 * @since 1.1.0
							 */
							do_action( 'bp_signup_blog_privacy_errors' ); ?>

							<label for="signup_blog_privacy_public"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == bp_get_signup_blog_privacy_value() || !bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'buddypress' ); ?></label>
							<label for="signup_blog_privacy_private"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'buddypress' ); ?></label>
						</fieldset>

						<?php

						/**
						 * Fires and displays any extra member registration blog details fields.
						 *
						 * @since 1.9.0
						 */
						do_action( 'bp_blog_details_fields' ); ?>

					</div>

				</div><!-- #blog-details-section -->

				<?php

				/**
				 * Fires after the display of member registration blog details fields.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_after_blog_details_fields' ); ?>

			<?php endif; ?>

			<?php

			/**
			 * Fires before the display of the registration submit buttons.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_before_registration_submit_buttons' ); ?>
            <div class="g-recaptcha" data-sitekey="6LeOgyQgAAAAADE8gY32ZOYRp6YDFTFU0_KaL2Jd"></div>
			<div class="submit">
				<input type="submit" name="signup_submit" id="signup_submit" value="<?php esc_attr_e( 'Complete Sign Up', 'buddypress' ); ?>" />
			</div>
            <!-- <script>
                jQuery(document).ready(function(){
                jQuery('#signup_form').on('submit',function(e){
                    e.preventDefault();
                    if (grecaptcha.getResponse() == ""){
                        alert("You can't proceed! Please fill Captchas");
                    } else {
                        jQuery(this).submit();

                    }
                })
            })
            </script> -->
			<?php

			/**
			 * Fires after the display of the registration submit buttons.
			 *
			 * @since 1.1.0
			 */
			do_action( 'bp_after_registration_submit_buttons' ); ?>

			<?php wp_nonce_field( 'bp_new_signup' ); ?>

		<?php endif; // request-details signup step ?>

		<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

			<div id="template-notices" role="alert" aria-atomic="true">
				<?php

				/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
				do_action( 'template_notices' ); ?>

			</div>

			<?php

			/**
			 * Fires before the display of the registration confirmed messages.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_before_registration_confirmed' ); ?>

			<div id="template-notices" role="alert" aria-atomic="true">
				<?php if ( bp_registration_needs_activation() ) : ?>
					<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ); ?></p>
				<?php else : ?>
					<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ); ?></p>
				<?php endif; ?>
			</div>

			<?php

			/**
			 * Fires after the display of the registration confirmed messages.
			 *
			 * @since 1.5.0
			 */
			do_action( 'bp_after_registration_confirmed' ); ?>

		<?php endif; // completed-confirmation signup step ?>

		<?php

		/**
		 * Fires and displays any custom signup steps.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_custom_signup_steps' ); ?>

		</form>

	</div>

	<?php

	/**
	 * Fires at the bottom of the BuddyPress member registration page template.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_register_page' ); ?>

	<?php

		$bp_directory_page_ids = bp_core_get_directory_page_ids();
		$activate_page = get_post($bp_directory_page_ids['activate']);
		$activate_page_link = get_permalink($activate_page->ID);

	?>

	<p class="vk-bp-register-page-nav">
		<a href="<?php echo esc_url(wp_login_url()); ?>"><?php esc_html_e('Login', 'vikinger'); ?></a>
		|
		<a href="<?php echo esc_url($activate_page_link); ?>"><?php esc_html_e('Activate', 'vikinger'); ?></a>
	</p>

</div><!-- #buddypress -->