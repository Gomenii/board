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

if (isset($_SESSION['loginName'])) {
    $display = '【ログイン中】ようこそ！' . $_SESSION['loginName'] . 'さん！';
}

if (isset($_SESSION['loginName']) && time() - $_SESSION['start'] > 15) {
    unset($_SESSION['loginName'], $_SESSION['loginPass']);
    $display = '時間が経過したため、ログイン状態が解除されました。<a href="login.php">再ログイン</a>';
}
$_SESSION['start'] = time();

if (!isset($display)) {
    $display = '※投稿したりスレッドを作成するには、<a href="account_reg.php">新規登録</a>または<a href="login.php">ログイン</a>が必要です。';
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
    <title>掲示板</title>
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', () => {
            const btn = document.querySelector('.menu_btn');
            const nav = document.querySelector('nav');
            btn.addEventListener('click', () => {
                nav.classList.toggle('open-menu')
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
        <h1><a href="toppage.php">サンプル掲示板</a></h1>
        <button class="menu_btn">Menu</button>
        <nav class="menu_list">
            <ul>
                <li><a href="#">トップページ</a></li>
                <li><a href="#">マイページ</a></li>
                <li><a href="#">ログアウト</a></li>
                <li><a href="#">お問い合わせ</a></li>
                <li><a href="#">運営者情報</a></li>
            </ul>
        </nav>
    </div>

    <div class="main">
        <h2>この掲示板はポートフォリオ用となっています</h2>
        <?php echo $display; ?>
    </div>

    <div class="main">
        <a href="new_thread.php">
            <button class="new_thread_btn" type="button">スレッド作成</button>
        </a>
    </div>

    <div class="main">
        <a href="thread1.php">
            <button class="thread_btn" type="button">スレッド1</button>
        </a>
    </div>

    <div class="footer">

    </div>
</body>

</html>