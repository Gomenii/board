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

if ($loginJudge == '未ログイン') {
    $display = '※投稿したりスレッドを作成するには、<a href="account_reg.php">新規登録</a>または<a href="login.php">ログイン</a>が必要です。';
}

// スレッド作成条件　 
// ログイン必須　タイトル：1～32文字以内　内容：1～1000文字以内

// ログインエラー処理
if (isset($_GET['title']) || isset($_GET['content'])) {
    if ($loginJudge == '未ログイン') {
        $errors[] = 'ログインされていません。';
    }
}

// タイトルの入力があれば変数に格納
if (isset($_GET['title'])) {
    $getTitle = $_GET['title'];
    $titleMaximum = 32;
    $titleLength = strlen($getTitle);
}

// 内容の入力があれば変数に格納
if (isset($_GET['content'])) {
    $getContent = $_GET['content'];
    $contentMaximum = 1000;
    $contentLength = strlen($getContent);
}

// タイトルのエラー処理
if (isset($getTitle)) {
    if ($getTitle = 0 || $titleLength > $titleMaximum) {
        $errors[] = '※タイトルが1～32文字ではありません。';
    }
    if ($getTitle = "") {
        $errors[] = '※タイトルが空白です。';
    }
} else {
    $errors[] = '※タイトルを入力してください（1～32文字以内）';
}

// 内容のエラー処理
if (isset($getContent)) {
    if ($getContent = 0 || $contentLength > $contentMaximum) {
        $errors[] = '※内容が1～1000文字ではありません。';
    }
    if ($getContent = "") {
        $errors[] = '※内容が空白です。';
    }
} else {
    $errors[] = '※内容を入力してください（1～1000文字以内）';
}

// エラーがない場合は確認画面に遷移
if (!isset($errors)) {
    $_SESSION['title'] = $getTitle;
    $_SESSION['content'] = $getContent;
    header('location: new_thread_cfm.php');
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
        <p><?php echo $loginJudge; ?></p>
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

        <div class="post">
            <h4 class="post_error"><?php foreach ($errors as $error) {
                    echo $error . '<br>' . '<br>';
                } ?></h4>
            <form class="post_form" action="" method="get">
                <p>　タイトル　<textarea name="title" cols="40" rows="2" value="<?php if (isset($_GET['title'])) {
                                                                                htmlsc($_GET['title']);
                                                                            } ?>"></textarea></p>
                <p>　　内容　　<textarea name="content" cols="40" rows="10" value="<?php if (isset($_GET['content'])) {
                                                                                    htmlsc($_GET['content']);
                                                                                } ?>"></textarea></p>
                <p><input class="btn" type="submit" name="cfm" value="確認画面にすすむ"></p>
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