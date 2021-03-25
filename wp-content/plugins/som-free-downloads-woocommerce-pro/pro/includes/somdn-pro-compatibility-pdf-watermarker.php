<?php
/**
 * Free Downloads - Modified version of WC_PDF_Watermarker class from WooCommerce PDF Watermark
 * 
 * @version 1.1.1
 * @author  Square One Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;

	class SOMDN_WC_PDF_Watermarker {

		private $pdf;

		/**
		 * Constructor
		 * @return void
		 */
		public function __construct() {
			// Include the PDF libs
			$this->includes();

			$this->pdf = new FPDI_Protection( 'P', 'pt' );

		} // __construct()

		/**
		 * Include PDF libraries
		 * @return void
		 */
		public function includes() {
			$pdf_watermarker_directory = ABSPATH . '/wp-content/plugins/woocommerce-pdf-watermark/includes/';
			// Include FPDF
			if ( ! class_exists( 'FPDF' ) ) {
				require_once $pdf_watermarker_directory . 'lib/fpdf/fpdf.php';
			}
			// Include FPDI
			if ( ! class_exists( 'FPDI' ) ) {
				require_once $pdf_watermarker_directory . 'lib/fpdi/fpdi.php';
			}
			// Include FPDI Protection
			if ( ! class_exists( 'FPDI_Protection' ) ) {
				require_once $pdf_watermarker_directory . 'lib/fpdi/fpdi_protection.php';
			}
		} // End includes()

		/**
		 * Test file for open-ability
		 * @return int number of pages in document, false otherwise
		 */
		public function tryOpen( $file ) {

			try {
				$pagecount = $this->pdf->setSourceFile( $file );
			} catch ( Exception $e ) {
				return false;
			}

			return $pagecount;
		}

		/**
		 * Apply the watermark to the file
		 * @return void
		 */
		public function watermark( $download_data, $product_id, $file, $new_file, $preview = false ) {

			// Legacy fallback
			$order_id = 0;

			// Set up the PDF file
			$pagecount = $this->tryOpen( $file );
			if ( false === $pagecount ) {
				wp_die( __( 'Unable to serve the file at this time. The file does not support watermarking.', 'woocommerce-pdf-watermark' ) );
			}

			$this->pdf->SetAutoPageBreak( 0 );
			$this->pdf->SetMargins( 0, 0 );

			// Get WC PDF Watermark Settings
			$type              = get_option( 'woocommerce_pdf_watermark_type' );
			$x_pos             = get_option( 'woocommerce_pdf_watermark_font_horizontal' );
			$y_pos             = get_option( 'woocommerce_pdf_watermark_font_vertical' );
			$opacity           = get_option( 'woocommerce_pdf_watermark_opacity', '1' );
			$override          = get_post_meta( $product_id, '_pdf_watermark_override', true );
			$horizontal_offset = intval( get_option( 'woocommerce_pdf_watermark_horizontal_offset', 0 ) );
			$vertical_offset   = intval( get_option( 'woocommerce_pdf_watermark_vertical_offset', 0 ) );
			$display_pages     = get_option( 'woocommerce_pdf_watermark_page_display', 'all' );

			// Get custom free download WC PDF Watermark Settings
			$somdn_pro_watermark_settings = get_option( 'somdn_pro_woo_pdf_watermark_settings' );
			$free_download_text = isset( $somdn_pro_watermark_settings['somdn_pro_woo_pdf_watermark_text'] ) ? $somdn_pro_watermark_settings['somdn_pro_woo_pdf_watermark_text'] : '' ;

			// Get logged in user ID (if applicable) and user submitted info (if applicable)
			$current_user_id = get_current_user_id();
			$user_fname = isset( $download_data['somdn_download_user_name'] ) ? sanitize_text_field( $download_data['somdn_download_user_name'] ) : '' ;
			$user_lname = isset( $download_data['somdn_download_user_lname'] ) ? sanitize_text_field( $download_data['somdn_download_user_lname'] ) : '' ;
			$user_email = isset( $download_data['somdn_download_user_email'] ) ? sanitize_text_field( $download_data['somdn_download_user_email'] ) : '' ;

			if ( empty( $user_fname ) ) {

				if ( $current_user_id ) {
					$user = get_user_by( 'ID', $current_user_id );
					$user_fname = $user->first_name;
				 } else {
					$user_fname = '';
				}

			}

			if ( empty( $user_lname ) ) {

				if ( $current_user_id ) {
					$user = get_user_by( 'ID', $current_user_id );
					$user_lname = $user->last_name;
				 } else {
					$user_lname = '';
				}

			}

			if ( empty( $user_email ) ) {

				if ( $current_user_id ) {
					$user = get_user_by( 'ID', $current_user_id );
					$user_email = $user->user_email;
				 } else {
					$user_email = '';
				}

			}

			$download_user_data = array(
				'first_name' => $user_fname,
				'last_name' => $user_lname,
				'email' => $user_email
			);

			if ( 'yes' == $override ) {
				$type = get_post_meta( $product_id, '_pdf_watermark_type', true );
			}

			if ( $type && 'text' == $type ) {

				// Get settings
				$text        = get_option( 'woocommerce_pdf_watermark_text' );
				if ( 'yes' == $override ) {
					$text = get_post_meta( $product_id, '_pdf_watermark_text', true );
				}

				if ( ! empty( $free_download_text ) ) {
					$text = $free_download_text;
				}

				$color       = $this->hex2rgb( get_option( 'woocommerce_pdf_watermark_font_colour', '#000' ) );
				$font        = get_option( 'woocommerce_pdf_watermark_font', 'times' );
				$size        = get_option( 'woocommerce_pdf_watermark_font_size', '8' );
				$line_height = is_numeric( $size ) ? $size : 8;
				$bold        = get_option( 'woocommerce_pdf_watermark_font_style_bold' );
				$italics     = get_option( 'woocommerce_pdf_watermark_font_style_italics' );
				$underline   = get_option( 'woocommerce_pdf_watermark_font_style_underline' );

				// Build style var
				$style = '';
				if ( $bold && 'yes' == $bold ) {
					$style .= 'B';
				}
				if ( $italics && 'yes' == $italics ) {
					$style .= 'I';
				}
				if ( $underline && 'yes' == $underline ) {
					$style .= 'U';
				}

				// Assign font
				$this->pdf->SetFont( $font, $style, $size );

				$text = $this->parse_template_tags( $download_user_data, $product_id, $text );
				if ( function_exists( 'iconv' ) ) {
					$text = iconv( 'UTF-8', 'ISO-8859-1//TRANSLIT', $text );
				} else {
					$text = html_entity_decode( utf8_decode( $text ) );
				}

				// Get number of lines of text, can use a new line to go to a new line
				$lines = 1;
				if ( stripos( $text, "\n" ) !== FALSE ) {
					$lines = explode( "\n", $text );
					$text = explode( "\n", $text );
					$lines = count( $lines );
					$longest_text = 0;
					foreach ( $text as $line ) {
						if ( $this->pdf->GetStringWidth( $line ) > $this->pdf->GetStringWidth( $longest_text ) ) {
							$longest_text = $this->pdf->GetStringWidth( $line );
						}
					}
				} else {
					$longest_text = $this->pdf->GetStringWidth( $text );
				}

				// Loop through pages to add the watermark
				for ( $i = 1; $i <= $pagecount; $i++ ) {
					$tplidx = $this->pdf->importPage( $i );
					$specs = $this->pdf->getTemplateSize( $tplidx );
					$orientation = ( $specs['h'] > $specs['w'] ? 'P' : 'L' );

					$this->pdf->addPage( $orientation, array( $specs['w'], $specs['h'] ) );
					$this->pdf->useTemplate( $tplidx );

					// Check if we must skip this page based on the display on page setting
					if ( 'first' == $display_pages && 1 !== $i ) {
						continue;
					} elseif ( 'last' == $display_pages && $i !== $pagecount ) {
						continue;
					} elseif ( 'alternate' == $display_pages ) {
						if ( ( $i % 2 ) == 0 ) {
							continue;
						}
					}

					// Horizontal Alignment for Cell function
					$x = 0;
					if ( 'right' == $x_pos ) {
						$x = $specs['w'];
					} elseif( 'center' == $x_pos ) {
						$x = ( $specs['w'] / 2 );
					} elseif( 'left' == $x_pos ) {
						$x = 0;
					}

					// Vertical Alignment for setY function
					$y = 0;
					if ( 'bottom' == $y_pos ) {
						$y = $specs['h'] - ( ( $line_height * $lines ) + 7 );
					} elseif( 'middle' == $y_pos ) {
						$y = ( $specs['h'] / 2 ) - ( $line_height / 2 );
					} elseif( 'top' == $y_pos ) {
						$y = $line_height / 2;
					}

					// Vertical offset
					$y += $vertical_offset;

					$this->pdf->setY( $y );
					$this->pdf->setAlpha( $opacity );
					$this->pdf->SetTextColor( $color[0], $color[1], $color[2] );

					// Put the text watermark down with Cell
					if ( is_array( $text ) ) {
						foreach ( $text as $line ) {
							if ( 'right' == $x_pos ) {
								$_x = $x - ( $this->pdf->GetStringWidth( $line ) + 7 );
							} elseif( 'center' == $x_pos ) {
								$_x = $x - ( $this->pdf->GetStringWidth( $line ) / 2 );
							} else {
								$_x = $x;
							}

							// Horizontal Offset
							$_x += $horizontal_offset;

							$this->pdf->SetXY( $_x, $y );
							$this->pdf->Write( $line_height, $line );
							$y += $line_height;
							//$this->pdf->Cell( 0, 0, $line, 0, 0, $x );
							//$this->pdf->Ln( $line_height );
						}
					} else {
						if ( 'right' == $x_pos ) {
							$_x = $x - ( $this->pdf->GetStringWidth( $text ) + 7 );
						} elseif( 'center' == $x_pos ) {
							$_x = $x - ( $this->pdf->GetStringWidth( $text ) / 2 );
						} else {
							$_x = $x;
						}

						// Horizontal Offset
						$_x += $horizontal_offset;

						$this->pdf->SetXY( $_x, $y );
						$this->pdf->Write( $line_height, $text );
						//$this->pdf->Cell( 0, 0, $text, 0, 0, $x );
					}
					$this->pdf->setAlpha( 1 );

				} // End forloop

			} elseif ( $type && 'image' == $type ) {
				$image      = get_option( 'woocommerce_pdf_watermark_image' );
				if ( 'yes' == $override ) {
					$image = get_post_meta( $product_id, '_pdf_watermark_image', true );
				}
				$image      = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $image );
				$image_info = getimagesize( $image );
				$width      = $image_info[0];
				$height     = $image_info[1];

				for ( $i = 1; $i <= $pagecount; $i++ ) {
					$tplidx = $this->pdf->importPage( $i );
					$specs = $this->pdf->getTemplateSize( $tplidx );
					$orientation = ( $specs['h'] > $specs['w'] ? 'P' : 'L' );

					$this->pdf->addPage( $orientation, array( $specs['w'], $specs['h'] ) );
					$this->pdf->useTemplate( $tplidx );

					// Check if we must skip this page based on the display on page setting
					if ( 'first' == $display_pages && 1 !== $i ) {
						continue;
					} elseif ( 'last' == $display_pages && $i !== $pagecount ) {
						continue;
					} elseif ( 'alternate' == $display_pages ) {
						if ( ( $i % 2 ) == 0 ) {
							continue;
						}
					}

					// Horizontal alignment
					$x = 0;
					if ( 'right' == $x_pos ) {
						$x = $specs['w'] - ( $width * 20 / 72 );
					} elseif ( 'center' == $x_pos ) {
						$x = ( $specs['w'] / 2 ) - ( $width * 20 / 72 );
					} elseif ( 'left' == $x_pos ) {
						$x = '0';
					}
					// Horizontal Offset
					$x += $horizontal_offset;

					// Vertical alignment
					$y = 0;
					if ( 'bottom' == $y_pos ) {
						$y = $specs['h'] - ( $height );
					} elseif ( 'middle' == $y_pos ) {
						$y = ( $specs['h'] / 2 ) - ( $height );
					} elseif ( 'top' == $y_pos ) {
						$y = '0';
					}
					// Vertical offset
					$y += $vertical_offset;

					$this->pdf->SetAlpha( $opacity );
					$this->pdf->Image( $image, $x, $y );
					$this->pdf->SetAlpha( 1 );
				} // End forloop
			} // End else for image type

			// Apply protection settings
			$password_protect = get_option( 'woocommerce_pdf_watermark_password_protects', 'no' );
			$do_not_allow_copy = get_option( 'woocommerce_pdf_watermark_copy_protection', 'no' );
			$do_not_allow_print = get_option( 'woocommerce_pdf_watermark_print_protection', 'no' );
			$do_not_allow_modify = get_option( 'woocommerce_pdf_watermark_modification_protection', 'no' );
			$do_not_allow_annotate = get_option( 'woocommerce_pdf_watermark_annotate_protection', 'no' );
			$protection_array = array();

			if ( 'no' == $do_not_allow_copy ) {
				$protection_array[] = 'copy';
			}
			if ( 'no' == $do_not_allow_print ) {
				$protection_array[] = 'print';
			}
			if ( 'no' == $do_not_allow_modify ) {
				$protection_array[] = 'modify';
			}
			if ( 'no' == $do_not_allow_annotate ) {
				$protection_array[] = 'annot-forms';
			}
			$user_pass = '';
			if ( 'yes' == $password_protect ) {
				$user_pass = $user_email;
			}

			$this->pdf->SetProtection( $protection_array, $user_pass, 0 );

			if ( $preview ) {
				$this->pdf->Output();
			} else {
				$this->pdf->Output( $new_file, 'F' );
			}
		}

		/**
		 * Convert HEX color code to RGB
		 * @param  string $color HEX color code
		 * @return array
		 */
		public function hex2rgb( $color ) {
			if ( $color[0] == '#')
				$color = substr( $color, 1 );

			if ( strlen( $color ) == 6 ) {
				list( $r, $g, $b ) = array(
					$color[0] . $color[1],
					$color[2] . $color[3],
					$color[4] . $color[5]
				);
			} elseif ( strlen( $color ) == 3 ) {
				list( $r, $g, $b ) = array(
					$color[0] . $color[0],
					$color[1] . $color[1],
					$color[2] . $color[2]
				);
			} else {
				return false;
			}

			$r = hexdec( $r );
			$g = hexdec( $g );
			$b = hexdec( $b );

			return array( $r, $g, $b );
		} // End hex2rgb()

		/**
		 * Parse text for template tags and populate it
		 * @param  array  $download_user_data
		 * @param  string $text
		 * @return string
		 */
		public function parse_template_tags( $download_user_data, $product_id, $text ) {

			if ( false === strpos( $text, '{' ) ) {
				return $text;
			}

			if ( is_null( $product_id ) ) {
				return $text;
			}

			$first_name = $download_user_data['first_name'];
			$last_name = $download_user_data['last_name'];
			$email = $download_user_data['email'];
			$site_name = get_bloginfo( 'name' );
			$site_url = home_url();

			$unparsed_text = $parsed_text = $text;

			$parsed_text = str_replace( '{first_name}', $first_name, $parsed_text );
			$parsed_text = str_replace( '{last_name}', $last_name, $parsed_text );
			$parsed_text = str_replace( '{email}', $email, $parsed_text );
			$parsed_text = str_replace( '{site_name}', $site_name, $parsed_text );
			$parsed_text = str_replace( '{site_url}', $site_url, $parsed_text );

			return $parsed_text;
		} // End parse_template_tags()
	}