<div <?php post_class(); ?>>
	<?php do_action( 'ct_tribes_archive_post_before' ); ?>
	<article>
		<?php ct_tribes_featured_image(); ?>
		<div class="post-container">
			<div class='post-header'>
				<?php do_action( 'ct_tribes_sticky_post_status' ); ?>
				<h2 class='post-title'>
					<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a>
				</h2>
				<?php get_template_part( 'content/post-byline' ); ?>
				<div class="tribe-share">
					<input type="text" value="<?php echo esc_url( get_permalink() ); ?>?ref=<?php $current_user = wp_get_current_user(); echo $current_user->user_login ?>">
					<?php echo do_shortcode('[gamipress_social_share]'); ?>
				</div>
				<div class="tribe-comments">
				
				<a href="<?php echo esc_url( get_permalink() ); ?>"><?php 
					echo get_comments_number( $post_id ); 
				?>	<span class="dashicons dashicons-admin-comments"></span></a>
					<span class="dashicons dashicons-share" ></span>
				<?php 
				global $current_user;
				get_currentuserinfo();
				if (is_user_logged_in() && $current_user->ID == $post->post_author)  {
					$post_link = get_edit_post_link( $post_id );
			        echo '<a href="'.$post_link.'">	<span class="dashicons dashicons-edit"></a>'; 
			    }
				?>
			</div>
			</div>
			<div class="post-content">
				<?php ct_tribes_excerpt(); ?>
			</div>
		</div>
	</article>
	<?php do_action( 'ct_tribes_archive_post_after' ); ?>
</div>
