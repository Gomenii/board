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
    $display = '【ログイン中】';
}

if (isset($_SESSION['loginName']) && time() - $_SESSION['start'] > 15) {
    unset($_SESSION['loginName'], $_SESSION['loginPass']);
    $display = '時間が経過したため、ログイン状態が解除されました。<a href="login.php">再ログイン</a>';
}
$_SESSION['start'] = time();

if (!isset($display)) {
    $display = '※投稿したりスレッドを作成するには、<a href="account_reg.php">新規登録</a>または<a href="login.php">ログイン</a>が必要です。';
}

// スレッド作成条件　 
// タイトル：1～32文字以内。　内容：1～1000文字以内。

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
    if ($getTitle = " ") {
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
    if ($getContent = " ") {
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
                <li><a href="toppage.php">トップページ</a></li>
                <li><a href="mypage.php">マイページ</a></li>
                <li><a href="logout_cfm.php">ログアウト</a></li>
                <li><a href="inquiry.php">お問い合わせ</a></li>
                <li><a href="admin.php">運営者情報</a></li>
            </ul>
        </nav>
    </div>

    <div class="main">
        <h2>スレッド作成</h2>
        <?php echo $display; ?>

        <form action="" method="get" class="thread">
            <h4><?php foreach ($errors as $error) {
                    echo $error . '<br>' . '<br>';
                } ?></h4>
            <p>タイトル <textarea name="title" cols="40" rows="2" value="<?php if (isset($_GET['title'])) {
                                                                            htmlsc($_GET['title']);
                                                                        } ?>"></textarea></p>
            <p>　内容　 <textarea name="content" cols="40" rows="10" value="<?php if (isset($_GET['content'])) {
                                                                            htmlsc($_GET['content']);
                                                                        } ?>"></textarea></p>
            <p><input type="submit" name="cfm" value="確認画面にすすむ"></p>
        </form>

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