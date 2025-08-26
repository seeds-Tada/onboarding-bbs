<?php
/* --------------------------------------------------
 * 必要なファイルを読み込む
 * -------------------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'private/database.php';
require_once 'validation/delete_confirm_validation.php';

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

?>

<!-- 描画するHTML -->
<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>削除確認</title>
</head>
<body>
    <header>
        <h1>確認</h1>
    </header>
    <main>
        <div>下記の内容を削除しますがよろしいですか?</div>
        <table>
            <tbody>
            <tr><th>名前</th><td><?= htmlspecialchars($name); ?></td></tr>
            <tr><th>投稿内容</th><td><pre><?= htmlspecialchars($content); ?><pre></td></tr>
            </tbody>
        </table>
        <form action="delete_complete.php" method="post">
            <input type="hidden" name="token" value="<?= $token ?>">
            <button type="submit">削除</button>
        </form>
    </main>
    <footer>
        <hr>
        <div>(　・ω・)ノ⌒■</div>
    </footer>
</body>
</html>
