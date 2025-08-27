<?php
/* --------------------------------------------------
 * 必要なファイルを読み込む
 * -------------------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'private/database.php';
require_once 'validation/delete_confirm_validation.php';

require_once 'vendor/autoload.php';
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

/* --------------------------------------------------
 * セッション開始
 * -------------------------------------------------- */
session_start();

/* --------------------------------------------------
 * 送られてきた値を取得する
 * セッションにも保存しておく
 * -------------------------------------------------- */
$id = $_POST['id'] ?? "";
$_SESSION['id'] = $id;

/* --------------------------------------------------
 * 値のバリデーションを行う
 *
 * 1.値が入力されているか
 * 2.データベースに対象IDのレコードが存在するか
 * -------------------------------------------------- */
// 1.値が入力されているか
$error_mes = validation();
if(count($error_mes) !== 0) {
	unset($_SESSION['id']);
	redirect('/index.php');
}

// 2.データベースに対象IDのレコードが存在するか
$connection = connectDB();
$result_article = [];
try {
	$sql = "SELECT * FROM articles WHERE id = :id";
	$stmt = $connection->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$result_article = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt = null;
}catch(PDOException $e) {
	echo("db error. sources table.<br>");
	echo($e->getMessage());
}catch(Exception $e) {
	echo("error<br>");
	echo($e->getMessage());
}

if(empty($result_article)) {
	unset($_SESSION['id']);
	redirect('/index.php');
}

/* --------------------------------------------------
 * 削除する投稿のデータ
 * -------------------------------------------------- */
$name = $result_article[0]['name'];
$content = $result_article[0]['content'];

/* --------------------------------------------------
 * 確認画面と削除画面で利用するトークンを発行する
 * 今回は時刻をトークンとする
 * -------------------------------------------------- */
$token = strval(time());
$_SESSION['token'] = $token;

/* --------------------------------------------------
 * Twigを利用する
 * -------------------------------------------------- */
$twig_data = array(
	'name' => $name,
	'content' => $content,
	'token' => $token,
);
$loader = new FilesystemLoader('./templates');
$twig = new Environment($loader);
$template = $twig->load('delete_confirm.html.twig');

echo($template->render(["data" => $twig_data]));