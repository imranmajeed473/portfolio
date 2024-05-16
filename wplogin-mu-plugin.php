<?php
/*
Plugin Name: WPLogin mu-plugin
Version: 1.0
Description: Functionality to my WordPress site.
*/

// Disable Gutenberg
add_filter( 'use_block_editor_for_post', '__return_false' );


// wp-admin logo URL and title
add_action('login_headerurl', function () { return home_url(); });
add_action('login_headertitle', function () { return wp_title(); });

// wp-admin_logo image and style
add_action('login_enqueue_scripts', function () {
	$logo_id = get_theme_mod('custom_logo');
	$logo_url = wp_get_attachment_image_url($logo_id, 'full');
	echo "<style>#login h1 a {background-image: url('".esc_url($logo_url)."'); }</style>";
	?>
	<style type="text/css">
		#login h1 a {min-height: 130px; width: 100%; background-size: contain; background-position: center center; }
		body.login{background:#fcfcff;}
	</style>
	<?php 
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
