<?php
function wpscf_prx($key) {
	global $wpscfsettings, $shortname;
	return $wpscfsettings[$shortname . '_' . $key];
}

$pluginname = "WP Sup Contact Form";
$shortname = "wpscf";

$wpscfsettings = array();
$wpscfoptions = array (
	/** BEGIN General Form Settings **/
	array(
		"name"=>"General Form Settings",
		"id"=>$shortname."_general_settings",
		"type"=>"open",
		),
	array(
		"name"=>"Your Email",
		"id"=>$shortname."_contact_email",
		"std"=>"example@usup.net",
		"type"=>"text",
		"note"=>"To be used to received message"
		),
	array(
		"name"=>"Succes Message",
		"id"=>$shortname."_success",
		"std"=>"Your email has been sent, we will respond shortly.",
		"type"=>"text",
		"note"=>" "
		),
	array(
		"name"=>"Erorr Message",
		"id"=>$shortname."_error",
		"std"=>"The following error(s) has occurred:",
		"type"=>"text",
		"note"=>" "
		),
	array(
		"name"=>"Instruction Message",
		"id"=>$shortname."_wpscf_instruction",
		"std"=>"Please complete field bellow!",
		"type"=>"textarea",
		"note"=>"Showing up on the top of contact form"
		),
	array("type"=>"close"),
	/** END General Settings **/

	/** BEGIN Advanced Settings **/
	array(
		"name"=>"Advanced Settings",
		"id"=>$shortname."_Advanced_settings",
		"type"=>"open",
		),
	array(  
		"name"=>"Enable Recaptcha",	
		"id"=>$shortname."_allow_recaptcha",
		"std"=>"disable",
		"type"=>"select",
		"wpscfoptions"=>array('disable','enable'),
		"note"=>'Get reCaptcha Key <a href="https://www.google.com/recaptcha/admin/create"><span style="color:red">Signup Here</span></a>'),
	array(  
		"name"=>"Color Scheme",	
		"id"=>$shortname."_recaptcha_scheme",
		"std"=>"disable",
		"type"=>"select",
		"wpscfoptions"=>array('red','white','blackglass','clean'),
		"note"=>'reCaptcha Color Theme'),
	array(
		"name"=>"Publick Key",
		"id"=>$shortname."_recaptcha_public_key",
		"std"=>"",
		"type"=>"text",
		"note"=>"Recaptcha Public Key"
		),
	array(
		"name"=>"Private Key",
		"id"=>$shortname."_recaptcha_private_key",
		"std"=>"",
		"type"=>"text",
		"note"=>"Recaptcha Private Key"
		),

	array(  
		"name"=>"Attachment File",	
		"id"=>$shortname."_allow_attc",
		"std"=>"disable",
		"type"=>"select",
		"wpscfoptions"=>array('disable','enable')),
	array(
		"name"=>"Max File Size",
		"id"=>$shortname."_max_file_size",
		"std"=>"1024",
		"type"=>"text",
		"note"=>"Maximum file size in KB"
		),
	array(
		"name"=>"Display WPscf Link",
		"id"=>$shortname."_wpscf_link",
		"std"=>"yes",
		"type"=>"select",
		"wpscfoptions"=>array('yes','none'),
		"note"=>"Link to plugin developer page"
		),
	array("type"=>"close"),
	/** END Advanced Settings **/
);

