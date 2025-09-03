<?php
/* --------------------------------------------------
 * 必要なファイルを読み込む
 * -------------------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'private/database.php';
require_once 'validation/post_complete_validation.php';

require_once 'vendor/autoload.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

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
}catch(PDOException $e) {
	echo("データベースエラーが発生しました。<br>");
	echo($e->getMessage());
}catch(Exception $e) {
	echo("エラーが発生しました<br>");
	echo($e->getMessage());
}

/* --------------------------------------------------
 * セッション内のデータを削除する
 * -------------------------------------------------- */
unset($_SESSION['name']);
unset($_SESSION['content']);
unset($_SESSION['token']);

/* --------------------------------------------------
 * Twigを利用する
 * -------------------------------------------------- */
$loader = new FilesystemLoader('./templates');
$twig = new Environment($loader);
$template = $twig->load('post_complete.html.twig');

echo($template->render([]));