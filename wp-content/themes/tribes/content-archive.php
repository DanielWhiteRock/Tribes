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
			<div class="tribe-comments">
				<a href="<?php echo esc_url( get_permalink() ); ?>"><?php 
					echo get_comments_number( $post_id ); 
				?></a>	<span class="dashicons dashicons-admin-comments"></span>
			</div>
			</div>
			<div class="post-content">
				<?php ct_tribes_excerpt(); ?>
			</div>

			
		</div>
	</article>
	<?php do_action( 'ct_tribes_archive_post_after' ); ?>
</div>