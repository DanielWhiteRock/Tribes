<div <?php post_class(); ?>>
	<?php do_action( 'ct_tribes_post_before' ); ?>
	<article>
		<?php ct_tribes_featured_image(); ?>
		<div class="post-container">
			<div class='post-header'>
				<h1 class='post-title'><?php the_title(); ?></h1>
				<?php get_template_part( 'content/post-byline' ); ?>
			</div>
			<div class="post-content">
				<?php ct_tribes_output_last_updated_date(); ?>
				<?php 
				global $current_user;
				get_currentuserinfo();
				if (is_user_logged_in() && $current_user->ID == $post->post_author)  {
			        echo do_shortcode('[acf_frontend form="form_6277e61aa24fe"]'); 
			    }
				?>
				<?php the_content(); ?>
				<?php wp_link_pages( array(
					'before' => '<p class="singular-pagination">' . esc_html__( 'Pages:', 'tribes' ),
					'after'  => '</p>',
				) ); ?>
				<?php do_action( 'ct_tribes_post_after' ); ?>
			</div>
			<div class="post-meta">
				<?php get_template_part( 'content/post-categories' ); ?>
				<?php get_template_part( 'content/post-tags' ); ?>
				<?php get_template_part( 'content/post-nav' ); ?>
			</div>
		</div>
	</article>
	<?php comments_template(); ?>
</div>