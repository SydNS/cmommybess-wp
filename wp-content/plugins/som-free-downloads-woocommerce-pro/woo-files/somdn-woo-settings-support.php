<?php
/**
 * Free Downloads - Support Settings
 * 
 * Support guide, FAQ, etc.
 * 
 * @version	3.1.8
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'somdn_get_settings_sub_tabs', 'somdn_get_settings_sub_tabs_support_woo_basic', 10, 1 );
function somdn_get_settings_sub_tabs_support_woo_basic( $extra_tabs ) {

	if ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'support' ) {
			
		$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'guide';

		ob_start(); ?>

		<ul class="subsubsub">
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=guide" class="<?php echo $active_section == 'guide' ? 'current' : ''; ?>">Guide</a> | </li>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=features" class="<?php echo $active_section == 'features' ? 'current' : ''; ?>">Features</a> | </li>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=shortcodes" class="<?php echo $active_section == 'shortcodes' ? 'current' : ''; ?>">Shortcodes</a> | </li>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=settings" class="<?php echo $active_section == 'settings' ? 'current' : ''; ?>">Settings Explained</a> | </li>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=faq" class="<?php echo $active_section == 'faq' ? 'current' : ''; ?>">FAQs</a> | </li>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=error_logs" class="<?php echo $active_section == 'error_logs' ? 'current' : ''; ?>">Error Logs & Debug</a> | </li>
			<li>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=more" class="<?php echo $active_section == 'more' ? 'current' : ''; ?>">More</a></li>
		</ul>

		<?php

		$extra_tabs = ob_get_clean();

		return $extra_tabs;

	}

}

add_action( 'somdn_settings_after_settings', 'somdn_settings_support_tab_woo_basic', 10, 1 );
function somdn_settings_support_tab_woo_basic( $active_tab ) {

	if ( $active_tab == 'support' ) {

		$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'guide';

		if ( 'guide' == $active_section ) {

			somdn_support_guide();

		} elseif ( 'features' == $active_section ) {

			somdn_support_features();

		} elseif ( 'shortcodes' == $active_section ) {

			somdn_support_shortcodes();
			
		} elseif ( 'settings' == $active_section ) {

			somdn_support_settings();
			
		} elseif ( 'faq' == $active_section ) {

			somdn_support_faq();

		} elseif ( 'error_logs' == $active_section ) {

			somdn_support_error_logs();

		} elseif ( 'more' == $active_section ) {

			somdn_support_more();
			
		}

	}

}

function somdn_support_guide() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7 som-settings-guide">

				<h2>Quick Guide</h2>

				<p>Once enabled any free downloadable products in your store will be downloadable by visitors without needing to go through the checkout process. "Add to Cart" buttons will be replaced with "Download Now" buttons, or for products with multiple files attached, a list of files or a single button will be displayed, depending on which settings you choose. On pages that list multiple products such as your store home, or in "Related Products" sections for example, the button will say "Read More", taking the user to the product page. This can be changed in the plugin settings.</p>

				<p><strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> includes implicit support for the official <strong>Memberships</strong> and <strong>Subscriptions</strong> plugins from Woo.</p>

				<p>The download button and links should be styled by your current theme automatically, so won't look out of place with the rest of your content.</p>

				<p>There are several ways you can customise your experience with this plugin. To get started go to the <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings">settings tab</a>, or read through the <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=features">features</a> section or <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=settings">settings explained</a> page. Be sure to check out the <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=faq">FAQ</a> for more info.</p>

			</div>

		</div>
	</div>

<?php }

function somdn_support_features() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12 som-settings-guide som-settings-guide-features">
					
				<h2>Main Features</h2>
				<p>There are tonnes of customisations available on the <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings">settings</a> tab.</p>
				<ul>
					<li>
						<h3>Checkout-free Downloading</h3>
						<p>If a product is free and downloadable, this plugin will remove the <em>add to cart</em> functionality for that product and allow your visitors to download it straight away without going through the checkout. By default this only includes items that are priced at 0, but you can also customise it to include items that are free because they're on sale.</p>
						<p>You can also offer this functionality only to logged in users, but no message will display informing the user to log in.</p>
						<p>Products with single or multiple files are supported, with several ways you can customise how they're displayed, but currently products with variations are not supported.</p>
						<p>If you want to include only specific products rather than affecting all that meet the requirements, on the <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings">settings</a> page you can choose <em>Include selected products only</em>. With this setting turned on, only products you choose will be available without checkout. Go to the product page and in the <strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> box on the right, tick the box to include that product.</p>
						<?php $somdn_image_01 = plugins_url( '/assets/images/indy-product.png', dirname(__FILE__) ); ?>
						<div class="som-settings-guide-img">
							<img src="<?php echo $somdn_image_01; ?>">
						</div>
					</li>
					<li>
						<h3>WooCommerce Memberships</h3>
						<p><strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> was built with WooCommerce memberships in mind. There are various options to choose from if you have the official Memberships plugin installed. By default if a product is free but requires the user have a specific membership plan, that product will only be available to download for free by those users.</p>
						<p>You are also able to exclude membership products entirely, or only allow free download of membership only products.</p>
					</li>
					<li>
						<h3>Quick View</h3>
						<p>A quick view type feature built to support <strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong>, allowing your users to preview your products from the shop pages without going to the product page. Supports all products, not just free digital downloads.</p>
						<?php $img_location = plugins_url( '/images/', SOMDN_WOO_FILE ); ?>
						<div class="som-settings-guide-img-group">
							<div class="som-settings-guide-img half-col">
								<h4>Shop Listing (mouse hovered)</h4>
								<a href="<?php echo $img_location . 'quick-view-shop.png'; ?>" target="_blank"><img src="<?php echo $img_location . 'quick-view-shop.png' ?>"></a>
							</div>
							<div class="som-settings-guide-img half-col">
								<h4>Quick View Popup</h4>
								<a href="<?php echo $img_location . 'quick-view-shop-product.png' ?>" target="_blank"><img style="width: 360px;" src="<?php echo $img_location . 'quick-view-shop-product.png' ?>"></a>
							</div>
						</div>
					</li>
					<li>
						<h3>Download Logging</h3>
						<p>As a consequence of bypassing the checkout, no orders are created. As such you cannot report on how many downloads you've had through the usual WooCommerce ways. Because of this <strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> keeps a count of how many times a product has been downloaded (only shown in the admin area).</p>
						<p>This can be viewed on the products list page in the columns, and on the indiviual product page itself in the sidebar area. See below screenshots. The download count can also be reset on a per-product basis.</p>
						<?php do_action( 'somdn_support_after_logging' ); ?>

						<div class="som-settings-guide-img-group">
							<?php $somdn_count_01 = plugins_url( '/images/count-list.png', SOMDN_WOO_FILE ); ?>
							<div class="som-settings-guide-img half-col">
								<a href="<?php echo $somdn_count_01 ; ?>" target="_blank"><img src="<?php echo $somdn_count_01 ; ?>"></a>
							</div>
							
							<?php $somdn_count_02 = plugins_url( '/images/count-product.png', SOMDN_WOO_FILE ); ?>
							<div class="som-settings-guide-img half-col">
								<a href="<?php echo $somdn_count_02 ; ?>" target="_blank"><img src="<?php echo $somdn_count_02 ; ?>"></a>
							</div>
						</div>

					</li>
					<li>
						<h3>PDF Viewer</h3>
						<p>Instead of being downloaded, any PDF file will be opened for the visitor to preview, and from there they can download or print the document. This feature, once enabled, will check automatically if the file attached to a product has the <em>.pdf</em> extension. It uses Google Drive online viewer, so visitors can even save the file to their Drive account. For this feature to work, the file needs to be uploaded to your WordPress site, for example using the WooCommerce <strong>Choose File</strong> option.</p>
						<p>As a security feature if the file is uploaded to your website server, it will be temporarily duplicated and that file used for the preview. If the file is on an external server, or isn't duplicated correctly, the normal download process will apply.</p>
						<p>As with the dynamically created ZIP files for multiple file downloads, these temporary PDF files will be deleted every hour or so.</p>

						<?php $somdn_pdf = plugins_url( '/assets/images/pdf-viewer.png', dirname(__FILE__) ); ?>
						<div class="som-settings-guide-img">
							<a href="<?php echo $somdn_pdf ; ?>" target="_blank"><img src="<?php echo $somdn_pdf ; ?>"></a>
						</div>

					</li>
					<?php do_action( 'somdn_support_after_features' ); ?>
				</ul>

			</div>

		</div>
	</div>

<?php

}

function somdn_support_shortcodes() {

	?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-8 som-settings-guide">

				<h2>Shortcodes</h2>

				<p><strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> includes some extra shortcodes to use with free products.</p>

				<p>Free products affected by this plugin should be compatible with all WooCommerce shortcodes, unless specified below.</p>

				<hr>
				<div class="som-settings-settings-spacer-sm"></div>
				
				<h3>Free Download Link</h3>

				<p>Insert a link to instantly download a free product. Supports aligning left/center/right. Default/blank is left. You can also change the button text from whatever the default would be.</p>
				<?php //<p>Note: On the product edit screen the ID will automatically be set to the current product ID.</p> ?>
				<p class="description">This is a block level element so will display on it's own line, not with other text or images in the post.</p>
				
				<p><strong>Args</strong></p>

<div class="som-settings-pre-wrap">

<pre class="som-settings-pre"><code>array(
	'id' => '',
	'align' => 'left',
	'text' => ''
)</code></pre>

</div>

				<p><strong>Example</strong></p>
				<div class="som-settings-pre-wrap">
					<pre class="som-settings-pre"><code>[download_now id="99" text="Get it Free!"]</code></pre>
				</div>

				<hr id="single">
				<div class="som-settings-settings-spacer-sm"></div>

				<h3>Free Download Page</h3>

				<p>Use this shortcode to display the download cart content, as it would appear on a single product page. Useful if you're making a custom product page. You can also change the button text from whatever the default would be.</p>
				<p>Note: If no ID is specified, the shortcode will grab the product ID from the page, if it exists.</p>
				<?php //<p>Note: On the product edit screen the ID will automatically be set to the current product ID.</p> ?>
				<p class="description">This is a block level element so will display on it's own line, not with other text or images in the post.</p>
				
				<p><strong>Args</strong></p>

<div class="som-settings-pre-wrap">

<pre class="som-settings-pre"><code>array(
	'id' => '',
	'text' => ''
)</code></pre>

</div>

				<p><strong>Example</strong></p>
				<div class="som-settings-pre-wrap">
					<pre class="som-settings-pre"><code>[download_now_page id="99" text="Get it Free!"]</code></pre>
				</div>

				<?php do_action( 'somdn_support_after_shortcodes' ); ?>

				<hr>
				<div class="som-settings-settings-spacer-sm"></div>
				
				<h2>Standard WooCommerce Shortcodes</h2>
				
				<ul>
					<li>
				<div class="som-settings-pre-wrap">
					<pre class="som-settings-pre"><code>[add_to_cart id=""]</code></pre>
				</div>
					<p>This WooCommerce shortcode should work with a free download. If the product specified is not valid for free download, the default button will show.</p>
					</li>
				</ul>

				<?php do_action( 'somdn_support_after_woo_shortcodes' ); ?>

			</div>

		</div>
	</div>

<?php

}

function somdn_support_settings() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-10 som-settings-guide">

				<ol>
					<li><a href="#general">General</a></li>
					<?php do_action( 'somdn_support_explained_after_gen_list' ); ?>
					<li><a href="#single">Single Files</a></li>
					<?php do_action( 'somdn_support_explained_after_single_list' ); ?>
					<li><a href="#multiple">Multiple Files</a></li>
					<?php do_action( 'somdn_support_explained_after_multiple_list' ); ?>
					<li><a href="#quickview">Quick View</a> <span class="som-settings-ui-new">Beta</span></li>
					<?php do_action( 'somdn_support_explained_after_quickview_list' ); ?>
					<li><a href="#memberships">Memberships</a></li>
					<?php do_action( 'somdn_support_explained_after_memberships_list' ); ?>
					<li><a href="#docs">PDF Settings</a></li>
					<?php do_action( 'somdn_support_explained_after_docs_list' ); ?>
				</ol>

			</div>

			<div class="som-settings-col-10 som-settings-guide" style="padding-top: 0;">

			<hr id="general">
			<div class="som-settings-settings-spacer-sm"></div>
					
				<h2>General</h2>
				<ul>
					<li>
						<h3>Read More text</h3>
						<p>The text that displays on pages that list products, which link to the product page. This is only applicable to products affected by this plugin.</p>
						<p>If <em>Allow download on shop / archive pages</em> is enabled, it is recommended you leave blank, which will label the button with "Download". This is because the shop pages tend to have small buttons, so won't fit much text. If <em>PDF Viewer</em> is enabled, the button will use the text set in those settings.</p>
					</li>
					<li>
						<h3>Allow download on shop / archive pages</h3>
						<p>If you want to allow users to download files directly from your shop or archive pages, rather than only from the product page.</p>
					</li>
					<li>
						<h3>Only show the button to logged in users</h3>
						<p>If a user is not logged in, no free files will be downloadable. No message is displayed informing the user to log in to download for free. If users are not logged in to your site, the "Add to Cart" buttons will show.</p>
					</li>
					<li>
						<h3>Include paid items that are currently on sale for free</h3>
						<p>If you have a paid product that is on sale for free, it will be included by this plugin. This is not recommended if you use the "Redirect" WooCommerce download method.</p>
					</li>
					<li>
						<h3>Include selected products only</h3>
						<p>Tick this box if you want to pick which products are to be included, rather than all free downloadable products. You can choose if a product should be included on the product page, using the Free Downloads settings box on the right hand side (as below).</p>
						<?php $somdn_image_01 = plugins_url( '/assets/images/indy-product.png', dirname(__FILE__) ); ?>
						<div class="som-settings-guide-img" style="text-align: left; padding-bottom: 5px;">
							<img src="<?php echo $somdn_image_01; ?>">
						</div>
					</li>
					<li>
						<h3>Button classes</h3>
						<p>Apply custom classess to the download button. For example:</p>
						<p><code>my-button-class</code></p>
						<p>Buttons should style automatically to match your current theme with the following classes:</p>
						<p><code>somdn-download-button single_add_to_cart_button button</code></p>
						<p>However, you can add custom classes to add extra theme support.</p>
						<p>To add multiple classes just type them in, separated by a space.</p>
					</li>
					<li>
						<h3>Button CSS</h3>
						<p>Apply custom CSS styles to the download button. For example:</p>
						<p><code>background-color: #333;</code></p>
						<p>Buttons should style automatically to match your current theme with the following classes:</p>
						<p><code>somdn-download-button single_add_to_cart_button button</code></p>
					</li>
					<li>
						<h3>Link classes</h3>
						<p>Apply custom classess to the download link. For example:</p>
						<p><code>my-button-class</code></p>
						<p>Links should style automatically to match your current theme with the following classes:</p>
						<p><code>somdn-download-link</code></p>
						<p>However, you can add custom classes to add extra theme support.</p>
						<p>To add multiple classes just type them in, separated by a space.</p>
					</li>
					<li>
						<h3>Link CSS</h3>
						<p>Apply custom CSS styles to the download link. For example:</p>
						<p><code>font-size: 16px;</code></p>
						<p>Links should style automatically to match your current theme. The link also has the following CSS class:</p>
						<p><code>somdn-download-link</code></p>
					</li>
				</ul>

				<?php do_action( 'somdn_support_explained_after_gen' ); ?>
				
				<hr id="single">
				<div class="som-settings-settings-spacer-sm"></div>
				
				<h2>Single Files</h2>
				<p class="description">Products that only have 1 file attached to them.</p>
				<ul>
					<li>
						<h3>Display method</h3>
						<p>How the download link will be displayed on the product page. Default is a Button. You can also select a Link.</p>
					</li>
					<li>
						<h3>Button text</h3>
						<p>What the text should be on the download button. Default is "Download Now". If a Link is the chosen display method, the download filename will be the text.</p>
					</li>
				</ul>

				<?php do_action( 'somdn_support_explained_after_single' ); ?>

				<hr id="multiple">
				<div class="som-settings-settings-spacer-sm"></div>

				<h2>Multiple Files</h2>
				<p class="description">Products that have more than 1 file attached to them.</p>
				<p class="description"><strong>Note: Any display method with a Button requires the download file be uploaded to this website, for example using the WooCommerce "Choose File" option when adding a downloadable product. External download links will not work. If you use external links for your files, leave the display method as Links Only, which is the default.</strong></p>
				<ul>
					<li>
						<h3>Display method</h3>
						<p>How the download link will be displayed on the product page. Default is <strong>Links Only</strong>.</p>
						<p class="description">The <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings&section=multiple">multiple files</a> settings page shows a preview of each option.</p>
						<ol>
							<li>
								<h3>Links Only (default)</h3>
								<p>For each file available a link will be displayed. The filename entered into the product "Downloadable Files" section will be the link text.</p>
							</li>
							<li>
								<h3>Button Only (download all)</h3>
								<p>A single button will show which will download a dynamically created ZIP file for the user. The ZIP will contain all files for that product.</p>
							</li>
							<li>
								<h3>Button + Checkboxes</h3>
								<p>A list of all available files will show with checkboxes. A single button will display which will download a dynamically created ZIP file for the user. The ZIP will contain all files for that product that the user selected.</p>
							</li>
							<li>
								<h3>Button + Links</h3>
								<p>A list of all available files will show as individual links and the user can download an individual file. A button will also display which will download a dynamically created ZIP file for the user. The ZIP will contain all files for that product.</p>
							</li>
							<li>
								<h3>Button + Filenames</h3>
								<p>A list of all available files will be displayed as text. A button will display which will download a dynamically created ZIP file for the user. The ZIP will contain all files for that product.</p>
							</li>
						</ol>
					</li>
					<li>
						<h3>Button text</h3>
						<p>What the text should be on the download button. Default is "Download All (.ZIP)".</p>
					</li>
					<li>
						<h3>File list text</h3>
						<p>What the text should be above the list of available file links or filenames. Default is "Available Downloads:".</p>
					</li>
					<li>
						<h3>Show Select All box</h3>
						<p>If Button + Checkboxes is the chosen display method, tick this box if you want to include a Select All checkbox.</p>
					</li>
					<li>
						<h3>Show number next to filename</h3>
						<p>If the display method shows a list of links or filenames, tick this box if you want them to be numbered.</p>
					</li>
				</ul>

				<?php do_action( 'somdn_support_explained_after_multiple' ); ?>

				<hr id="quickview">
				<div class="som-settings-settings-spacer-sm"></div>

				<h2>Quick View <span class="som-settings-ui-new">Beta</span></h2>
				<p>Quick View is a feature for allowing your users to preview your products from the shop pages without going to the product page. This feature works for all products, whether free digital ones or otherwise. This was added to include a quick view type feature which fully supports <strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong>.</p>
				<p>When the user hovers over the product in the shop listings a <span class="somdn-qview-button-example">Quick View</span> button appears over the product thumbnail, and when clicked opens up a preview popup of the product showing short description, price, and add to cart or download buttons.</p>
				<?php do_action( 'somdn_support_explained_after_quickview_description' ); ?>
				<br>

				<?php $img_location = plugins_url( '/images/', SOMDN_WOO_FILE ); ?>

				<div class="som-settings-guide-img-group">
					<div class="som-settings-guide-img half-col">
						<a href="<?php echo $img_location . 'quick-view-shop.png'; ?>" target="_blank"><img src="<?php echo $img_location . 'quick-view-shop.png' ?>"></a>
					</div>
					<div class="som-settings-guide-img half-col">
						<a href="<?php echo $img_location . 'quick-view-shop-product.png' ?>" target="_blank"><img style="width: 360px;" src="<?php echo $img_location . 'quick-view-shop-product.png' ?>"></a>
					</div>
				</div>
				<ul>
					<?php do_action( 'somdn_support_explained_before_quickview_button_text' ); ?>
					<li>
						<h3>Button Text</h3>
						<p>Change the text in the button. Default is <strong>Quick View</strong>.</p>
					</li>
					<?php do_action( 'somdn_support_explained_after_quickview_button_text' ); ?>
					<li>
						<h3>Background Colour</h3>
						<p>Change the button background colour. Default is #2679CE.</p>
					</li>
					<?php do_action( 'somdn_support_explained_after_quickview_bg_colour' ); ?>
					<li>
						<h3>Font Colour</h3>
						<p>Change the button font colour. Default is #FFFFFF.</p>
					</li>
					<?php do_action( 'somdn_support_explained_after_quickview_font_colour' ); ?>
				</ul>

				<?php do_action( 'somdn_support_explained_after_quickview' ); ?>

				<hr id="memberships">
				<div class="som-settings-settings-spacer-sm"></div>

				<h2>WooCommerce Memberships</h2>
				<ul>
					<li>
						<h3>Include Membership restricted items</h3>
						<p>Default behaviour. If you have a free product with a membership restriction, and the user has that membership, the free download will be available. If the user does not have that membership, normal purchase restriction will apply.</p>
						<p class="description">For example the message to purchase that membership will display.</p>
					</li>
					<li>
						<h3>Exclude Membership restricted items</h3>
						<p>Regardless of product price, all membership restricted products will be excluded. This is useful if your free membership only products require being purchased using the default cart behaviour.</p>
					</li>
					<li>
						<h3>Members only</h3>
						<p>Only free products that have a membership restriction, with the user having that membership, will be included. This plugin will essentially become a membership only feature.</p>
					</li>
					<li>
						<h3>Include selected memberships only</h3>
						<p>Only users that have specific memberships will be included for free download of any product regardless of membership restrictions on the product itself. When enabled you can include a membership from that membership plan's screen (as below).</p>
						<div class="som-settings-guide-img" style="text-align: left; padding-bottom: 5px;">
							<?php $somdn_image_02 = plugins_url( '/images/membership-plans-allow-free.png', SOMDN_WOO_FILE ); ?>
							<img src="<?php echo $somdn_image_02; ?>">
						</div>
					</li>
					<li>
						<h3>Include paid items that have 100% discounts for members.</h3>
						<p>If a user has a membership that entitles them to a 100% discount for a product, that product will be included.</p>
					</li>

				</ul>

				<?php do_action( 'somdn_support_explained_after_memberships' ); ?>

				<hr id="docs">
				<div class="som-settings-settings-spacer-sm"></div>

				<h2>PDF Viewer</h2>
				
				<p class="description">PDF Viewer will only work for files attached to products that have the .pdf extension.</p>				

				<p>PDF Viewer applies to products that have only a single file attached, or for products with multiple files when your chosen display method includes links. Any other situation and the normal download process will apply.</p>

				<p>Although this feature makes use of the online Google Docs preview service, the file link will be viewable in the URL address bar for the PDF preview. However, in order to protect your files <strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> will create a duplicate of your original PDF file, and use that duplicate for the URL. Like the dynamically created ZIP files for multiple file downloads, these will be deleted from your server every hour.</p>

				<p class="description"><strong>Note: This duplication security feature will only work for files uploaded to this website, not external links. External files will be downloaded the usual way.</strong></p>

				<ul>
					<li>
						<h3>Enable PDF Viewer</h3>
						<p>Enable or disable this feature. Default is disabled.</p>
					</li>
					<li>
						<h3>Single file display</h3>
						<p>How the download link will be displayed on the product page. Default is a Button. You can also select a Link. Only applies to products with a single file.</p>
					</li>
					<li>
						<h3>Link/Button Text</h3>
						<p>What the text should be on the download button/link. Default is "Download Now". Only applies to products with a single file.</p>
					</li>
				</ul>

				<?php do_action( 'somdn_support_explained_after_docs_list' ); ?>

				<?php do_action( 'somdn_support_explained_after_all' ); ?>

			</div>

		</div>
	</div>

<?php

}

function somdn_support_faq() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7 som-settings-guide">
					
				<h2>Frequently Asked Questions</h2>
				<ol>
					<?php do_action( 'somdn_support_before_faq' ); ?>
					<li>
						<h3>How are files downloaded?</h3>
						<p>The short answer is the plugin uses a safe and secure form on the front-end which requests the file. A second round of security checks is performed, and if everything is ok the file is downloaded using the WooCommerce download script; as well as using the download method you set for WooCommerce <strong>(Force Downloads, X-Accel-Redirect/X-Sendfile, or Redirect)</strong>.</p>
					</li>
					<li>
						<h3>How are the dynamically created ZIP files handled?</h3>
						<p>The product files must have been uploaded to your WordPress site, for example using the WooCommerce <strong>Choose File</strong> option, otherwise the ZIP file will be empty. They will not be included if they are external links.</p>
						<p>Once created with either all the files for a product or a selection of the files, it is temporarily saved in a folder on your server. Every hour that folder is emptied. If you deactivate this plugin, that folder and its contents will be removed.</p>
						<p>If you use external file links it is recommended that you use the <strong>Links Only</strong> <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings&section=multiple">display method</a>, if you have products with multiple files.</p>
					</li>
					<li>
						<h3>Are the full links to files visible to a user?</h3>
						<p>It depends on your WooCommerce settings.</p>
						<p>If you use the <strong>Force Downloads</strong> or <strong>X-Accel-Redirect/X-Sendfile</strong> download methods (found in the WooCommerce settings, Products, Downloadable Products) for your store downloading, the file paths and URLs will be hidden. If there are multiple files downloaded as a dynamically created ZIP file, regardless of setting, the URLs will be hidden.</p>
						<p>If you use the <strong>Redirect</strong> download method, the full URL may be visible for single files. For example, a PDF. This is the same as it would be without this plugin.</p>
						<p>If in doubt and you're worried test it yourself on your own site, or please don't hesitate to <a href="https://wordpress.org/support/plugin/download-now-for-woocommerce/" target="_blank">get in touch</a>.</p>
					</li>
					<li>
						<h3>Are WooCommerce Memberships and/or Subscriptions supported?</h3>
						<p>The official Memberships and Subscriptions plugins from Woo are supported. If you have a free product that requires a user have a membership to purchase, that free product will only be available to download if the user is a member.</p>
					</li>
					<li>
						<h3>What other plugins are supported?</h3>
						<p><strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> should be compatible with most plugins. If you have a problem please get in touch and we will include support if possible. Some plugins are only supported in the Pro Edition of Free Downloads WooCommerce.</p>
						<p>Below is a list of explicitly supported plugins:</p>
						<ul class="som-settings-square-ul">
							<li>WooCommerce Memberships</li>
							<li>WooCommerce Subscriptions</li>
							<li>TI WooCommerce Wishlist</li>
							<li>WooCommerce Quickview by IconicWP</li>
							<li>WooCommerce PDF Watermark (Pro Edition)</li>
							<li>Paid Member Subscriptions by Cozmoslabs (Pro Edition)</li>
							<li>Woocommerce Products List by NitroWeb (Pro Edition)</li>
						</ul>
					</li>
					<li>
						<h3>Where should files be hosted for the PDF Viewer to work?</h3>
						<p>It is recommended that the files are uploaded to your WordPress site, for example using the WooCommerce <strong>Choose File</strong> option. External links will only work for products with a single file attached.</p>
					</li>
					<?php do_action( 'somdn_support_after_faq' ); ?>
				</ol>

			</div>

		</div>
	</div>

<?php

}

function somdn_support_error_logs() { ?>

	<?php $log_errors = somdn_logs_export_errors();

	if ( ! empty( $log_errors ) ) { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12 som-settings-guide">

					<div class="som-settings-gen-settings-form-wrap">
						<?php echo $log_errors; ?>
					</div>

			</div>

		</div>
	</div>

	<?php } ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
			<div class="som-settings-col-12 som-settings-guide">

				<div class="som-settings-setting-section-wrapper wrapper-small">

					<h2>Free Dowload Error Logs</h2>
					<p>Notable free download errors and warnings are stored on the server in a text file, and displayed here for convenience.</p>

					<?php

						// Let's grab the free downloads error logs file

						$upload_dir = wp_upload_dir();
						$parent = $upload_dir['basedir'] . '/free-downloads-files';
						$log_filename = $parent . '/free_downloads_log.txt';

						$button_disabled = '';

						if ( file_exists( $log_filename ) ) {
							$logs = file_get_contents( $log_filename );
							// Clean the logs of any HTML before outputting to the page
							$logs = implode( "\n", array_map( 'esc_html', explode( "\n", $logs ) ) );
						} else {
							$logs = 'No errors :)';
							$button_disabled = ' disabled';
						}

					?>

					<div id="somdn-error-logs-textbox" class="somdn-error-logs-textbox"><?php echo $logs; ?></div>
					<textarea id="somdn-error-logs-textbox-default"><?php echo $logs; ?></textarea>

					<div class="somdn-error-logs-textbox-actions">
						<form action="" class="som-settings-settings-form" method="post">
								<?php wp_nonce_field( 'somdn-error-logs-export-nonce', 'somdn-error-logs-export-nonce' ); ?>
								<button class="button" type="submit" name="somdn-error-logs-export" id="somdn-error-logs-export" value="true"<?php echo $button_disabled; ?>>Export<span class="dashicons dashicons-download"></span></button>
								<button class="button" type="button" name="somdn-error-logs-copy" id="somdn-error-logs-copy"<?php echo $button_disabled; ?>><span class="copy-text">Copy</span><span class="copied-text">Copy</span><span class="dashicons"></span></button>
								<button class="button" type="submit" name="somdn-error-logs-delete" id="somdn-error-logs-delete" value="true"<?php echo $button_disabled; ?>>Delete<span class="dashicons dashicons-trash"></span></button>
						</form>
					</div>

				</div>

			</div>
		</div>
	</div>

	<div class="som-settings-container">
		<div class="som-settings-row">
			<div class="som-settings-col-12 som-settings-guide">

				<div class="som-settings-setting-section-wrapper wrapper-small">

					<form action="options.php" class="som-settings-settings-form" method="post">
				
						<div class="som-settings-gen-settings-form-wrap">

							<?php
								settings_fields( 'somdn_debug_settings' );
								do_settings_sections( 'somdn_debug_settings', true, false );
								//wp_nonce_field( 'somdn-debug-logs-enable-nonce', 'somdn-debug-logs-enable-nonce' );
								submit_button();
							?>

						</div>
				
					</form>

				</div>

			</div>
		</div>
	</div>
<?php }

function somdn_logs_export_errors() {

	if ( empty( $_REQUEST ) ) {
		return;
	}

	ob_start();

	$somdn_logs_export_errors = isset( $_REQUEST['somdn_error_logs_export_errors'] ) ? $_REQUEST['somdn_error_logs_export_errors'] : '' ;

	$somdn_errors_used = array();

	if ( ! empty( $somdn_logs_export_errors ) && is_array( $somdn_logs_export_errors ) ) :

		$allowed_tags = somdn_get_allowed_html_tags();

		foreach ( $somdn_logs_export_errors as $somdn_error ) :
			if ( ! empty( $somdn_error ) && is_array( $somdn_error ) ) :
				foreach ( $somdn_error as $error ) :

					$cleaned_error = wpautop( wp_kses( $error, $allowed_tags ) );

					if ( ! in_array( $cleaned_error, $somdn_errors_used ) ) :

						array_push( $somdn_errors_used, $cleaned_error ); ?>

						<div class="somdn-setting-warning-wrap somdn-setting-warning-alert">
							<?php echo $cleaned_error; ?>
						</div>

					<?php endif;

				endforeach;
			endif;
		endforeach;

	endif;

	$error_content = ob_get_clean();
	return $error_content;

}

add_action( 'init', 'somdn_logs_export_init', 10 );
function somdn_logs_export_init() {

	$_REQUEST['somdn_error_logs_export_errors'] = array();

	if ( ! isset( $_POST['somdn-error-logs-export-nonce'] ) )
		return;

	$export = isset( $_POST['somdn-error-logs-export'] ) ? true : false ;
	$delete = isset( $_POST['somdn-error-logs-delete'] ) ? true : false ;

	if ( ! $export && ! $delete )
		return;

	$nonce_key = sanitize_key( $_POST['somdn-error-logs-export-nonce'] );
	if ( ! wp_verify_nonce( $nonce_key, 'somdn-error-logs-export-nonce' ) ) {
		return;
	}

	//echo '<pre>';
	//print_r( $_REQUEST );
	//echo '</pre>';
	//exit;

	// We got this far, let's check if logs exist

	$upload_dir = wp_upload_dir();
	$parent = $upload_dir['basedir'] . '/free-downloads-files';
	$log_filename = $parent . '/free_downloads_log.txt';

	if ( $delete == true ) {
		unlink( $log_filename );
		return true;
	}

	if ( ! file_exists( $log_filename ) ) {
		$no_data_error = __( 'No error logs found.', 'somdn-pro' );
		$errors['no_data_error'] = $no_data_error;
		array_push( $_REQUEST['somdn_error_logs_export_errors'], $errors);
		return;
	}

	$current_time = date( 'd_m_y_H_i_s' );
	$new_filename = 'free_downloads_logs_' . $current_time . '.txt';

	header( 'Content-Type: text/html' );
	header( 'Content-Disposition: attachment; filename=' . basename( $new_filename ) );
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate' );
	header( 'Pragma: public' );
	readfile( $log_filename );
	exit;

}

function somdn_support_more() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12 som-settings-guide">

				<h2>More Support</h2>
				<p><?php do_action( 'somdn_get_forum_link' ); ?></p>

				<?php do_action( 'somdn_support_more' ); ?>

			</div>

		</div>
	</div>

<?php

}