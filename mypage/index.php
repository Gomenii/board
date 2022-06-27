<?php
session_start();
require_once('../assets/db_board.php');
require_once('../assets/fanctions.php');

//　セッションタイムアウト判定
if (isset($_SESSION['loginName']) && time() - $_SESSION['start'] > 600) {
    $_SESSION = array();
    session_destroy();
    $display = '時間が経過したため、ログイン状態が解除されました。<a href="../menu/login.php">再ログイン</a>';
}

// ログイン判定
if (isset($_SESSION['loginName'])) {
    $loginJudge = 'ログイン中';
    $_SESSION['start'] = time();
} else {
    $loginJudge = '未ログイン';
    $display = '※マイページを見るには、<a href="../menu/login.php">ログイン</a>が必要です。';
}

// 投稿スレッド・レス情報取得
if (isset($_SESSION['loginName'])) {
    $name = $_SESSION['loginName'];

    $stmt = $dbh->prepare('SELECT * FROM threads WHERE name = :name ORDER BY id DESC');
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $resStmt = $dbh->prepare('SELECT * FROM posts WHERE name = :name ORDER BY id DESC');
    $resStmt->bindValue(':name', $name, PDO::PARAM_STR);
    $resStmt->execute();
    $resData = $resStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="../assets/board.css">
    <title>マイページ</title>
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', () => {
            const btn = document.querySelector('.menu_btn');
            const nav = document.querySelector('nav');
            btn.addEventListener('click', () => {
                nav.classList.toggle('open_menu')
                if (btn.innerHTML === 'Menu') {
                    btn.innerHTML = 'Close';
                } else {
                    btn.innerHTML = 'Menu';
                }
            });
        });
    </script>
</head>

<body>
    <div class="header">
        <h1 class="header_title"><a href="../index.php">サンプル掲示板</a></h1>
        <button class="menu_btn">Menu</button>
        <p><?= $loginJudge; ?></p>
        <nav class="menu_list">
            <ul>
                <li><a href="../index.php">トップページ</a></li>
                <li><a href="./index.php">マイページ</a></li>
                <li><a href="../menu/logout_cfm.php">ログアウト</a></li>
                <li><a href="../menu/inquiry.php">お問い合わせ</a></li>
                <li><a href="../menu/admin.php">運営者情報</a></li>
            </ul>
        </nav>
    </div>

    <div class="main">

        <div class="head">
            <h2>マイページ</h2>
        </div>

        <div class="content">
            <?php
            if (isset($display)) {
                echo $display;
            }
            ?>

            <div class="content_left">
                <h3><a href="./thread.php">作成したスレッド一覧をみる</a></h3>
                <p>あなたが作成したスレッドを確認することができます。</p>
                <p>スレッドの削除は原則できません。</p>
            </div>

            <div class="content_left content_mypage">
                <h3><a href="./res.php">投稿したレス一覧をみる</a></h3>
                <p>あなたが投稿したレスを確認することができます。</p>
                <p>削除ボタンを押すことで、レスを削除することができます。</p>
            </div>

        </div>

        <div class="bottom">
            <p><a href="../index.php">
                    <button class="btn" type="button">トップページへ</button></p>
            </a>
        </div>

    </div>

</body>

</html>