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
    $display = '※投稿したりスレッドを作成するには、<a href="../account/index.php">新規登録</a>または<a href="../menu/login.php">ログイン</a>が必要です。';
}

// ログインエラー処理・リクエストエラー処理
if (!empty($_POST)) {
    if ($loginJudge == '未ログイン') {
        $error = 'ログインされていません。';
    }
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['threadCsrfToken']) {
        $_SESSION = array();
        session_destroy();
        header('Location: ../assets/request_error.php');
        exit();
    }
}
if (!isset($_SESSION['threadCsrfToken'])) {
    $_SESSION = array();
    session_destroy();
    header('Location: ../assets/request_error.php');
    exit();
}

// スレッド作成ボタンが押されたらDBに保存し完了画面に遷移
if (!isset($error) && !empty($_POST)) {
    $name = $_SESSION['loginName'];
    $title = $_SESSION['title'];
    $content = $_SESSION['content'];

    $stmt = $dbh->prepare('INSERT INTO threads (name, title, content) VALUES (:name, :title, :content)');
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    $stmt->execute();

    unset($_SESSION['title'], $_SESSION['content'], $_SESSION['threadCsrfToken']);
    header('Location: ./cpl.php');
    exit();
}

// エラーがなければ確認文を表示
if (!isset($error)) {
    $error = '下記の内容でスレッドを作成します。<br>（作成後の変更・削除は原則できません。）';
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
    <title>スレッド作成確認</title>
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
        <p><?= $loginJudge; ?></p>
        <button class="menu_btn">Menu</button>
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
            <h2 class="head_title">スレッド作成確認</h2>
            <?php if (isset($display)) {
                echo $display;
            } ?>
        </div>

        <div class="content">
            <h4><?= $error; ?></h4>
            <form action="" method="post">
                <input type="hidden" name="token" value="<?= $_SESSION['threadCsrfToken']; ?>">
                <p>【タイトル】</p>
                <p><?= $_SESSION['title']; ?></p>
                <p><br>【内容】</p>
                <p><?= $_SESSION['content']; ?></p>
                <p><input class="btn btn_blue" type="submit" name="cfm" value="スレッドを作成"></p>
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

    </div>

</body>

</html>