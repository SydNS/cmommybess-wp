<?php
/**
 * Settings Header
 *
 * Branded Square One Media header
 *
 * @version	2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

	$logo = plugins_url( '/assets/images/logo-white.png', dirname(__FILE__) );
	
	?>

<div class="som-settings-nav">
	<a href="https://squareonemedia.co.uk" target="_blank" rel="nofollow">
		<img class="som-brand-img" src="<?php echo $logo ?>">
		<h1 class="som-brand-name">Square One Media</h1>
	</a>
	<a href="https://facebook.com/squareonemediauk" target="_blank"><div class="dashicons dashicons-facebook-alt"></div></a>
	<a href="https://twitter.com/Square1MediaUK" target="_blank"><div class="dashicons dashicons-twitter"></div></a>
	<a href="https://www.youtube.com/squareonemediauk" target="_blank"><div class="dashicons dashicons-video-alt3"></div></a>
	<a href="https://profiles.wordpress.org/squareonemedia/" target="_blank"><div class="dashicons dashicons-wordpress"></div></a>
	<a href="https://squareonemedia.co.uk" target="_blank"><div class="dashicons dashicons-desktop"></div></a>
</div>

<div class="som-settings-settings-spacer"></div>

<div class="somdn-debug">
	<h3>Plugin debugging</h3>
	<?php
		$schedule = wp_next_scheduled( 'somdn_delete_download_files_event' );
		echo 'Next folder cleanup at ' . gmdate("d-m-Y T H:i:s", $schedule);
	?>
</div>