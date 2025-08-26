<?php
/* --------------------------------------------------
 * 必要なファイルを読み込む
 * -------------------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'private/database.php';
require_once 'validation/editing_validation.php';

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
 * 編集する投稿のデータ
 * -------------------------------------------------- */
$name = $result_article[0]['name'];
$content = $result_article[0]['content'];

/* --------------------------------------------------
 * 編集画面と編集完了画面で利用するトークンを発行する
 * 今回は時刻をトークンとする
 * -------------------------------------------------- */
$token = strval(time());
$_SESSION['token'] = $token;
?>

<!-- 描画するHTML -->
<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>投稿編集</title>
	<style>
		textarea {
			resize: vertical;
		}
		textarea, input[type=text] {
			border: solid 1px gray;
			box-sizing: border-box;
			padding: 4px;
			width: 100%;
		}
	</style>
</head>
<body>
	<header>
		<h1>投稿編集</h1>
	</header>
	<main>
		<form action="edit_complete.php" method="post">
			<input type="hidden" name="token" value="<?= $token ?>">
			<table>
				<tbody>
				<tr>
					<th><label for="name">名前</label></th>
					<td><input type="text" name="name" id="name" value="<?= htmlspecialchars($name); ?>" required></td>
				</tr>
				<tr>
					<th><label for="content">投稿内容</label></th>
					<td><textarea name="content" id="content" rows="4" required><?= htmlspecialchars($content); ?></textarea></td>
				</tr>
				</tbody>
			</table>
			<button type="submit">編集</button>
		</form>
	</main>
	<footer>
		<hr>
		<div>＿φ(・ω・　)</div>
	</footer>
</body>
</html>
