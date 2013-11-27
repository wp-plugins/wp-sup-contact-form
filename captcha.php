<?php
session_start();
	$image = imagecreatefrompng('img_ver.png');
	$text = sha1(rand(0,9999));
	$new_text = substr($text, 17, 6);
	$_SESSION['img_ver'] = $new_text;
	imagestring($image, 5, 10, 8, $new_text, 0x002CDD);
		// $gaussian = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
		//imageconvolution($image, $gaussian, 10, 0);
header('Content-Type: image/png');
imagepng($image, null, 9);
?>
