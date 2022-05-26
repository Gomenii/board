<?php
/* ドライバ呼び出しを使用して MySQL データベースに接続する */
$user = 'root';
$pass = '';

try {
    $dbh = new PDO('mysql:host=127.0.0.1;dbname=board;charset=utf8mb4', $user, $pass);
} catch (PDOException $e) {
    echo 'DB接続エラー' . $e->getMessage();
}
?>