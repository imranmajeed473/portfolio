<?php
/*
Plugin Name: WPLogin muplugin
Version: 1.1
Description: Functionality to my WordPress site.
*/

// Disable Gutenberg
add_filter( 'use_block_editor_for_post', '__return_false' );

// wp-admin logo URL and title
add_action('login_headerurl', function () { return home_url(); });
add_action('login_headertext', function () { return wp_title('', false); });

// wp-admin_logo image and style
add_action('login_enqueue_scripts', function () {
	$logo_id = get_theme_mod('custom_logo');
	if ($logo_id) {
		$logo_url = wp_get_attachment_image_url($logo_id, 'full');
		echo "<style id='logo_url'>#login h1 a {background-image: url('".esc_url($logo_url)."'); }</style>";
	}
	?>
	<style type="text/css">
		#login h1 a {min-height: 130px; width: 100%; background-size: contain; background-position: center center; }
		body.login{background:#fcfcff;}
		#login form p.submit input#wp-submit {background: #2271b1; font-size: 20px; margin-top: 10px; padding: 0px 30px; }
		#login form p.submit {display: flex; width: 100%; text-align: center; justify-content: center; }
		.login form {padding-bottom: 15px; }
	</style>
	<?php 
	// Get the custom CSS for the admin area
	$custom_css = wp_get_custom_css();
	if (!empty($custom_css)) {echo '<style id="custom_css" type="text/css">' . wp_strip_all_tags($custom_css) . '</style>'; }
});

//wp-admin header
add_action('admin_head', function () {
	// code here
});

// Allow empty tags in TinyMCE editor //<i class="fa fa-facebook"></i>
add_filter('tiny_mce_before_init', 'override_mce_options');
function override_mce_options($initArray) {
    // Add the 'li', 'span', and 'i' tags to the list of valid elements
    $initArray['valid_elements'] = '*[*]';
    // Ensure that the 'li', 'span', and 'i' tags are not stripped when switching between Visual and Text modes
    $initArray['extended_valid_elements'] = 'li[*],span[*],i[*]';
    return $initArray;
} 

//Remove dashboard widget
add_action('wp_dashboard_setup', 'remove_dashboard_widgets');
function remove_dashboard_widgets() {
    remove_meta_box('dashboard_primary', 'dashboard', 'side'); // WordPress Events and News
    remove_meta_box('e-dashboard-overview', 'dashboard', 'normal'); // Elementor Overview
}
