<?php
function validation() {
	$error_msg = [];
	
	//token　時刻
	if(empty($_POST['token']) || empty($_SESSION['token'])) {
		array_push($error_msg, "トークンがありません。");
	}else {
		if($_POST['token'] !== $_SESSION['token']){
			 array_push($error_msg, "トークンが違います。");
		}
	}

	//名前
	if(empty($_POST['name'])) {
		array_push($error_msg, "名前が入力されていません。");
	} else {
		if(!is_string($_POST['name'])) {
			array_push($error_msg, "名前に文字列を入力してください。");
		}
	}

	//投稿内容
	if(empty($_POST['content'])) {
		array_push($error_msg, "投稿内容が入力されていません。");
	}else {
		if(!is_string($_POST['content'])) {
			array_push($error_msg, "投稿内容に文字列を入力してください。");
		}
	}

	return $error_msg;
}