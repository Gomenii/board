<?php
session_start();
require_once('db_board.php');
require_once('fanctions.php');
// echo var_dump($_);
// echo var_dump($_POST);
// echo var_dump($_SERVER);
// echo var_dump($_SESSION);
// echo var_dump($_COOKIE);

// echo var_dump($_POST['name']);
// echo var_dump($_POST['pass']);
// echo var_dump($_SESSION['name']);
// echo var_dump($_SESSION['pass']);
// echo var_dump($_SERVER['HTTP_HOST']);
// echo var_dump($_SERVER['HTTP_REFERER']);
// echo var_dump(strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']));
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="board.css">
    <title>アカウント新規登録確認</title>
</head>

<body>
    <div class="header">
        <h1><a href="toppage.php">掲示板</a></h1>
    </div>

    <div class="main">
        <h2>アカウント新規登録完了</h2>

        <div class="account_reg">
            <h4>下記の内容でアカウントを登録しました。</h4>
            <p>ユーザー名 : <?php echo $_SESSION['name'] ?></p>
            <p>パスワード : セキュリティ保護のため非表示</p>
        </div>

    </div>

    <div class="main">
        <a href="login.php">
            <button class="login_page" type="button">ログイン画面にすすむ</button>
        </a>
    </div>

    <div class="main">
    </div>

    <div class="footer">
    </div>

</body>

</html>