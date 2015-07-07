<?php
#Send the debug code to the JavaScript Console
funtion debug_to_console($data) {
	if(is_array($data) || is_object($data)) {
		echo ("<script>console.log('PHP: ".json_encode($data)."');</script>");
	} else {
		echo ("<script>console.log('PHP: ".$data."');</script>");
	}
}
?>