<header id="masthead" class="site-header header-three">
	<?php
	$social_defaults = array(
		array(
			'icon' 		=> '',
			'link' 		=> '',
			'target' 	=> true,
		)			
	);
	$social_icons = get_theme_mod( 'social_media_links', $social_defaults );
	$has_social_icon = false;
	if ( is_array( $social_icons ) ){
		foreach( $social_icons as $value ){
			if( !empty( $value['icon'] ) ){
				$has_social_icon = true;
				break;
			}
		}
	}
	?>
	<div class="top-header">
		<?php if( !get_theme_mod( 'disable_top_header_section', false ) ){ ?>
			<?php if( ( !get_theme_mod( 'disable_header_social_links', false ) && $has_social_icon ) || has_nav_menu( 'menu-3') ){ ?>
				<div class="top-header-inner">
					<div class="container">
						<div class="row align-items-center">
							<div class="col-lg-7 d-none d-lg-block">
								<?php if( has_nav_menu( 'menu-3') ){ ?>
									<nav id="secondary-navigation" class="header-navigation">
										<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'bosa' ); ?></button>
										<?php
										wp_nav_menu( array(
											'theme_location' => 'menu-3',
											'menu_id'        => 'secondary-menu',
										) );
										?>
									</nav><!-- #site-navigation -->
								<?php } ?>
							</div>
							<div class="col-lg-5 d-none d-lg-block">
								<div class="header-icons">
									<div class="social-profile">
										<?php 
									        if( !get_theme_mod( 'disable_header_social_links', false ) && $has_social_icon ){

									            echo '<ul class="social-group">';
									            $count = 0.2;
									            $link_target = '';
									            foreach( $social_icons as $value ){
									                if ( $value['target'] ){
										        		$link_target = '_blank';
										        	}else{
										        		$link_target = '';
										        	}
										            if( !empty( $value['icon'] ) ){
											            echo '<li><a href="' . esc_url( $value['link'] ) . '" target="' .esc_html( $link_target ). '"><i class=" ' . esc_attr( $value['icon'] ) . '"></i></a></li>';
											            $count = $count + 0.2;
											        }
									            }
									            echo '</ul>';
					        				}
				        				?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
		<?php if( ( !get_theme_mod( 'disable_header_social_links', false ) && $has_social_icon ) || has_nav_menu( 'menu-3') || !get_theme_mod( 'disable_search_icon', false ) || is_active_sidebar( 'menu-sidebar' ) ){ ?>
			<div class="alt-menu-icon d-lg-none">
				<a class="offcanvas-menu-toggler" href="#">
					<span class="icon-bar-wrap">
						<span class="icon-bar"></span>
					</span>
					<span class="iconbar-label d-lg-none"><?php echo esc_html( get_theme_mod( 'top_bar_name', esc_html__( 'Top Bar', 'bosa' ) ) ); ?></span>
				</a>
			</div>
		<?php } ?>
	</div>
	<?php
	$header_slider_defaults = array(
		array(
			'slider_item' 	=> '',
			)			
	);
	$header_image_slider = get_theme_mod( 'header_image_slider', $header_slider_defaults );
	$has_header_image = false;
	if ( is_array( $header_image_slider ) ){
		foreach( $header_image_slider as $value ){
			if( !empty( $value['slider_item'] ) ){
				$has_header_image = true;
				break;
			}
		}
	}
	?> 
	<div class="mid-header header-image-wrap">
		<?php 
			if( $has_header_image ){ ?>
				<div class="header-image-slider">
				    <?php foreach( $header_image_slider as $slider_item ) : ?>
				      <div class="header-slide-item" style="background-image: url( <?php echo esc_url( wp_get_attachment_url( $slider_item['slider_item'] ) ); ?> )"><div class="slider-inner"></div>
				      </div>
				    <?php endforeach; ?>
				</div>
				<?php if( !get_theme_mod( 'disable_header_slider_arrows', false ) ) { ?>
					<ul class="slick-control">
				        <li class="header-slider-prev">
				        	<span></span>
				        </li>
				        <li class="header-slider-next">
				        	<span></span>
				        </li>
				    </ul>
				<?php }
			} ?>
		<div class="container">
			<?php get_template_part( 'template-parts/site', 'branding' ); ?>
		</div>
		<div class="overlay"></div>
	</div>
	<div class="bottom-header fixed-header">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-10 d-none d-lg-block">
					<nav id="site-navigation" class="main-navigation d-none d-lg-flex">
						<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'bosa' ); ?></button>
						<?php if ( has_nav_menu( 'menu-1' ) ) :
							wp_nav_menu( 
								array(
									'container'      => '',
									'theme_location' => 'menu-1',
									'menu_id'        => 'primary-menu',
									'menu_class'     => 'menu nav-menu',
								)
							);
						?>
						<?php else :
							wp_page_menu(
								array(
									'menu_class' => 'menu-wrap',
				                    'before'     => '<ul id="primary-menu" class="menu nav-menu">',
				                    'after'      => '</ul>',
								)
							);
						?>
						<?php endif; ?>
					</nav><!-- #site-navigation -->
				</div>
				<div class="col-lg-2 d-none d-lg-block">
					<div class="header-icons">
						<!-- Search form structure -->
						<?php if( !get_theme_mod( 'disable_search_icon', false ) ): ?>
							<div id="search-form" class="header-search-wrap ">
								<button class="search-icon">
									<span class="fas fa-search"></span>
								</button>
							</div>
						<?php endif; ?>
						<?php if( !get_theme_mod( 'disable_hamburger_menu_icon', false ) && is_active_sidebar( 'menu-sidebar' ) ){ ?>
							<div class="alt-menu-icon">
								<a class="offcanvas-menu-toggler" href="#">
									<span class="icon-bar"></span>
								</a>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>	
		<!-- header search form -->
		<div class="header-search">
			<div class="container">
				<?php get_search_form(); ?>
				<button class="close-button">
					<span class="fas fa-times"></span>
				</button>
			</div>
		</div>
		<!-- header search form end-->
		<div class="mobile-menu-container"></div>
	</div>
	<?php get_template_part( 'template-parts/offcanvas', 'menu' ); ?>
</header><!-- #masthead -->