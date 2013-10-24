<?php
//start session
session_start();

/**
 * Contact form Plugin for Wordpress with file attachment
 * Release Date: 23/10/2013
 * Author: Usupdotnet <http://usup.net/>
 * 
 */

// prints form
function call_wpscf_form(){
?>
<div class="wpscf_wrap">
<p style="border-bottom:1px solid #B9B9B9;padding:0 0 7px;"><?php echo wpscf_prx('wpscf_instruction') ?></p>
<p><span class="required">*</span> Required fields</p>
	<form method="post" action="" id="uploadform" enctype="multipart/form-data">
	<p><label for="namefrom">Name <span class="required">*</span></label>
	<input name="namefrom" id="namefrom" type="text" class="field" value="<?= $_SESSION['myForm']['namefrom']; ?>" tabindex="1"/></p>
	
	<p><label for="emailfrom">Email <span class="required">*</span></label>
	<input name="emailfrom" id="emailfrom" type="text" class="field" value="<?= $_SESSION['myForm']['emailfrom']; ?>" tabindex="3"/></p>
	
	<p><label for="phone">Phone</label>
	<input name="phone" id="phone" type="text" class="field" value="<?= $_SESSION['myForm']['phone']; ?>" tabindex="4"/></p>
	
	<p><label for="subject">Subject <span class="required">*</span></label>
	<input name="subject" id="subject" type="text" class="field" value="<?= $_SESSION['myForm']['subject']; ?>" tabindex="5"/></p>
	
	<p><label for="comments">Comments <span class="required">*</span></label>
	<textarea name="comments" id="comments" rows="7" cols="10" class="field" tabindex="6"><?= $_SESSION['myForm']['comments']; ?></textarea></p>
	
	<?php if (wpscf_prx('allow_attc')=='enable'): ?>
		<p><label for="attachment">File Upload</label> 
		<input name="attachment" id="attachment" type="file" tabindex="7"></p>
		<p><small>1 file only, max file size <?php echo wpscf_prx('max_file_size'); ?>kb. Allowed file formats are .zip. rar .doc .pdf .txt</small></p>
	<?php endif; ?>
	
	<p><input type="submit" name="submit" id="submit" value="Send Email!"  tabindex="8"/></p>
	<p><input type="hidden" name="submitted"  value="true" /></p>
<br />
	</form>
	<div style="display:<?php echo wpscf_prx('wpscf_link') ?>" class="wpscf_link">Powered by : <a href="http://usup.net">WP Sup Contact Form</a></div>
<div style="clear:both"> </div>
</div>
<?php
}

// enquiry form validation

