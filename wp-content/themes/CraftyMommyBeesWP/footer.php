<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 */
?>
<?php
$footerHideBlog = false;
$footerHidePost = false;
$pagePost = is_single();
$pageBlog = is_home();
$footerHideProducts = false;
$footerHideProduct = false;
$footerHideCart = false;
$pageProducts = false;
$pageProduct = false;
$pageCart = false;
if (function_exists('wc_get_product')) {
    $pageProducts = is_shop() || is_product_category();
    $pageProduct = is_product();
    $pageCart = is_cart();
} ?>
		</div><!-- #content -->
<?php if (!$pageBlog && !$pagePost && !$pageProducts && !$pageProduct && !$pageCart ||
    $pageBlog && !$footerHideBlog ||
    $pagePost && !$pageProduct && !$footerHidePost ||
    $pageProducts && !$footerHideProducts ||
    $pageProduct && !$footerHideProduct ||
    $pageCart && !$footerHideCart) { ?>
        <footer class="u-clearfix u-footer u-white u-footer" id="sec-c31e">
  <div class="u-clearfix u-sheet u-sheet-1">
    <div class="u-clearfix u-expanded-width-xs u-gutter-8 u-layout-wrap u-layout-wrap-1">
      <div class="u-gutter-0 u-layout">
        <div class="u-layout-row">
          <div class="u-align-center-lg u-align-center-xs u-align-left-md u-align-left-sm u-align-left-xl u-container-style u-layout-cell u-left-cell u-size-12 u-size-60-md u-layout-cell-1">
            <div class="u-container-layout u-valign-top-sm u-container-layout-1">
              <?php $logo = theme_get_logo(array(
            'default_src' => "/images/logomommy1.png",
            'default_url' => home_url('/'),
            'default_width' => '179'
        )); ?><a <?php if (is_customize_preview()) echo 'data-default-src="' . esc_url($logo['default_src']) . '" '; ?>href="<?php echo esc_url($logo['url']); ?>" class="u-image u-logo u-image-1 custom-logo-link" data-image-width="395" data-image-height="363">
                <img <?php if ($logo['svg']) {echo 'style="width:'.$logo['width'].'px"';} else {echo 'style="width:auto"';}?>src="<?php echo esc_url($logo['src']); ?>" class="u-logo-image u-logo-image-1" data-image-width="179">
              </a>
              <p class="u-align-left-lg u-align-left-md u-align-left-sm u-align-left-xl u-custom-font u-font-montserrat u-text u-text-1">Follow Us</p>
              <div class="u-social-icons u-spacing-18 u-social-icons-1">
                <a class="u-social-url" title="facebook" target="_blank" href=""><span class="u-icon u-icon-circle u-social-facebook u-social-icon u-text-custom-color-2 u-icon-1"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 24 24" style=""><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-6866"></use></svg><svg class="u-svg-content" viewBox="0 0 24 24" id="svg-6866"><path d="m15.997 3.985h2.191v-3.816c-.378-.052-1.678-.169-3.192-.169-3.159 0-5.323 1.987-5.323 5.639v3.361h-3.486v4.266h3.486v10.734h4.274v-10.733h3.345l.531-4.266h-3.877v-2.939c.001-1.233.333-2.077 2.051-2.077z"></path></svg></span>
                </a>
                <a class="u-social-url" title="twitter" target="_blank" href=""><span class="u-icon u-icon-circle u-social-icon u-social-twitter u-text-custom-color-2 u-icon-2"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 512 512" style=""><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-5ae3"></use></svg><svg class="u-svg-content" viewBox="0 0 512 512" x="0px" y="0px" id="svg-5ae3" style="enable-background:new 0 0 512 512;"><g><g><path d="M512,97.248c-19.04,8.352-39.328,13.888-60.48,16.576c21.76-12.992,38.368-33.408,46.176-58.016    c-20.288,12.096-42.688,20.64-66.56,25.408C411.872,60.704,384.416,48,354.464,48c-58.112,0-104.896,47.168-104.896,104.992    c0,8.32,0.704,16.32,2.432,23.936c-87.264-4.256-164.48-46.08-216.352-109.792c-9.056,15.712-14.368,33.696-14.368,53.056    c0,36.352,18.72,68.576,46.624,87.232c-16.864-0.32-33.408-5.216-47.424-12.928c0,0.32,0,0.736,0,1.152    c0,51.008,36.384,93.376,84.096,103.136c-8.544,2.336-17.856,3.456-27.52,3.456c-6.72,0-13.504-0.384-19.872-1.792    c13.6,41.568,52.192,72.128,98.08,73.12c-35.712,27.936-81.056,44.768-130.144,44.768c-8.608,0-16.864-0.384-25.12-1.44    C46.496,446.88,101.6,464,161.024,464c193.152,0,298.752-160,298.752-298.688c0-4.64-0.16-9.12-0.384-13.568    C480.224,136.96,497.728,118.496,512,97.248z"></path>
</g>
</g></svg></span>
                </a>
                <a class="u-social-url" title="instagram" target="_blank" href=""><span class="u-icon u-icon-circle u-social-icon u-social-instagram u-text-custom-color-2 u-icon-3"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 511 511.9" style=""><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-ac80"></use></svg><svg class="u-svg-content" viewBox="0 0 511 511.9" id="svg-ac80"><path d="m510.949219 150.5c-1.199219-27.199219-5.597657-45.898438-11.898438-62.101562-6.5-17.199219-16.5-32.597657-29.601562-45.398438-12.800781-13-28.300781-23.101562-45.300781-29.5-16.296876-6.300781-34.898438-10.699219-62.097657-11.898438-27.402343-1.300781-36.101562-1.601562-105.601562-1.601562s-78.199219.300781-105.5 1.5c-27.199219 1.199219-45.898438 5.601562-62.097657 11.898438-17.203124 6.5-32.601562 16.5-45.402343 29.601562-13 12.800781-23.097657 28.300781-29.5 45.300781-6.300781 16.300781-10.699219 34.898438-11.898438 62.097657-1.300781 27.402343-1.601562 36.101562-1.601562 105.601562s.300781 78.199219 1.5 105.5c1.199219 27.199219 5.601562 45.898438 11.902343 62.101562 6.5 17.199219 16.597657 32.597657 29.597657 45.398438 12.800781 13 28.300781 23.101562 45.300781 29.5 16.300781 6.300781 34.898438 10.699219 62.101562 11.898438 27.296876 1.203124 36 1.5 105.5 1.5s78.199219-.296876 105.5-1.5c27.199219-1.199219 45.898438-5.597657 62.097657-11.898438 34.402343-13.300781 61.601562-40.5 74.902343-74.898438 6.296876-16.300781 10.699219-34.902343 11.898438-62.101562 1.199219-27.300781 1.5-36 1.5-105.5s-.101562-78.199219-1.300781-105.5zm-46.097657 209c-1.101562 25-5.300781 38.5-8.800781 47.5-8.601562 22.300781-26.300781 40-48.601562 48.601562-9 3.5-22.597657 7.699219-47.5 8.796876-27 1.203124-35.097657 1.5-103.398438 1.5s-76.5-.296876-103.402343-1.5c-25-1.097657-38.5-5.296876-47.5-8.796876-11.097657-4.101562-21.199219-10.601562-29.398438-19.101562-8.5-8.300781-15-18.300781-19.101562-29.398438-3.5-9-7.699219-22.601562-8.796876-47.5-1.203124-27-1.5-35.101562-1.5-103.402343s.296876-76.5 1.5-103.398438c1.097657-25 5.296876-38.5 8.796876-47.5 4.101562-11.101562 10.601562-21.199219 19.203124-29.402343 8.296876-8.5 18.296876-15 29.398438-19.097657 9-3.5 22.601562-7.699219 47.5-8.800781 27-1.199219 35.101562-1.5 103.398438-1.5 68.402343 0 76.5.300781 103.402343 1.5 25 1.101562 38.5 5.300781 47.5 8.800781 11.097657 4.097657 21.199219 10.597657 29.398438 19.097657 8.5 8.300781 15 18.300781 19.101562 29.402343 3.5 9 7.699219 22.597657 8.800781 47.5 1.199219 27 1.5 35.097657 1.5 103.398438s-.300781 76.300781-1.5 103.300781zm0 0"></path><path d="m256.449219 124.5c-72.597657 0-131.5 58.898438-131.5 131.5s58.902343 131.5 131.5 131.5c72.601562 0 131.5-58.898438 131.5-131.5s-58.898438-131.5-131.5-131.5zm0 216.800781c-47.097657 0-85.300781-38.199219-85.300781-85.300781s38.203124-85.300781 85.300781-85.300781c47.101562 0 85.300781 38.199219 85.300781 85.300781s-38.199219 85.300781-85.300781 85.300781zm0 0"></path><path d="m423.851562 119.300781c0 16.953125-13.746093 30.699219-30.703124 30.699219-16.953126 0-30.699219-13.746094-30.699219-30.699219 0-16.957031 13.746093-30.699219 30.699219-30.699219 16.957031 0 30.703124 13.742188 30.703124 30.699219zm0 0"></path></svg></span>
                </a>
                <a class="u-social-url" title="pinterest" target="_blank" href=""><span class="u-icon u-icon-circle u-social-icon u-social-pinterest u-text-custom-color-2 u-icon-4"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 24 24" style=""><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-56c6"></use></svg><svg class="u-svg-content" viewBox="0 0 24 24" id="svg-56c6"><path d="m12.326 0c-6.579.001-10.076 4.216-10.076 8.812 0 2.131 1.191 4.79 3.098 5.633.544.245.472-.054.94-1.844.037-.149.018-.278-.102-.417-2.726-3.153-.532-9.635 5.751-9.635 9.093 0 7.394 12.582 1.582 12.582-1.498 0-2.614-1.176-2.261-2.631.428-1.733 1.266-3.596 1.266-4.845 0-3.148-4.69-2.681-4.69 1.49 0 1.289.456 2.159.456 2.159s-1.509 6.096-1.789 7.235c-.474 1.928.064 5.049.111 5.318.029.148.195.195.288.073.149-.195 1.973-2.797 2.484-4.678.186-.685.949-3.465.949-3.465.503.908 1.953 1.668 3.498 1.668 4.596 0 7.918-4.04 7.918-9.053-.016-4.806-4.129-8.402-9.423-8.402z"></path></svg></span>
                </a>
              </div>
            </div>
          </div>
          <div class="u-align-left u-container-style u-layout-cell u-size-12 u-size-60-md u-layout-cell-2">
            <div class="u-container-layout u-container-layout-2"><!--position-->
              <?php $sidebar_html = theme_sidebar(array(
            'id' => 'area_1',
            'template' => <<<WIDGET_TEMPLATE
                <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-2">{block_header}</h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-custom-font u-font-montserrat u-text u-text-3">{block_content}</div><!--/block_content-->
                  </div>
                </div>
WIDGET_TEMPLATE
        )); ?> <div data-position="Widget Area 1" class="u-position u-position-1"><!--block-->
                <?php if ($sidebar_html) { echo stylingDefaultControls($sidebar_html); } else { ?> <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-2"><!--block_header_content-->Quick Links<!--/block_header_content--></h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-custom-font u-font-montserrat u-text u-text-3"><!--block_content_content-->Home<br>Who We Are<br>Customer Order<br>Sservices<br>Apparel<br>Tutorials<!--/block_content_content-->
                    </div><!--/block_content-->
                  </div>
                </div> <?php } ?><!--/block-->
              </div><!--/position-->
            </div>
          </div>
          <div class="u-align-left u-container-style u-layout-cell u-size-12 u-size-60-md u-layout-cell-3">
            <div class="u-container-layout u-container-layout-3"><!--position-->
              <?php $sidebar_html = theme_sidebar(array(
            'id' => 'area_2',
            'template' => <<<WIDGET_TEMPLATE
                <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-4">{block_header}</h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-custom-font u-font-montserrat u-text u-text-5">{block_content}</div><!--/block_content-->
                  </div>
                </div>
WIDGET_TEMPLATE
        )); ?> <div data-position="Widget Area 2" class="u-position u-position-2"><!--block-->
                <?php if ($sidebar_html) { echo stylingDefaultControls($sidebar_html); } else { ?> <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-4"><!--block_header_content--><!--/block_header_content--></h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-custom-font u-font-montserrat u-text u-text-5"><!--block_content_content--><b> Blog<br></b>Affiliat Links<br>Donate<br>Contact<!--/block_content_content-->
                    </div><!--/block_content-->
                  </div>
                </div> <?php } ?><!--/block-->
              </div><!--/position-->
            </div>
          </div>
          <div class="u-align-left u-container-style u-layout-cell u-size-12 u-size-60-md u-layout-cell-4">
            <div class="u-container-layout u-container-layout-4"><!--position-->
              <?php $sidebar_html = theme_sidebar(array(
            'id' => 'area_3',
            'template' => <<<WIDGET_TEMPLATE
                <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-6">{block_header}</h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-custom-font u-font-montserrat u-text u-text-7">{block_content}</div><!--/block_content-->
                  </div>
                </div>
WIDGET_TEMPLATE
        )); ?> <div data-position="Widget Area 3" class="u-position u-position-3"><!--block-->
                <?php if ($sidebar_html) { echo stylingDefaultControls($sidebar_html); } else { ?> <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-6"><!--block_header_content--> Shop<!--/block_header_content--></h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-custom-font u-font-montserrat u-text u-text-7"><!--block_content_content-->Gift Cards<br>Our Blog<br><!--/block_content_content-->
                    </div><!--/block_content-->
                  </div>
                </div> <?php } ?><!--/block-->
              </div><!--/position--><!--position-->
              <?php $sidebar_html = theme_sidebar(array(
            'id' => 'area_4',
            'template' => <<<WIDGET_TEMPLATE
                <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-8">{block_header}</h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-custom-font u-font-montserrat u-text u-text-9">{block_content}</div><!--/block_content-->
                  </div>
                </div>
WIDGET_TEMPLATE
        )); ?> <div data-position="Widget Area 4" class="u-position u-position-4"><!--block-->
                <?php if ($sidebar_html) { echo stylingDefaultControls($sidebar_html); } else { ?> <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-8"><!--block_header_content--> Help<!--/block_header_content--></h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-custom-font u-font-montserrat u-text u-text-9"><!--block_content_content-->Help Center<br>Privacy Settings<!--/block_content_content-->
                    </div><!--/block_content-->
                  </div>
                </div> <?php } ?><!--/block-->
              </div><!--/position-->
            </div>
          </div>
          <div class="u-align-left u-container-style u-layout-cell u-right-cell u-size-12 u-size-60-md u-layout-cell-5">
            <div class="u-container-layout u-container-layout-5"><!--position-->
              <?php $sidebar_html = theme_sidebar(array(
            'id' => 'area_5',
            'template' => <<<WIDGET_TEMPLATE
                <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-10">{block_header}</h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-text u-text-11">{block_content}</div><!--/block_content-->
                  </div>
                </div>
WIDGET_TEMPLATE
        )); ?> <div data-position="Widget Area 5" class="u-position u-position-5"><!--block-->
                <?php if ($sidebar_html) { echo stylingDefaultControls($sidebar_html); } else { ?> <div class="u-block">
                  <div class="u-block-container u-clearfix"><!--block_header-->
                    <h5 class="u-block-header u-text u-text-10"><!--block_header_content--> Instagram<!--/block_header_content--></h5><!--/block_header--><!--block_content-->
                    <div class="u-block-content u-text u-text-11"><!--block_content_content--><!--/block_content_content--></div><!--/block_content-->
                  </div>
                </div> <?php } ?><!--/block-->
              </div><!--/position-->
            </div>
          </div>
        </div>
      </div>
    </div><span class="u-icon u-icon-circle u-text-palette-1-base u-icon-5"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 469.351 469.351" style=""><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-04dd"></use></svg><svg class="u-svg-content" viewBox="0 0 469.351 469.351" x="0px" y="0px" id="svg-04dd" style="enable-background:new 0 0 469.351 469.351;"><path style="fill:#03A9F4;" d="M356.626,85.086c-10.332-9.837-25.314-13.036-38.763-8.277c-3.803,1.271-6.573,4.568-7.168,8.533  l-2.987,20.523c-4.529,30.998-31.052,54.019-62.379,54.144h-42.667c-4.896,0.001-9.162,3.335-10.347,8.085l-32,128  c-1.426,5.716,2.052,11.505,7.768,12.931c0.843,0.21,1.709,0.317,2.578,0.317h53.333c4.896-0.001,9.162-3.335,10.347-8.085  l19.307-77.248h41.6c31.934,0.106,59.792-21.66,67.413-52.672l7.872-31.552C376.075,120.377,370.763,99.49,356.626,85.086z"></path><g><path style="fill:#283593;" d="M10.664,437.342C4.773,437.341-0.002,432.564,0,426.673c0-0.869,0.107-1.735,0.317-2.578   l10.667-42.453v-0.448l10.667-42.432c1.185-4.75,5.451-8.084,10.347-8.085h27.136c14.728-0.003,26.669,11.933,26.673,26.661   c0,2.181-0.267,4.354-0.795,6.47l-2.667,10.667c-2.967,11.875-13.637,20.205-25.877,20.203H29.672l-8.64,34.581   C19.845,434.015,15.567,437.351,10.664,437.342z M35.005,373.342h21.461c2.447-0.007,4.575-1.678,5.163-4.053l2.667-10.667   c0.731-2.841-0.981-5.737-3.822-6.467c-0.438-0.113-0.888-0.169-1.341-0.167H40.338L35.005,373.342z"></path><path style="fill:#283593;" d="M124.733,437.342h-15.189c-16.33,0.004-29.571-13.231-29.575-29.561   c-0.001-2.419,0.296-4.829,0.882-7.175l0,0l1.408-5.675c3.157-12.736,14.612-21.662,27.733-21.611h15.189   c16.33,0.028,29.545,13.289,29.517,29.619c-0.004,2.407-0.302,4.804-0.887,7.138l-1.408,5.675   C149.243,428.457,137.824,437.366,124.733,437.342z M101.565,405.79c-1.096,4.414,1.594,8.88,6.008,9.976   c0.645,0.16,1.306,0.241,1.971,0.243h15.189c3.289,0.009,6.159-2.227,6.955-5.419l1.408-5.675c1.096-4.414-1.594-8.88-6.008-9.976   c-0.645-0.16-1.306-0.241-1.971-0.243h-15.189c-3.289-0.009-6.159,2.227-6.955,5.419L101.565,405.79z"></path><path style="fill:#283593;" d="M138.664,437.342c-5.891-0.002-10.665-4.779-10.664-10.67c0-0.869,0.107-1.735,0.317-2.578   l10.667-42.667c1.426-5.72,7.218-9.202,12.939-7.776c5.72,1.426,9.202,7.218,7.776,12.939l-10.667,42.667   C147.845,434.015,143.567,437.351,138.664,437.342z"></path>
</g><g><path style="fill:#03A9F4;" d="M266.664,437.342c-5.891-0.002-10.665-4.779-10.664-10.67c0-0.869,0.107-1.735,0.317-2.578   l10.667-42.453v-0.448l10.667-42.432c1.185-4.75,5.451-8.084,10.347-8.085h27.136c14.728-0.003,26.669,11.933,26.673,26.661   c0,2.181-0.267,4.354-0.795,6.47l-2.667,10.667c-2.967,11.875-13.637,20.205-25.877,20.203h-26.795l-8.64,34.581   C275.845,434.015,271.567,437.351,266.664,437.342z M291.005,373.342h21.483c2.447-0.007,4.575-1.678,5.163-4.053l2.667-10.667   c0.73-2.841-0.981-5.737-3.822-6.467c-0.438-0.113-0.889-0.169-1.341-0.167h-18.816L291.005,373.342z"></path><path style="fill:#03A9F4;" d="M380.733,437.342h-15.189c-16.33,0.004-29.571-13.231-29.575-29.561   c-0.001-2.419,0.296-4.829,0.882-7.175l0,0l1.408-5.675c3.157-12.736,14.612-21.662,27.733-21.611h15.189   c16.33-0.004,29.571,13.231,29.575,29.561c0.001,2.419-0.296,4.829-0.882,7.175l-1.408,5.675   C405.309,428.467,393.854,437.393,380.733,437.342z M357.565,405.79c-1.096,4.414,1.594,8.88,6.008,9.976   c0.645,0.16,1.306,0.241,1.971,0.243h15.189c3.289,0.009,6.159-2.227,6.955-5.419l1.408-5.675c1.096-4.414-1.594-8.88-6.008-9.976   c-0.645-0.16-1.306-0.241-1.971-0.243h-15.189c-3.289-0.009-6.159,2.227-6.955,5.419L357.565,405.79z"></path><path style="fill:#03A9F4;" d="M394.664,437.342c-5.891-0.002-10.665-4.779-10.664-10.67c0-0.869,0.107-1.735,0.317-2.578   l10.667-42.667c1.426-5.72,7.218-9.202,12.939-7.776c5.72,1.426,9.202,7.218,7.776,12.939l0,0l-10.667,42.667   C403.845,434.015,399.567,437.351,394.664,437.342z"></path>
</g><g><path style="fill:#283593;" d="M202.664,426.676c-3.568-0.002-6.898-1.787-8.875-4.757l-21.333-32   c-3.27-4.901-1.947-11.525,2.955-14.795s11.525-1.947,14.795,2.955l21.333,32c3.275,4.897,1.961,11.521-2.935,14.797   C206.846,426.051,204.778,426.677,202.664,426.676z"></path><path style="fill:#283593;" d="M181.33,458.676c-5.891-0.002-10.665-4.78-10.663-10.671c0.001-2.493,0.875-4.907,2.471-6.823   l53.333-64c3.776-4.524,10.505-5.131,15.029-1.355c4.524,3.776,5.131,10.505,1.355,15.029l0,0l-53.333,64   C187.493,457.281,184.492,458.68,181.33,458.676z"></path>
</g><path style="fill:#03A9F4;" d="M437.33,437.342c-5.891-0.002-10.665-4.779-10.664-10.67c0-0.869,0.107-1.735,0.317-2.578  l21.333-85.333c1.426-5.72,7.218-9.202,12.939-7.776c5.72,1.426,9.202,7.218,7.776,12.939l0,0l-21.333,85.333  C446.512,434.015,442.234,437.351,437.33,437.342z"></path><path style="fill:#283593;" d="M321.405,29.129c-10.249-11.739-25.077-18.468-40.661-18.453H159.997  c-5.159,0-9.578,3.692-10.496,8.768L106.834,254.11c-1.049,5.797,2.801,11.346,8.598,12.395c0.626,0.113,1.262,0.17,1.898,0.17h64  c4.896-0.001,9.162-3.335,10.347-8.085l19.328-77.248h34.325c41.958-0.165,77.478-31.012,83.52-72.533l5.333-36.459l0,0  C336.382,56.773,331.721,41.007,321.405,29.129z"></path></svg></span><span class="u-icon u-icon-circle u-icon-6"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 291.791 291.791" style=""><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-65d8"></use></svg><svg class="u-svg-content" viewBox="0 0 291.791 291.791" x="0px" y="0px" id="svg-65d8" style="enable-background:new 0 0 291.791 291.791;"><g><path style="fill:#E2574C;" d="M182.298,145.895c0,50.366-40.801,91.176-91.149,91.176S0,196.252,0,145.895   s40.811-91.176,91.149-91.176S182.298,95.538,182.298,145.895z"></path><path style="fill:#F4B459;" d="M200.616,54.719c-20.442,0-39.261,6.811-54.469,18.181l0.073,0.009   c2.991,2.89,6.291,4.924,8.835,8.251l-18.965,0.301c-2.972,3-5.68,6.264-8.233,9.656H161.3c2.544,3.054,4.896,5.708,7.03,9.081   h-46.536c-1.705,2.936-3.282,5.954-4.659,9.09h56.493c1.477,3.127,2.799,5.489,3.921,8.799h-63.76   c-1.012,3.146-1.878,6.364-2.535,9.646h68.966c0.675,3.155,1.194,6.072,1.55,9.045h-71.884c-0.301,3-0.456,6.045-0.456,9.118   h72.859c0,3.228-0.228,6.218-0.556,9.118h-71.847c0.31,3.091,0.766,6.127,1.368,9.118h68.856c-0.711,2.954-1.532,5.926-2.562,9.008   h-63.969c0.966,3.118,2.143,6.145,3.428,9.099h56.621c-1.568,3.319-3.346,5.972-5.306,9.081h-46.691   c1.842,3.191,3.875,6.236,6.081,9.154l33.589,0.501c-2.863,3.437-6.537,5.507-9.884,8.516c0.182,0.146-5.352-0.018-16.248-0.191   c16.576,17.105,39.744,27.772,65.446,27.772c50.357,0,91.176-40.82,91.176-91.176S250.981,54.719,200.616,54.719z"></path>
</g></svg></span><span class="u-icon u-icon-circle u-icon-7"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 512 512" style=""><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-40ae"></use></svg><svg class="u-svg-content" viewBox="0 0 512 512" x="0px" y="0px" id="svg-40ae" style="enable-background:new 0 0 512 512;"><path style="fill:#F0EFEB;" d="M512,256c0,11.839-0.805,23.5-2.372,34.91c-2.299,16.969-6.28,33.405-11.755,49.152  C463.099,440.153,367.94,512,256,512C114.615,512,0,397.385,0,256c0-24.66,3.49-48.504,10-71.074  c1.275-4.441,2.675-8.829,4.18-13.166C49.016,71.764,144.123,0,256,0C397.385,0,512,114.615,512,256z"></path><g><polygon style="fill:#191B73;" points="174.381,339.932 217.583,339.932 244.596,171.732 201.409,171.732  "></polygon><path style="fill:#191B73;" d="M183.996,171.75l-67.929,168.02H70.353L37.01,215.385c-3.438-12.831-12.33-23.5-24.325-29.215   c-0.888-0.428-1.787-0.846-2.685-1.243c1.275-4.441,2.675-8.829,4.18-13.166h34.816c14.294,0.188,24.806,8.84,28.108,21.661   l14.587,71.325c-0.052-0.146-0.125-0.303-0.178-0.449l4.441,21.713l42.329-114.28v0.021L183.996,171.75L183.996,171.75z"></path><path style="fill:#191B73;" d="M485,172.251h-33.426c-10.303,0-18.129,2.967-22.591,13.531l-64.167,154.154h45.338l12.487-33.374   h50.73l6.478,33.499h18.025c5.475-15.747,9.456-32.183,11.755-49.152L485,172.251z M435.21,272.875   c0.899,0,17.408-54.471,17.408-54.471l13.155,54.471C465.774,272.875,443.747,272.875,435.21,272.875z"></path><path style="fill:#191B73;" d="M331.725,240.247c-15.098-7.455-24.352-12.488-24.352-20.112   c0.185-6.933,7.826-14.036,24.892-14.036c14.036-0.352,24.352,2.946,32.179,6.228l3.92,1.75l5.873-35.192   c-8.534-3.299-22.064-6.933-38.776-6.933c-42.664,0-72.705,22.182-72.892,53.907c-0.352,23.411,21.508,36.402,37.868,44.196   c16.712,7.995,22.402,13.177,22.402,20.28c-0.185,10.906-13.514,15.938-25.951,15.938c-17.233,0-26.49-2.591-40.543-8.668   l-5.689-2.591l-6.042,36.572c10.149,4.511,28.812,8.481,48.184,8.685c45.34,0,74.843-21.846,75.214-55.656   C368.16,266.065,356.633,251.86,331.725,240.247z"></path>
</g></svg></span><span class="u-icon u-icon-circle u-icon-8"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 512 512" style=""><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-4df1"></use></svg><svg class="u-svg-content" viewBox="0 0 512 512" x="0px" y="0px" id="svg-4df1" style="enable-background:new 0 0 512 512;"><path style="fill:#306FC5;" d="M512,402.281c0,16.716-13.55,30.267-30.265,30.267H30.265C13.55,432.549,0,418.997,0,402.281V109.717  c0-16.715,13.55-30.266,30.265-30.266h451.47c16.716,0,30.265,13.551,30.265,30.266V402.281L512,402.281z"></path><path style="opacity:0.15;fill:#202121;enable-background:new    ;" d="M21.517,402.281V109.717  c0-16.715,13.552-30.266,30.267-30.266h-21.52C13.55,79.451,0,93.001,0,109.717v292.565c0,16.716,13.55,30.267,30.265,30.267h21.52  C35.07,432.549,21.517,418.997,21.517,402.281z"></path><g><polygon style="fill:#FFFFFF;" points="74.59,220.748 89.888,220.748 82.241,201.278  "></polygon><polygon style="fill:#FFFFFF;" points="155.946,286.107 155.946,295.148 181.675,295.148 181.675,304.885 155.946,304.885    155.946,315.318 184.455,315.318 197.666,300.712 185.151,286.107  "></polygon><polygon style="fill:#FFFFFF;" points="356.898,201.278 348.553,220.748 364.548,220.748  "></polygon><polygon style="fill:#FFFFFF;" points="230.348,320.875 230.348,281.241 212.268,300.712  "></polygon><path style="fill:#FFFFFF;" d="M264.42,292.368c-0.696-4.172-3.48-6.261-7.654-6.261h-14.599v12.516h15.299   C261.637,298.624,264.42,296.539,264.42,292.368z"></path><path style="fill:#FFFFFF;" d="M313.09,297.236c1.391-0.697,2.089-2.785,2.089-4.867c0.696-2.779-0.698-4.172-2.089-4.868   c-1.387-0.696-3.476-0.696-5.559-0.696h-13.91v11.127h13.909C309.613,297.932,311.702,297.932,313.09,297.236z"></path><path style="fill:#FFFFFF;" d="M413.217,183.198v8.344l-4.169-8.344H376.37v8.344l-4.174-8.344h-44.502   c-7.648,0-13.909,1.392-19.469,4.173v-4.173h-31.289v0.696v3.477c-3.476-2.78-7.648-4.173-13.211-4.173h-111.95l-7.652,17.384   l-7.647-17.384h-25.031h-10.431v8.344l-3.477-8.344h-0.696H66.942l-13.909,32.68L37.042,251.34l-0.294,0.697h0.294h35.463h0.444   l0.252-0.697l4.174-10.428h9.039l4.172,11.125h40.326v-0.697v-7.647l3.479,8.343h20.163l3.475-8.343v7.647v0.697h15.993h79.965   h0.696v-18.08h1.394c1.389,0,1.389,0,1.389,2.087v15.297h50.065v-4.172c4.172,2.089,10.426,4.172,18.771,4.172h20.863l4.172-11.123   h9.732l4.172,11.123h40.328v-6.952v-3.476l6.261,10.428h1.387h0.698h30.595v-68.143h-31.291l0,0H413.217z M177.501,241.609h-6.955   h-4.171v-4.169v-34.076l-0.696,1.595v-0.019l-16.176,36.669h-0.512h-3.719h-6.017l-16.687-38.245v38.245h-23.64l-4.867-10.43   H70.417l-4.868,10.43H53.326l20.57-48.675h17.382l19.469,46.587v-46.587h4.171h14.251l0.328,0.697h0.024l8.773,19.094l6.3,14.306   l0.223-0.721l13.906-33.375H177.5v48.674H177.501L177.501,241.609z M225.481,203.364h-27.119v9.039h26.423v9.734h-26.423v9.738   h27.119v10.427h-38.939v-49.367h38.939V203.364L225.481,203.364z M275.076,221.294c0.018,0.016,0.041,0.027,0.063,0.042   c0.263,0.278,0.488,0.557,0.68,0.824c1.332,1.746,2.409,4.343,2.463,8.151c0.004,0.066,0.007,0.131,0.011,0.197   c0,0.038,0.007,0.071,0.007,0.11c0,0.022-0.002,0.039-0.002,0.06c0.016,0.383,0.026,0.774,0.026,1.197v9.735h-10.428v-5.565   c0-2.781,0-6.954-2.089-9.735c-0.657-0.657-1.322-1.09-2.046-1.398c-1.042-0.675-3.017-0.686-6.295-0.686h-12.52v17.384h-11.818   v-48.675h26.425c6.254,0,10.428,0,13.906,2.086c3.407,2.046,5.465,5.439,5.543,10.812c-0.161,7.4-4.911,11.46-8.326,12.829   C270.676,218.662,272.996,219.129,275.076,221.294z M298.491,241.609h-11.822v-48.675h11.822V241.609z M434.083,241.609h-15.3   l-22.25-36.855v30.595l-0.073-0.072v6.362h-11.747v-0.029h-11.822l-4.172-10.43H344.38l-4.172,11.123h-13.211   c-5.559,0-12.517-1.389-16.687-5.561c-4.172-4.172-6.256-9.735-6.256-18.773c0-6.953,1.389-13.911,6.256-19.472   c3.474-4.175,9.735-5.562,17.382-5.562h11.128v10.429h-11.128c-4.172,0-6.254,0.693-9.041,2.783   c-2.082,2.085-3.474,6.256-3.474,11.123c0,5.564,0.696,9.04,3.474,11.821c2.091,2.089,4.87,2.785,8.346,2.785h4.867l15.991-38.243   h6.957h10.428l19.472,46.587v-2.376v-15.705v-1.389v-27.116h17.382l20.161,34.07v-34.07h11.826v47.977h0.002L434.083,241.609   L434.083,241.609z"></path><path style="fill:#FFFFFF;" d="M265.161,213.207c0.203-0.217,0.387-0.463,0.543-0.745c0.63-0.997,1.352-2.793,0.963-5.244   c-0.016-0.225-0.057-0.433-0.105-0.634c-0.013-0.056-0.011-0.105-0.026-0.161l-0.007,0.001c-0.346-1.191-1.229-1.923-2.11-2.367   c-1.394-0.693-3.48-0.693-5.565-0.693h-13.909v11.127h13.909c2.085,0,4.172,0,5.565-0.697c0.209-0.106,0.395-0.25,0.574-0.413   l0.002,0.009C264.996,213.389,265.067,213.315,265.161,213.207z"></path><path style="fill:#FFFFFF;" d="M475.105,311.144c0-4.867-1.389-9.736-3.474-13.212v-31.289h-0.032v-2.089c0,0-29.145,0-33.483,0   c-4.336,0-9.598,4.171-9.598,4.171v-4.171h-31.984c-4.87,0-11.124,1.392-13.909,4.171v-4.171h-57.016v2.089v2.081   c-4.169-3.474-11.824-4.171-15.298-4.171h-37.549v2.089v2.081c-3.476-3.474-11.824-4.171-15.998-4.171H215.05l-9.737,10.431   l-9.04-10.431h-2.911h-4.737h-54.93v2.089v5.493v62.651h61.19l10.054-10.057l8.715,10.057h0.698h35.258h1.598h0.696h0.692v-6.953   v-9.039h3.479c4.863,0,11.124,0,15.991-2.089v17.382v1.394h31.291v-1.394V317.4h1.387c2.089,0,2.089,0,2.089,2.086v14.6v1.394   h94.563c6.263,0,12.517-1.394,15.993-4.175v2.781v1.394h29.902c6.254,0,12.517-0.695,16.689-3.478   c6.402-3.841,10.437-10.64,11.037-18.749c0.028-0.24,0.063-0.48,0.085-0.721l-0.041-0.039   C475.087,312.043,475.105,311.598,475.105,311.144z M256.076,306.973h-13.91v2.081v4.174v4.173v7.649h-22.855l-13.302-15.299   l-0.046,0.051l-0.65-0.748l-15.297,15.996h-44.501v-48.673h45.197l12.348,13.525l2.596,2.832l0.352-0.365l14.604-15.991h36.852   c7.152,0,15.161,1.765,18.196,9.042c0.365,1.441,0.577,3.043,0.577,4.863C276.237,304.189,266.502,306.973,256.076,306.973z    M325.609,306.276c1.389,2.081,2.085,4.867,2.085,9.041v9.732h-11.819v-6.256c0-2.786,0-7.65-2.089-9.739   c-1.387-2.081-4.172-2.081-8.341-2.081H292.93v18.077h-11.82v-49.369h26.421c5.559,0,10.426,0,13.909,2.084   c3.474,2.088,6.254,5.565,6.254,11.128c0,7.647-4.865,11.819-8.343,13.212C322.829,303.49,324.914,304.885,325.609,306.276z    M373.589,286.107h-27.122v9.04h26.424v9.737h-26.424v9.736h27.122v10.429H334.65V275.68h38.939V286.107z M402.791,325.05h-22.252   v-10.429h22.252c2.082,0,3.476,0,4.87-1.392c0.696-0.697,1.387-2.085,1.387-3.477c0-1.394-0.691-2.778-1.387-3.475   c-0.698-0.695-2.091-1.391-4.176-1.391c-11.126-0.696-24.337,0-24.337-15.296c0-6.954,4.172-14.604,16.689-14.604h22.945v11.819   h-21.554c-2.085,0-3.478,0-4.87,0.696c-1.387,0.697-1.387,2.089-1.387,3.478c0,2.087,1.387,2.783,2.778,3.473   c1.394,0.697,2.783,0.697,4.172,0.697h6.259c6.259,0,10.43,1.391,13.211,4.173c2.087,2.087,3.478,5.564,3.478,10.43   C420.869,320.179,414.611,325.05,402.791,325.05z M462.59,320.179c-2.778,2.785-7.648,4.871-14.604,4.871H425.74v-10.429h22.245   c2.087,0,3.481,0,4.87-1.392c0.693-0.697,1.391-2.085,1.391-3.477c0-1.394-0.698-2.778-1.391-3.475   c-0.696-0.695-2.085-1.391-4.172-1.391c-11.122-0.696-24.337,0-24.337-15.295c0-6.609,3.781-12.579,13.106-14.352   c1.115-0.154,2.293-0.253,3.583-0.253h22.948v11.819h-15.3h-5.561h-0.696c-2.087,0-3.476,0-4.865,0.696   c-0.7,0.697-1.396,2.089-1.396,3.478c0,2.087,0.696,2.783,2.785,3.473c1.389,0.697,2.78,0.697,4.172,0.697h0.691h5.565   c3.039,0,5.337,0.375,7.44,1.114c1.926,0.697,8.302,3.549,9.728,10.994c0.124,0.78,0.215,1.594,0.215,2.495   C466.761,313.925,465.37,317.401,462.59,320.179z"></path>
</g></svg></span>
    <p class="u-custom-font u-font-montserrat u-text u-text-12">Powered by</p>
    <p class="u-custom-font u-font-montserrat u-text u-text-13">Terms Of Use Privacy Internet-Based Ads</p>
    <p class="u-custom-font u-font-montserrat u-text u-text-14">@ 2021 Crafty Mommy Bees. Handcrafted by Blanket Marketing Group</p>
  </div>
</footer>
        
<?php } ?>
        <?php $showBackLink = get_option('np_hide_backlink') ? false : true; ?>
<?php if ($showBackLink) : $GLOBALS['theme_backlink'] = true; ?>
<section class="u-backlink u-clearfix u-grey-80">
            <a class="u-link" href="https://nicepage.com/WordPress Themes" target="_blank">
        <span>wordpress-themes</span>
            </a>
        <p class="u-text"><span>created with</span></p>
        <a class="u-link" href="https://nicepage.com/wordpress-website-builder" target="_blank"><span>WordPress Website Builder</span></a>.
    </section>
<?php endif; ?>
        
	</div><!-- .site-inner -->
</div><!-- #page -->

<?php wp_footer(); ?>
<?php back_to_top(); ?>
</body>
</html>
