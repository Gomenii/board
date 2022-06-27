<?php
session_start();
require_once('../assets/db_board.php');
require_once('../assets/fanctions.php');

// ログイン判定
if (isset($_SESSION['loginName'])) {
    $loginJudge = 'ログイン中';
} else {
    $loginJudge = '未ログイン';
}

// リクエストエラー処理
if (!empty($_POST)) {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['accountRegCsrfToken']) {
        $_SESSION = array();
        session_destroy();
        header('Location: ../assets/request_error.php');
        exit();
    }
}
if (!isset($_SESSION['accountRegCsrfToken'])) {
    $_SESSION = array();
    session_destroy();
    header('Location: ../assets/request_error.php');
    exit();
}

// DB登録処理を実行し、完了画面に遷移
if (!empty($_POST)) {
    $name = $_SESSION['newName'];
    $pass = password_hash($_SESSION['newPass'], PASSWORD_BCRYPT);

    $stmt = $dbh->prepare('INSERT INTO users (name, password) VALUES (:name, :password)');
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':password', $pass, PDO::PARAM_STR);
    $stmt->execute();

    $_SESSION = array();
    session_destroy();
    header('Location: ./reg_cpl.php');
    exit();
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
    <title>アカウント新規登録確認</title>
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
                <li><a href="../mypage/index.php">マイページ</a></li>
                <li><a href="../menu/logout_cfm.php">ログアウト</a></li>
                <li><a href="../menu/inquiry.php">お問い合わせ</a></li>
                <li><a href="../menu/admin.php">運営者情報</a></li>
            </ul>
        </nav>
    </div>

    <div class="main">

        <div class="head">
            <h2 class="head_title">アカウント新規登録確認</h2>
        </div>

        <div class="content">
            <form action="" method="POST">
                <h4>下記の内容でアカウントを登録します。</h4>
                <input type="hidden" name="token" value="<?= $_SESSION['accountRegCsrfToken'] ?>">
                <p>ユーザー名 : <?= $_SESSION['newName']; ?></p>
                <p>パスワード : <?= $_SESSION['newPass']; ?></p>
                <p><input class="btn btn_blue" type="submit" name="account_reg_btn" value="新規登録する"></p>
            </form>
        </div>

        <div class="bottom">
            <!-- 前のページが存在している & 前のページのアドレスにサイトのホスト名が含まれていれば、前のページに戻るボタンを表示する -->
            <?php $hostName = $_SERVER['HTTP_HOST'];
            if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $hostName) !== false) : ?>
                <a href="<?= $_SERVER['HTTP_REFERER']; ?>">
                    <button class="btn" type="button">前の画面に戻る</button>
                </a>
            <?php endif; ?>
        </div>

</body>

</html>