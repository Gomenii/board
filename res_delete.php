<?php
session_start();
require_once('db_board.php');
require_once('fanctions.php');

$resId = $_REQUEST['id'];
$stmt = $dbh->prepare('DELETE FROM posts WHERE id = :id');
$stmt->bindValue(':id', $resId, PDO::PARAM_INT);
$stmt->execute();

header('location:' . $_SERVER['HTTP_REFERER']);
exit();
?>