<?php
/* --------------------------------------------------
 * 必要なファイルを読み込む
 * -------------------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'private/database.php';
require_once 'validation/edit_complete_validation.php';

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
$name = $_POST['name'] ?? "";
$content = $_POST['content'] ?? "";

/* --------------------------------------------------
 * 送られてきたトークンのバリデーション
 *
 * セッションに保存されているトークンと比較し、
 * 一致していなかった場合はトップ画面にリダイレクトする
 * 
 * 値のバリデーションも行う
 * -------------------------------------------------- */
$error_mes = validation();
if(count($error_mes) !== 0) {
    unset($_SESSION['token']);
    redirect('/index.php');
}

/* --------------------------------------------------
 * セッション内に保存したIDを取得する
 * -------------------------------------------------- */
$id = $_SESSION['id'];

/* --------------------------------------------------
 * データの更新処理
 * -------------------------------------------------- */
try {
	$connection = connectDB();
	$sql = "UPDATE articles SET name = :name, content = :content WHERE id = :id";
	$stmt = $connection->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':content', $content, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
unset($_SESSION['token']);
unset($_SESSION['id']);

/* --------------------------------------------------
 * Twigを利用する
 * -------------------------------------------------- */
$loader = new FilesystemLoader('./templates');
$twig = new Environment($loader);
$template = $twig->load('edit_complete.html.twig');

echo($template->render([]));