<?php
function validation() {
	$error_msg = [];
	//トークン　時刻
	if(empty($_POST['token']) || empty($_SESSION['token'])) {
		array_push($error_msg, "トークンがありません。");
	}else {
		if($_POST['token'] !== $_SESSION['token']){
			 array_push($error_msg, "トークンが違います。");
		}
	}

	return $error_msg;
}