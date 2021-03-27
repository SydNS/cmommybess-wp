<div class="bottom-footer">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-lg-3">
				<!-- social links area -->
				<?php 
				   	$defaults = array(
							array(
								'icon' 		=> '',
								'link' 		=> '',
								'target' 	=> true,
								)			
					);
					$social_icons = get_theme_mod( 'social_media_links', $defaults );
					$has_social_icon = false;
					if ( is_array( $social_icons ) ){
						foreach( $social_icons as $value ){
							if( !empty( $value['icon'] ) ){
								$has_social_icon = true;
								break;
							}
						}
					}
				    if( !get_theme_mod( 'disable_footer_social_links', false ) && $has_social_icon ){ ?>
				    	<div class="social-profile">
					        <?php echo '<ul class="social-group">';
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
					        echo '</ul>'; ?>
				        </div>
				<?php } ?> <!-- social links area -->
			</div>
			<div class="col-lg-6">
				<?php if ( has_nav_menu( 'menu-2' ) && !get_theme_mod( 'disable_footer_menu', false )){ ?>
					<div class="footer-menu"><!-- Footer Menu-->
						<?php
						wp_nav_menu( array(
							'theme_location' => 'menu-2',
							'menu_id'        => 'footer-menu',
							'depth'          => 1,
						) );
						?>
					</div><!-- footer Menu-->
				<?php }
				get_template_part( 'template-parts/site', 'info' ); ?>
			</div>
			<div class="col-lg-3">
				<?php bosa_footer_image(); ?>
			</div>
		</div>
	</div>
</div>