<?php
/* --------------------------------------------------
 * 必要なファイルを読み込む
 * -------------------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'private/database.php';
require_once 'validation/post_complete_validation.php';

/* --------------------------------------------------
 * セッション開始
 * -------------------------------------------------- */
session_start();

/* --------------------------------------------------
 * 送られてきた値を取得する
 * -------------------------------------------------- */
$token = $_POST['token'] ?? "";

/* --------------------------------------------------
 * 送られてきたトークンのバリデーション
 *
 * セッションに保存されているトークンと比較し、
 * 一致していなかった場合はトップ画面にリダイレクトする
 * -------------------------------------------------- */
$error_mes = validation();
if(count($error_mes) !== 0) {
	unset($_SESSION['token']);
	redirect('/index.php');
}

/* --------------------------------------------------
 * セッション内に保存した投稿内容を取得する
 * -------------------------------------------------- */
$name = $_SESSION['name'];
$content = $_SESSION['content'];

/* --------------------------------------------------
 * データベース接続
 * -------------------------------------------------- */
$connection = connectDB();

/* --------------------------------------------------
 * データのインサート処理
 * -------------------------------------------------- */
try {
	$sql = "INSERT INTO articles(name, content) VALUE(:name, :content);";
	$stmt = $connection->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':content', $content, PDO::PARAM_STR);
	$stmt->execute();
	$stmt = null;
}catch(PDOException $e) {
	echo("db error. sources table.<br>");
	echo($e->getMessage());
}catch(Exception $e) {
	echo("error<br>");
	echo($e->getMessage());
}
/* --------------------------------------------------
 * セッション内のデータを削除する
 * -------------------------------------------------- */
unset($_SESSION['name']);
unset($_SESSION['content']);
unset($_SESSION['token']);
?>

<!-- 描画するHTML -->
<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>登録成功</title>
</head>
<body>
	<header>
		<h1>登録成功</h1>
	</header>
	<main>
		<a href="index.php">戻る</a>
	</main>
	<footer>
		<hr>
		<div>o(・ω・k)</div>
	</footer>
</body>
</html>
