<?php
function validation() {
	$error_msg = [];
	
	//id
	if(empty($_POST['id'])) {
		array_push($error_msg, "idがありません。");
	}

	return $error_msg;
}