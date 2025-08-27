<?php
/* ----------------------------------------
 * 必要なファイルを読み込む
 * ---------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'private/database.php';

require_once 'vendor/autoload.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/* ----------------------------------------
 * セッション開始
 * ---------------------------------------- */
session_start();

/* ----------------------------------------
 * データベース接続
 * ---------------------------------------- */
$connection = connectDB();

/* ----------------------------------------
 * データベースから投稿されている内容を取得する
 * ---------------------------------------- */
$twig_data = [];
try {
	$sql = "SELECT * FROM articles;";
	$stmt = $connection->query($sql);
	$twig_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt = null;
}catch(PDOException $e) {
	echo("error");
}catch(Exception $e) {
	echo("error");
	echo($e);
}

/* --------------------------------------------------
 * Twigを利用する
 * -------------------------------------------------- */
$loader = new FilesystemLoader('./templates');
$twig = new Environment($loader);
$template = $twig->load('index.html.twig');

echo($template->render(["data" => $twig_data]));

/* --------------------
 * Session削除
 * -------------------- */
foreach (array_keys($_SESSION ?? []) as $key) {
	unset($_SESSION[$key]);
}
