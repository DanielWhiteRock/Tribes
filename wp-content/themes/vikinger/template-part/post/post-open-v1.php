<?php
/**
 * Vikinger Template Part - Post Open V1
 * 
 * @package Vikinger
 * @since 1.0.0
 * @author Odin Design Themes (https://odindesignthemes.com/)
 * 
 * @see vikinger_post_get_loop_data
 * 
 * @param array $args {
 *   @type array $post Post data.
 * }
 */

  $blog_sidebar_is_active = is_active_sidebar('blog_bottom');
  $post_classes = ['post-open'];

  if (!$args['post']['cover_url']) {
    $post_classes[] = 'no-cover';
  }

  global $vikinger_settings;

  $display_verified = vikinger_plugin_bpverifiedmember_is_active() && $vikinger_settings['bp_verified_member_display_badge_in_wp_posts'];

  $display_verified_in_fullname = $display_verified && $vikinger_settings['bp_verified_member_display_badge_in_profile_fullname'];
  $display_verified_in_username = $display_verified && $vikinger_settings['bp_verified_member_display_badge_in_profile_username'];

  $verified_user = $args['post']['author']['verified'];

?>

<!-- CONTENT GRID -->
<div class="content-grid full">
  <!-- POST OPEN -->
  <article <?php post_class($post_classes); ?>>
  <?php if ($args['post']['cover_url']) : ?>
    <!-- POST OPEN COVER -->
    <div class="post-open-cover" style="<?php echo esc_attr('background: url(' . esc_url($args['post']['cover_url']) . ') center center / cover no-repeat'); ?>"></div>
    <!-- /POST OPEN COVER -->
  <?php endif; ?>

    <!-- POST OPEN BODY -->
    <div class="post-open-body">
      <!-- POST OPEN HEADING -->
      <div class="post-open-heading">
        <!-- POST OPEN TIMESTAMP -->
        <p class="post-open-timestamp">
        <?php foreach ($args['post']['categories'] as $category): ?>
          <a href="<?php echo esc_url(get_category_link($category->cat_ID)); ?>"><?php echo esc_html($category->name); ?></a> -
        <?php endforeach; ?>

        <?php echo esc_html($args['post']['date']); ?>
        </p>
        <!-- /POST OPEN TIMESTAMP -->

        <!-- POST OPEN TITLE -->
        <h2 class="post-open-title"><?php echo wp_kses($args['post']['title'], vikinger_wp_kses_post_title_get_allowed_tags()); ?></h2>
        <!-- /POST OPEN TITLE -->
      </div>

      <div class="tribe-comments tribe-post-action-box">
       
				<?php 
				global $current_user;
				get_currentuserinfo();
				if (is_user_logged_in() && $current_user->ID == $post->post_author)  {
					$post_link = get_edit_post_link( $post_id );
			        echo '<a href="'.$post_link.'" class="menu-item-link text-tooltip-tfr "  data-title="Edit">
              <svg class="icon-settings menu-item-link-icon">
              <use href="#svg-settings"></use>
            </svg>
            </a>'; 
			    }
				?>
			</div>

      <div class="tribe-post-action-box">
        <a class="menu-item-link text-tooltip-tfr"  data-title="Share" style="position: relative;">
          <svg class="icon-share dashicons-share menu-item-link-icon">
            <use href="#svg-share"></use>
          </svg>
          <div class="share-url tribe-share" style="display:none">
            <input type="text" value="<?php echo esc_url( get_permalink() ); ?>?ref=<?php $current_user = wp_get_current_user(); echo $current_user->user_login ?>">
				  </div>
        </a>
      </div>  
    <!-- ICON SVG -->

			


      <style>
          .share-url{
            float: left;
            position: fixed;
            min-width: 300px;
            right: 3%;
          }
          .tribe-post-action-box{
            display: inline-block;
            float: right;
            position: relative;
            bottom: 50px;
            margin: 20px;
          }
        </style>
				<script>

          jQuery(document).ready(function(){
              jQuery(".dashicons-share").click(function() {
                  jQuery(this).parent().parent().find(".tribe-share").toggle();
              })
          });
        </script>
      <!-- POST OPEN CONTENT -->
      <div class="post-open-content">
        <!-- POST OPEN CONTENT SIDEBAR -->
        <div class="post-open-content-sidebar">
        <?php
      
          /**
           * Author Preview
           */
          get_template_part('template-part/author/author-preview', null, [
            'user'                          => $args['post']['author'],
            'linked'                        => vikinger_plugin_buddypress_is_active(),
            'display_verified_in_fullname'  => $display_verified_in_fullname && $verified_user,
            'display_verified_in_username'  => $display_verified_in_username && $verified_user
          ]);

        ?>
        </div>
        <!-- /POST OPEN CONTENT SIDEBAR -->

        <!-- POST OPEN CONTENT BODY -->
        <div class="post-open-content-body">
        <?php if (isset($args['post']['excerpt'])): ?>
          <!-- POST OPEN EXCERPT -->
          <p class="post-open-excerpt"><?php echo wp_kses($args['post']['excerpt'], vikinger_wp_kses_post_excerpt_get_allowed_tags()); ?></p>
          <!-- /POST OPEN EXCERPT -->
        <?php endif; ?>

        <?php
        
          wp_reset_postdata();

          the_content();

          wp_link_pages();

        ?>

        <?php
          if (isset($args['post']['tags'])) {
            /**
             * Tag List
             */
            get_template_part('template-part/tag/tag', 'list', [
              'tags'  => $args['post']['tags']
            ]);
          }
        ?>
        </div>
        <!-- /POST OPEN CONTENT BODY -->
      </div>
      <!-- /POST OPEN CONTENT -->
      
    <?php

      $current_post_is_restricted = false;
      $current_post_category_is_restricted = false;

      // display comments list only if not membership restricted
      if (vikinger_plugin_pmpro_is_active()) {
        $restricted_categories = vikinger_pmpro_logged_user_restricted_categories_get();
        $restricted_posts = vikinger_pmpro_logged_user_restricted_posts_get();

        foreach ($args['post']['categories'] as $post_category) {
          if (in_array($post_category->cat_ID, $restricted_categories)) {
            $current_post_category_is_restricted = true;
            break;
          }
        }

        $current_post_is_restricted = in_array($args['post']['id'], $restricted_posts);
      }

    ?>

    <?php if (!$current_post_is_restricted && !$current_post_category_is_restricted) : ?>
      <!-- COMMENT LIST -->
      <div id="comment-list" data-postid="<?php echo esc_attr($args['post']['id']); ?>"></div>
1rfghjkl;

      <!-- /COMMENT LIST -->
    <?php endif; ?>
    </div>
    <!-- /POST OPEN BODY -->
  </article>
  <!-- /POST OPEN -->
</div>

<!-- /CONTENT GRID -->
<?php if ($blog_sidebar_is_active) : ?>
  <!-- CONTENT GRID -->
  <div class="content-grid grid medium">
  <?php get_sidebar('blog_bottom'); ?>
  </div>
  <!-- /CONTENT GRID -->
<?php endif; ?>