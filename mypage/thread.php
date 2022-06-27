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
    header('Location: ../menu/login.php');
    exit();
}

// 自分が作成した全スレッドの件数を取得
$name = $_SESSION['loginName'];
$stmt = $dbh->prepare('SELECT * FROM threads WHERE name = :name');
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->execute();
$myThreadCount = $stmt->rowCount();

// 自分が作成した全スレッドの合計ページ数を計算（1ページ10件）
$pageCount = ceil($myThreadCount / 10);

// 現在のページ数を取得
if (isset($_REQUEST['page'])) {
    $currentPage = $_REQUEST['page'];
} else {
    $currentPage = 1;
}

// 現在のページ数によってDBから取得する開始行と行数を計算し、変数に格納
$startLine = ($currentPage - 1) * 10;
$getLine = 10;

// 現在のページ数によってDBからスレッド情報を取得
$stmt = $dbh->prepare('SELECT * FROM threads WHERE name = :name ORDER BY id DESC LIMIT ' . $startLine . ',' . $getLine);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// スレッド情報の有無によって画面表示
if (empty($data)) {
    $message = '作成したスレッドはまだありません。';
} else {
    $message = 'スレッドの削除は原則できません。';
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
    <title>作成したスレッド一覧（新着順）</title>
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
            <h2>作成したスレッド一覧（新着順）</h2>
        </div>

        <div class="content">
            <?php
            if (isset($display)) {
                echo $display;
            }
            ?>
            <h5><?= $message; ?></h5>

            <div class="content_thread_list">
                <?php
                foreach ($data as $a) {
                    echo '<p>' . 'タイトル：' . '<a href="../thread/thread.php?id=' . $a['id'] . '">' . $a['title'] . '</a></p>';
                    echo '作成日時：' . $a['created'];
                }
                ?>
            </div>

        </div>

        <div class="bottom">
            <?php
            if ($pageCount > 1) {
                $nextPage = $currentPage + 1;
                $backPage = $currentPage - 1;

                if ($currentPage != 1) {
                    echo '<a href="./thread.php"><button class="btn btn_small" type="button">◀最初ヘ</button></a>';
                }

                if ($currentPage == 1) {
                    echo '<a href="./thread.php?page=2"><button class="btn btn_small btn_blue" type="button">次ヘ</button></a>';
                } elseif ($currentPage == $pageCount) {
                    echo '<a href="./thread.php?page=' . $backPage . '"><button class="btn btn_small btn_blue" type="button">前ヘ</button></a>';
                } else {
                    echo '<a href="./thread.php?page=' . $backPage . '"><button class="btn btn_small btn_blue" type="button">前ヘ</button></a>
                          <a href="./thread.php?page=' . $nextPage . '"><button class="btn btn_small btn_blue" type="button">次ヘ</button></a>';
                }

                if ($currentPage != $pageCount) {
                    echo '<a href="./thread.php?page=' . $pageCount . '"><button class="btn btn_small" type="button">最後ヘ▶</button></a>';
                }

                echo '<p>ページ：' . $currentPage . ' / ' . $pageCount . '</p>';
            }
            ?>
            <p><a href="./index.php">
                    <button class="btn" type="button">マイページへ</button></p>
            </a>
        </div>

    </div>

</body>

</html>