function process_form() {
	// Read POST request params into global vars
	// FILL IN YOUR EMAIL
	$to = " ".wpscf_prx('contact_email')." ";
	$subject = trim($_POST['subject']);
	$namefrom = trim($_POST['namefrom']);
	$phone = trim($_POST['phone']);
	$emailfrom = trim($_POST['emailfrom']);
	$comments = trim($_POST['comments']);
	
	// Allowed file types. add file extensions WITHOUT the dot.
	$allowtypes=array("zip", "rar", "doc", "pdf", "txt");
	
	// Require a file to be attached: false = Do not allow attachments true = allow only 1 file to be attached
	$requirefile="false";
	
	// Maximum file size for attachments in KB NOT Bytes for simplicity. MAKE SURE your php.ini can handel it,
	// post_max_size, upload_max_filesize, file_uploads, max_execution_time!
	// 2048kb = 2MB,       1024kb = 1MB,     512kb = 1/2MB etc..
	$max_file_size=" ".wpscf_prx('max_file_size')." ";
	
	$errors = array(); //Initialize error array

	//checks for a name
	if (empty($_POST['namefrom']) ) {
		$errors[]='You forgot to enter your name';
		}

	//checks for an email
	if (empty($_POST['emailfrom']) ) {
		$errors[]='You forgot to enter your email';
		} else {

		if (!eregi ('^[[:alnum:]][a-z0-9_\.\-]*@[a-z0-9\.\-]+\.[a-z]{2,4}$', stripslashes(trim($_POST['emailfrom'])))) {
			$errors[]='Please enter a valid email address';
		} // if eregi
	} // if empty email

	//checks for a subject
	if (empty($_POST['subject']) ) {
		$errors[]='You forgot to enter a subject';
		}

	//checks for a message
	if (empty($_POST['comments']) ) {
		$errors[]='You forgot to enter your message';
		}
		
 	// checks for required file
	if($requirefile=="true") {
		if($_FILES['attachment']['error']==4) {
			$errors[]='You forgot to attach a file';
		}
	}
		
	//checks attachment file
	// checks that we have a file
	if((!empty($_FILES["attachment"])) && ($_FILES['attachment']['error'] == 0)) {
			// basename -- Returns filename component of path
			$filename = basename($_FILES['attachment']['name']);
			$ext = substr($filename, strrpos($filename, '.') + 1);
			$filesize=$_FILES['attachment']['size'];
			$max_bytes=$max_file_size*1024;
			
			//Check if the file type uploaded is a valid file type. 
			if (!in_array($ext, $allowtypes)) {
				$errors[]="Invalid extension for your file: <strong>".$filename."</strong>";
				
		// check the size of each file
		} elseif($filesize > $max_bytes) {
				$errors[]= "Your file: <strong>".$filename."</strong> is to big. Max file size is <b>".$max_file_size."</b>kb.";
			}
			
	} // if !empty FILES

	if (empty($errors)) { //If everything is OK
		
		// send an email
		// Obtain file upload vars
		$fileatt      = $_FILES['attachment']['tmp_name'];
		$fileatt_type = $_FILES['attachment']['type'];
		$fileatt_name = $_FILES['attachment']['name'];
		
		// Headers
		$headers = "From: $emailfrom";
		
		// create a boundary string. It must be unique
		  $semi_rand = md5(time());
		  $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

		  // Add the headers for a file attachment
		  $headers .= "\nMIME-Version: 1.0\n" .
		              "Content-Type: multipart/mixed;\n" .
		              " boundary=\"{$mime_boundary}\"";

		  // Add a multipart boundary above the plain message
		  $message ="This is a multi-part message in MIME format.\n\n";
		  $message.="--{$mime_boundary}\n";
		  $message.="Content-Type: text/plain; charset=\"iso-8859-1\"\n";
		  $message.="Content-Transfer-Encoding: 7bit\n\n";
		  $message.="From: ".$namefrom."\n";
		  $message.="Phone: ".$phone."\n";
		  $message.="Comments: ".$comments."\n\n";
		
		if (is_uploaded_file($fileatt)) {
		  // Read the file to be attached ('rb' = read binary)
		  $file = fopen($fileatt,'rb');
		  $data = fread($file,filesize($fileatt));
		  fclose($file);

		  // Base64 encode the file data
		  $data = chunk_split(base64_encode($data));

		  // Add file attachment to the message
		  $message .= "--{$mime_boundary}\n" .
		              "Content-Type: {$fileatt_type};\n" .
		              " name=\"{$fileatt_name}\"\n" .
		              //"Content-Disposition: attachment;\n" .
		              //" filename=\"{$fileatt_name}\"\n" .
		              "Content-Transfer-Encoding: base64\n\n" .
		              $data . "\n\n" .
		              "--{$mime_boundary}--\n";
		}
		
		
		// Send the completed message
		
		$envs = array("HTTP_USER_AGENT", "REMOTE_ADDR", "REMOTE_HOST");
		foreach ($envs as $env)
		$message .= "$env: $_SERVER[$env]\n";
		
		if(!mail($to,$subject,$message,$headers)) {
			exit("Mail could not be sent. Sorry! An error has occurred, please report this to the website administrator.\n");
		} else {
			echo '<div id="formfeedback"><h3>Thank You!</h3>'.wpscf_prx('success').'</div>';
			unset($_SESSION['myForm']);
			call_wpscf_form();
			
		} // end of if !mail
		
	} else { //report the errors
		echo '<div id="formfeedback"><h3>Error!</h3>'.wpscf_prx('error').'<br />';
		foreach ($errors as $msg) { //prints each error
				echo " - $msg<br />\n";
			} // end of foreach
		echo 'Please try again</div>';
		call_wpscf_form();
	} //end of if(empty($errors))

} // end of process_form()
?>