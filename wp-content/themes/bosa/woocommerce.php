<?php
/**
 * The template for displaying archived woocommerce products
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @package Bosa
 */
get_header(); 
?>
<div id="content" class="site-content">
	<div class="container">
		<section class="wrap-detail-page ">
				<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
				<?php
					if( get_theme_mod( 'breadcrumbs_controls', 'show_in_all_page_post' ) == 'disable_in_all_pages' || get_theme_mod( 'breadcrumbs_controls', 'show_in_all_page_post' ) == 'show_in_all_page_post' ){
						bosa_breadcrumb_wrap();
					}
				?>
				<div class="row">
					<?php
						$sidebarClass = 'col-lg-8';
						$sidebarColumnClass = 'col-lg-4';

						if ( get_theme_mod( 'sidebar_settings', 'right' ) == 'right' ){
							if( !is_active_sidebar( 'woocommerce-right-sidebar') ){
								$sidebarClass = "col-12";
							}	
						}elseif ( get_theme_mod( 'sidebar_settings', 'right' ) == 'left' ){
							if( !is_active_sidebar( 'woocommerce-left-sidebar') ){
								$sidebarClass = "col-12";
							}	
						}elseif ( get_theme_mod( 'sidebar_settings', 'right' ) == 'right-left' ){
							$sidebarClass = 'col-lg-6';
							$sidebarColumnClass = 'col-lg-3';
							if( !is_active_sidebar( 'woocommerce-left-sidebar') && !is_active_sidebar( 'woocommerce-right-sidebar') ){
								$sidebarClass = "col-12";
							}
						}
						if ( get_theme_mod( 'sidebar_settings', 'right' ) == 'no-sidebar' || get_theme_mod( 'disable_sidebar_woocommerce_page', false ) ){
							$sidebarClass = 'col-12';
						}
						if( !get_theme_mod( 'disable_sidebar_woocommerce_page', false ) ){
							if ( get_theme_mod( 'sidebar_settings', 'right' ) == 'left' ){ 
								if( is_active_sidebar( 'woocommerce-left-sidebar') ){ ?>
									<div id="secondary" class="sidebar left-sidebar <?php echo esc_attr( $sidebarColumnClass ); ?>">
										<?php dynamic_sidebar( 'woocommerce-left-sidebar' ); ?>
									</div>
								<?php }
							}elseif ( get_theme_mod( 'sidebar_settings', 'right' ) == 'right-left' ){
								if( is_active_sidebar( 'woocommerce-left-sidebar') || is_active_sidebar( 'woocommerce-right-sidebar') ){ ?>
									<div id="secondary" class="sidebar left-sidebar <?php echo esc_attr( $sidebarColumnClass ); ?>">
										<?php dynamic_sidebar( 'woocommerce-left-sidebar' ); ?>
									</div>
								<?php
								}
							}
						} 
						?>
					
					<div id="primary" class="content-area <?php echo esc_attr( $sidebarClass ); ?>">
						<main id="main" class="site-main post-detail-content woocommerce-products" role="main">
							<?php if ( have_posts() ) :
								woocommerce_content();
							endif;
							?>
						</main><!-- #main -->
					</div><!-- #primary -->
					<?php
						if( !get_theme_mod( 'disable_sidebar_woocommerce_page', false ) ){
							if ( get_theme_mod( 'sidebar_settings', 'right' ) == 'right' ){ 
								if( is_active_sidebar( 'woocommerce-right-sidebar') ){ ?>
									<div id="secondary" class="sidebar right-sidebar <?php echo esc_attr( $sidebarColumnClass ); ?>">
										<?php dynamic_sidebar( 'woocommerce-right-sidebar' ); ?>
									</div>
								<?php }
							}elseif ( get_theme_mod( 'sidebar_settings', 'right' ) == 'right-left' ){
								if( is_active_sidebar( 'woocommerce-left-sidebar') || is_active_sidebar( 'woocommerce-right-sidebar') ){ ?>
									<div id="secondary-sidebar" class="sidebar right-sidebar <?php echo esc_attr( $sidebarColumnClass ); ?>">
										<?php dynamic_sidebar( 'woocommerce-right-sidebar' ); ?>
									</div>
								<?php
								}
							}
						} ?>
				</div>
		</section>
	</div><!-- #container -->
</div><!-- #content -->
<?php
get_footer();
