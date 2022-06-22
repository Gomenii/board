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

// レコードをidの降順（新しい順）に並び替えてから1件のレコードを取得
$stmt = $dbh->prepare('SELECT * FROM threads ORDER BY id DESC LIMIT 1');
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
$threadId = $data['id'];

// 書き込みファイル読み込み
$files = file_get_contents('thread.php');

// スレッドidを加えてファイル名に
$fileName = 'thread' . $threadId . '.php';

// 書き込みオリジナルファイルから変更点を置き換える
$files = str_replace("okikae", $threadId, $files);

// 文字コードをUTFに変換
$files = mb_convert_encoding($files, "UTF-8", "AUTO");

// ファイル生成＆書き込み
$handle = fopen($fileName, 'w');
fwrite($handle, $files);
fclose($handle);

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sample text">
    <link rel="stylesheet" type="text/css" href="board.css">
    <title>スレッド作成</title>
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
        <p><?= $loginJudge; ?></p>
        <button class="menu_btn">Menu</button>
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
            <h2 class="head_title">スレッド作成</h2>
            <?php if (isset($display)) {
                echo $display;
            } ?>
        </div>

        <div class="content">
                <h4>スレッドを作成しました。</h4>
                <p>※ブラウザの戻るボタンなどで、前の画面に戻らないでください。</p>
                <p><a href="<?= $fileName ?>">
                        <button class="btn btn_big btn_blue" type="button">作成したスレッドへ</button>
                    </a></p>
        </div>

        <div class="bottom">
            <a href="toppage.php">
                <button class="btn" type="button">トップページへ</button>
            </a>
        </div>


    </div>

</body>

</html>