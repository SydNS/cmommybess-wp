<?php global $wp_query;
 ?>
<?php $skip_min_height = false; ?><section class="u-clearfix u-section-1" id="sec-1da2">
  <div class="u-clearfix u-sheet u-valign-middle u-sheet-1"><!--blog--><!--blog_options_json--><!--{"type":"Recent","source":"","tags":"","count":""}--><!--/blog_options_json-->
    <div class="u-blog u-expanded-width u-repeater u-repeater-1"><!--blog_post-->
      <?php while (have_posts()) : the_post(); ?><div class="u-blog-post u-container-style u-repeater-item u-white u-repeater-item-1">
        <div class="u-container-layout u-similar-container u-valign-top u-container-layout-1"><!--blog_post_header-->
          <h4 class="u-blog-control u-text u-text-1">
            <?php if (!is_singular()): ?><a class="u-post-header-link" href="<?php the_permalink(); ?>"><?php endif; ?><?php the_title(); ?><?php if (!is_singular()): ?></a><?php endif; ?>
          </h4><!--/blog_post_header--><!--blog_post_image-->
          <?php
                            $post_image = theme_get_post_image(array('class' => 'u-blog-control u-expanded-width u-image u-image-default u-image-1', 'default' => '/images/1.jpeg'));
                            if ($post_image) echo $post_image; else { echo '<div class="hidden-image"></div>'; $skip_min_height = true; } ?><!--/blog_post_image--><!--blog_post_content-->
          <div class="u-blog-control u-post-content u-text u-text-2"><?php echo is_singular() ? theme_get_content() : theme_get_excerpt(); ?></div><!--/blog_post_content--><!--blog_post_readmore-->
          <a href="<?php the_permalink(); ?>" class="u-blog-control u-border-2 u-border-palette-1-base u-btn u-btn-rectangle u-button-style u-none u-btn-1"><?php _e(sprintf(__('Read more', 'craftymommybeeswp'))); ?></a><!--/blog_post_readmore-->
        </div>
      </div><?php endwhile; ?><!--/blog_post--><!--blog_post-->
      <!--/blog_post--><!--blog_post-->
      <!--/blog_post-->
    </div><!--/blog-->
  </div>
</section><?php if ($skip_min_height) { echo "<style> .u-section-1, .u-section-1 .u-sheet {min-height: auto;}</style>"; } ?>