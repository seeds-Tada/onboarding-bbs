<?php
/* --------------------------------------------------
 * 必要なファイルを読み込む
 * -------------------------------------------------- */
require_once 'private/bootstrap.php';
require_once 'private/database.php';
require_once 'validation/edit_complete_validation.php';

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
?>

<!-- 描画するHTML -->
<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>削除成功</title>
</head>
<body>
    <header>
        <h1>削除成功</h1>
    </header>
    <main>
        <a href="index.php">戻る</a>
    </main>
    <footer>
        <hr>
        <div>(　・ω・)ノ</div>
    </footer>
</body>
</html>
