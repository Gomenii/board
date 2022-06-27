<?php
session_start();
require_once('./db_board.php');
require_once('./fanctions.php');

// 返信先のレス情報取得
$resId = $_REQUEST['id'];
$stmt = $dbh->prepare('SELECT * FROM posts WHERE id = :id');
$stmt->bindValue(':id', $resId, PDO::PARAM_INT);
$stmt->execute();
$resData = $stmt->fetch(PDO::FETCH_ASSOC);

$_SESSION['resNumber'] = $resData['number'];
$_SESSION['resName'] = $resData['name'];

header('location:' . $_SERVER['HTTP_REFERER']);
exit();
?>