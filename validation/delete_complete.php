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

    //id
	if(empty($_SESSION['id'])) {
		array_push($error_msg, "idがありません。");
	}

	return $error_msg;
}