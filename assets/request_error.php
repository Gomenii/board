<?php
session_start();
require_once('./db_board.php');
require_once('./fanctions.php');

// ログイン判定
if (isset($_SESSION['loginName'])) {
    $loginJudge = 'ログイン中';
} else {
    $loginJudge = '未ログイン';
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="./board.css">
    <title>不正なリクエスト</title>
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
        <p><?php echo $loginJudge; ?></p>
        <nav class="menu_list">
            <ul>
                <li><a href="../index.php">トップページ</a></li>
                <li><a href="../mypage/index.php">マイページ</a></li>
                <li><a href="../menu/logout_cfm.php">ログアウト</a></li>
                <li><a href="../menu/inquiry.php">お問い合わせ</a></li>
                <li><a href="../menu/admin.php">運営者情報</a></li>
            </ul>
        </nav>
    </div>

    <div class="main">

        <div class="head">
            <h2 class="head_title">不正なリクエスト</h2>
        </div>

        <div class="content">
            <p>不正なリクエストを検知しました。</p>
            <p>お手数ですが最初からやり直してください。</p>
        </div>

        <div class="bottom">
            <a href="../index.php">
                <button class="btn" type="button">トップページへ</button>
            </a>
            <p><a href="../menu/login.php">
                    <button class="btn" type="button">ログイン画面へ</button>
                </a></p>
        </div>

    </div>
</body>

</html>