function wpscf_admin_menu() {
    global $pluginname, $shortname, $wpscfoptions;
    if ( $_GET['page'] == basename(__FILE__) ) {
		if ( 'save' == $_REQUEST['action'] ) {
			foreach ($wpscfoptions as $value) if ($value['type']!='header') update_option( $value['id'], $_REQUEST[ $value['id'] ] );
			foreach ($wpscfoptions as $value) {
			if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
			header("Location: ?page=wpscf-settings.php&saved=true");
			die;
		} else if( 'reset' == $_REQUEST['action'] ) {
			foreach ($wpscfoptions as $value) delete_option( $value['id'] );
			header("Location: ?page=wpscf-settings.php&reset=true");
			die;
		}
    }
	add_options_page($pluginname." Settings", "WP Sup contact Form", 'edit_plugins', basename(__FILE__), 'wpscf_admin');
}

function wpscf_admin() {

    global $pluginname, $shortname, $wpscfoptions;

    if ($_REQUEST['saved']) echo '<div id="message" class="updated fade"><p><strong>'.$pluginname.' settings saved.</strong></p></div>';
    if ($_REQUEST['reset']) echo '<div id="message" class="updated fade"><p><strong>'.$pluginname.' settings reset.</strong></p></div>';
    ?>
	<?php echo '<link rel="stylesheet" type="text/css" href="'. plugins_url( 'style.css' , __FILE__ ) . '" />'; ?>
	
	<form method="post">
	<!-- BEGIN wrapper -->
	<div id="to_wrapper"> 
	<div class="wpscf-admin-head">
		<div id="icon-options-general" class="icon32"><br></div>
		<h2><?php echo $pluginname; ?></h2>
	</div>
	<!-- BEGIN content -->
	<div id="wpscf-content">
	
		<?php foreach ($wpscfoptions as $value) { 
			if ($value['type']=="open") { 
				$first = true;
				?>
		<div class="postbox">
 		<h3><?php echo $value['name']; ?></h3>
	   	<div class="inside">
			<?php } elseif ($value['type'] == "text") { ?>
				<p>
					<label<?php if ($first) { $first=false; echo ' '; } ?>><?php echo $value['name']; ?>
						<br/><small>
						<?php echo $value['note']; ?>
						</small>
					</label>
					<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings($value['id']); } else { echo $value['std']; } ?>" />
				</p>

			<?php } elseif ($value['type'] == "textarea") { ?>
				<p>
					<label<?php if ($first) { $first=false; echo ' '; } ?>><?php echo $value['name']; ?>
						<br/><small>
						<?php echo $value['note']; ?>
						</small>
					</label>
					<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php if ( get_settings( $value['id'] ) != "") { echo htmlentities(stripslashes(get_settings( $value['id'] ))); } else { echo stripslashes($value['std']); } ?></textarea>
				</p>
			<?php } elseif ($value['type'] == "select") { ?>
				<p>
					<label<?php if ($first) { $first=false; echo ' '; } ?>><?php echo $value['name']; ?>
						<br/><small>
						<?php echo $value['note']; ?>
						</small>
					</label>
					<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
					<?php foreach ($value['wpscfoptions'] as $option) { ?>
						<option value="<?php echo $option; ?>" <?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
					<?php } ?>
					</select>
				</p>
			<?php 
			} elseif ($value['type']=='close') { ?>
	<div style="clear:both"></div>	
 	</div> <!-- End inside -->
	</div> <!-- End postbox -->				 
			<?php }
		}
		?>
	</div>
	<!-- END content -->
		<!-- Begin Sidebar -->
	<div class="wpscf-sidebar">

		<div class="wpscf-widget">
		<h2>How to</h2>
		<p>
			WP sup contact form has been setup to diplaying a contact form to the post or page with shortcode.<br/>
		
			<br/>
			<b>Displaying Contact Form in the post/page</b><br/>
			Place shortcode below to the post/page :<br/>
			<span style="color:red;font-weight:bold">[wpscf_display]</span>
		</p>
		</div>

		<div class="wpscf-widget">
		<h2>About</h2>
		<p>
			WPSCF: WP sup contact form created by Usupdotnet / <a href="http://dev.templatemaxs.com">Dev Templatemaxs</a><br/><br/>
			If you like this plugin and find it useful, help keep this plugin free and actively developed by clicking the donate button or send me a gift from my Amazon wishlist. Also, don't forget to follow me on Twitter.
			<br/><br/>
			<b>Please Donate to help us continue this Plugin.</b><br/><br/>	
			<center>
			<a href="http://dev.templatemaxs.com/p/donate.html" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG_global.gif"/></a>
			</center>
			<br/><br/>
		 	<a href="http://dev.templatemaxs.com/p/donate.html" target="_blank">Donate Page</a> | 
		 	<a href="http://www.amazon.com/gp/registry/wishlist/16HXF07JWPO9E/ref=cm_wl_rlist_go_o" target="_blank">Send Amazon Gift</a> | 
		 	<a href="http://dev.templatemaxs.com/2013/11/wp-sup-contact-form-wordpress-plugin.html" target="_blank" title="WPSCF : Contact Form Wordpress Plugin">Support Page</a>
			<br/><br/>
			My other Plugin : <a href="http://wordpress.org/plugins/sup-posts-widget/" target="_blank">Sup Posts Widget</a>
			<br/><br/>	
 
		<br/>
		Sponsored by : <a href="http://jogjatouring.com/" title="Paket Wisata Jogja" target="_blank">Paket Wisata Jogja</a> | <a href="http://indojavatours.com/" target="_blank" title="Bromo Tour">Bromo Tour</a>
		</p>
		</div>
	</div>
	<!-- END Sidebar -->
	<div id="wpscf-footer" style="clear:both">
		<p>
		<input  class="button-primary" name="save" type="submit" value="Save changes" />    
		<input type="hidden" name="action" value="save" />
		</p>
	</div>
	
	 </div>
	<!-- END wrapper -->
	
	</form>

<?php
}

add_action('admin_menu', 'wpscf_admin_menu');

foreach ($wpscfoptions as $value)
	if (get_settings($value['id'])===FALSE)
		$wpscfsettings[$value['id']]=stripslashes($value['std']); 
	else 
		$wpscfsettings[$value['id']]=stripslashes(get_settings($value['id']));
	
?>