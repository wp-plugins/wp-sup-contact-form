<?php
/*
Plugin Name: WP Sup Contact Form
Plugin URI: http://dev.templatemaxs.com/2013/11/wp-sup-contact-form-wordpress-plugin.html
Description:  WP Sup Contact Form, display contact form field on the post/page easy using Shortcode. This contact form support for file attachment or file upload.
Version: 0.0.3
Author: Usupdotnet
Author URI: http://dev.templatemaxs.com/
License: GPLv2 or later
*/

require_once dirname( __FILE__ ) . '/wpscf-frame-work.php';
require_once dirname( __FILE__ ) . '/wpscf-settings.php';
 
// enqueue the scripts and style
function wpscf_plugin_scripts(){
    wp_register_style('wpscf_plugin_style', plugin_dir_url( __FILE__ ).'style.css');
    wp_enqueue_style('wpscf_plugin_style');
}

add_action('wp_enqueue_scripts','wpscf_plugin_scripts');

// also check the above link for remembering checkboxes values
$_SESSION['myForm'] = $_POST;

// add short code
add_shortcode ('wpscf_display', 'wpscf_add_shortcode');

// Display form with shortcode
function wpscf_add_shortcode() {
?>
	<?php
	// contact form
	if (isset($_POST['submitted']) && ('true' == $_POST['submitted'])) { 
		// checks if the form is submitted and then processes it
    	process_form(); 
		
	} else { 
		// else prints the form
    	call_wpscf_form(); 
	}

	
	?>

<?php
}
?>