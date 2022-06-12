<?php
session_start();
require_once('db_board.php');
require_once('fanctions.php');

// ログイン判定
if (isset($_SESSION['loginName'])) {
    $loginJudge = 'ログイン中';
} else {
    $loginJudge = '未ログイン';
}

if (isset($_SESSION['loginName']) && time() - $_SESSION['start'] > 600) {
    unset($_SESSION['loginName'], $_SESSION['loginPass']);
    $display = '時間が経過したため、ログイン状態が解除されました。<a href="login.php">再ログイン</a>';
}
$_SESSION['start'] = time();

// ログインエラー処理
if (isset($_GET['cfm'])) {
    if ($loginJudge == '未ログイン') {
        $error = '※ログインされていません。';
    } else {
        unset($_SESSION['loginName'], $_SESSION['loginPass']);
        header('location: logout_cpl.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="board.css">
    <title>ログアウト確認</title>
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
        <h1 class="header_title"><a href="toppage.php">サンプル掲示板</a></h1>
        <button class="menu_btn">Menu</button>
        <p><?php echo $loginJudge; ?></p>
        <nav class="menu_list">
            <ul>
                <li><a href="toppage.php">トップページ</a></li>
                <li><a href="mypage.php">マイページ</a></li>
                <li><a href="logout_cfm.php">ログアウト</a></li>
                <li><a href="inquiry.php">お問い合わせ</a></li>
                <li><a href="admin.php">運営者情報</a></li>
            </ul>
        </nav>
    </div>

    <div class="main">

        <div class="head">
            <h2 class="head_title">ログアウト確認</h2>
            <?php if (isset($display)) {
                echo $display;
            } ?>
        </div>

        <div class="post">
            <h4 class="post_error"><?php if (isset($error)) {
                                        echo $error;
                                    } ?></h4>
            <p>本当にログアウトしますか？</p>
            <form class="post_form" action="" method="get">
                <p><input class="btn red_btn" type="submit" name="cfm" value="ログアウトする"></p>
            </form>
        </div>

        <div class="bottom">
            <!-- 前のページが存在している & 前のページのアドレスにサイトのホスト名が含まれていれば、前のページに戻るボタンを表示する -->
            <?php $hostName = $_SERVER['HTTP_HOST'];
            if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $hostName) !== false) : ?>
                <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                    <button class="btn back_btn" type="button">前の画面に戻る</button>
                </a>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>