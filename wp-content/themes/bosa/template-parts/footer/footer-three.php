<div class="bottom-footer">
	<div class="container">
		<div class="row align-items-center">
			<?php 
				$socialEmptyClass = '';
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
				if ( !get_theme_mod( 'disable_footer_social_links', false ) && $has_social_icon ){
					$socialEmptyClass = 'col-lg-8';
				}else{
					$socialEmptyClass = 'col-lg-12 text-center';
				}
			?>
			<?php 
			    if( !get_theme_mod( 'disable_footer_social_links', false ) && $has_social_icon ){ ?>
			    	<div class="col-lg-4">
				    	<div class="social-profile"> <!-- social links area -->
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
						            echo '<li><a href="' . esc_url( $value['link'] ) . '" target="' . esc_attr( $link_target ). '"><i class=" ' . esc_attr( $value['icon'] ) . '"></i></a></li>';
						            $count = $count + 0.2;
						        }
					        }
					        echo '</ul>'; ?>
				        </div> <!-- social links area -->
			    	</div>
			<?php } ?> 
			<div class="<?php echo esc_attr( $socialEmptyClass ) ?>">
				<div class="footer-desc-wrap">
					<?php get_template_part( 'template-parts/site', 'info' ); ?>
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
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>