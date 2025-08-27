<?php
/* --------------------------------------------------
 * 必要なファイルを読み込む
 * -------------------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'private/database.php';
require_once 'validation/delete_complete_validation.php';

require_once 'vendor/autoload.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/* --------------------------------------------------
 * セッション開始
 * -------------------------------------------------- */
session_start();

/* --------------------------------------------------
 * 送られてきたトークンのバリデーション
 *
 * セッションに保存されているトークンと比較し、
 * 一致していなかった場合はトップ画面にリダイレクトする
 * 
 * idのバリデーションも行う
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
 * データの削除処理
 * -------------------------------------------------- */
try {
	$connection = connectDB();
    $sql = "DELETE FROM articles WHERE id = :id";
	$stmt = $connection->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
 * Twigを利用する
 * -------------------------------------------------- */
$loader = new FilesystemLoader('./templates');
$twig = new Environment($loader);
$template = $twig->load('delete_complete.html.twig');

echo($template->render([]));