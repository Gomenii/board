<?php
session_start();
require_once('db_board.php');
require_once('fanctions.php');

//　セッションタイムアウト判定
if (isset($_SESSION['loginName']) && time() - $_SESSION['start'] > 600) {
    $_SESSION = array();
    session_destroy();
    $display = '時間が経過したため、ログイン状態が解除されました。<a href="login.php">再ログイン</a>';
}

// ログイン判定
if (isset($_SESSION['loginName'])) {
    $loginJudge = 'ログイン中';
    $_SESSION['start'] = time();
} else {
    $loginJudge = '未ログイン';
    $display = '※投稿したりスレッドを作成するには、<a href="account_reg.php">新規登録</a>または<a href="login.php">ログイン</a>が必要です。';
}

// 最新のスレッド5件のデータを取得
$stmt = $dbh->prepare('SELECT * FROM threads ORDER BY id DESC LIMIT 5');
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 不要なセッション情報の削除
if (isset($_SESSION['threadCsrfToken'])) {
    unset($_SESSION['threadCsrfToken'], $_SESSION['title'], $_SESSION['content']);
}
if (isset($_SESSION['resCsrfToken'])) {
    unset($_SESSION['resCsrfToken']);
}
if (isset($_SESSION['accountRegCsrfToken'])) {
    unset($_SESSION['accountRegCsrfToken'], $_SESSION['newName'], $_SESSION['newPass']);
}
if (isset($_SESSION['loginCsrfToken'])) {
    unset($_SESSION['loginCsrfToken']);
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
        <p><?= $loginJudge; ?></p>
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
            <?php
            if (isset($_SESSION['loginName'])) {
                echo '<h2>こんにちは！' . $_SESSION['loginName'] . 'さん！</h2>';
            } else {
                echo '<h2>この掲示板はポートフォリオ用となっています</h2>';
            }
            if (isset($display)) {
                echo $display;
            }
            if (isset($_SESSION['loginName'])) {
                echo '<p><a href="new_thread.php"><button class="btn btn_blue" type="button">スレッド作成</button></a></p>';
            }
            ?>
        </div>

        <div class="content">
            <div class="content_thread_list">
                <h4>～最新のスレッド5件表示中～</h4>
                <?php
                foreach ($data as $a) {
                    echo '<p>タイトル：' . '<a href="thread.php?id=' . $a['id'] . '">' . mb_strimwidth($a['title'], 0, 64, '...', 'UTF-8')  . '</a></p>';
                    echo '作成者：' . $a['name'] . '　作成日時：' . $a['created'];
                }
                ?>
            </div>
        </div>

        <div class="bottom">
            <p><a href="thread_list.php"><button class="btn btn_blue" type="button">もっとみる</button></a></p>
        </div>

    </div>

</body>

</html>