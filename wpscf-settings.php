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
		<div id="icon-wpscfoptions-general" class="icon32"><br></div>
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