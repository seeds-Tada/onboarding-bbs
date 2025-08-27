<?php
/* --------------------------------------------------
 * 必要なファイルを読み込む
 * -------------------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'validation/post_confirm_validation.php';

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
$name = $_POST['name'] ?? "";
$content =  $_POST['content'] ?? "";

$_SESSION['name'] = $_POST['name'] ?? "";
$_SESSION['content'] = $_POST['content'] ?? "";

/* --------------------------------------------------
 * 値のバリデーションを行う
 *
 * 入力された値が正しいフォーマットで送られているかを確認する
 * 今回は値が入力されているかのみを確認する
 * -------------------------------------------------- */
$error_mes = validation();
if(count($error_mes) !== 0) {
	redirect('/index.php');
}

/* --------------------------------------------------
 * 確認画面と登録画面で利用するトークンを発行する
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
$template = $twig->load('post_confirm.html.twig');

echo($template->render(["data" => $twig_data]));