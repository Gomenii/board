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
    $display = '※マイページを見るには、<a href="login.php">ログイン</a>が必要です。';
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
    <link rel="stylesheet" type="text/css" href="board.css">
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
            <h2>マイページ</h2>
        </div>

        <div class="content">
            <?php
            if (isset($display)) {
                echo $display;
            }
            ?>

            <div class="content_left content_mypage">
                <h3>---作成したスレッド一覧（新着順）---</h3>
                <p>スレッドの削除は原則できません。</p>
                <?php
                if ($loginJudge == 'ログイン中') {
                    foreach ($data as $array) {
                        echo '<p>' . 'タイトル：' . '<a href="thread.php?id=' . $array['id'] . '">' . $array['title'] . '</a>';
                        echo '<br>' . '作成日時：' . $array['created'];
                    }
                }
                ?>
            </div>

            <div class="content_left content_mypage">
                <h3>---投稿したレス一覧（新着順）---</h3>
                <p>削除ボタンを押すことで、レス内容がスレッドから削除されます。</p>
                <?php
                if ($loginJudge == 'ログイン中') {
                    foreach ($resData as $resArray) {
                        echo '<p>' . 'タイトル：' . '<a href="thread.php?id=' . $resArray['thread_id'] . '">' . $resArray['thread_title'] . '</a>';
                        echo '<br>' . 'レス内容：' . $resArray['content'];
                        echo '<br>' . '投稿日時：' . $resArray['created'] .
                            '　<a class="content_res_delete" href="res_delete.php?id=' . $resArray['id'] . '">[削除]</a>';
                    }
                }
                ?>
            </div>

            <!-- <div class="content_form">
                <form action="" method="POST">
                    <p><input class="btn btn_small btn_blue" type="submit" name="res" value="レス投稿"></p>
                </form>
            </div> -->

        </div>

        <div class="bottom">
            <p><a href="toppage.php">
                    <button class="btn" type="button">トップページへ</button></p>
            </a>
        </div>

    </div>

</body>

</html>