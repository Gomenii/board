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

// スレッド作成条件　 ※ログイン必須
// タイトル：1～32文字以内（全角）　1～128文字以内（半角）　
// 内容：1～1000文字以内（全角）1～4000文字以内（半角）

// ログインエラー処理・リクエストエラー処理
if (!empty($_POST)) {
    if ($loginJudge == '未ログイン') {
        $errors[] = 'ログインされていません。';
    }
    if (!isset($_POST["token"]) || $_POST["token"] !== $_SESSION['csrfToken']) {
        $_SESSION = array();
        header('Location: request.error.php');
        exit();
    }
}

// タイトルの入力があれば変数に格納
if (isset($_POST['title']) && !isset($errors)) {
    $postTitle = $_POST['title'];
    $titleMaximum = 128;
    $titleLength = strlen($postTitle);
    $_SESSION['title'] = htmlsc($postTitle);
}

// 内容の入力があれば変数に格納
if (isset($_POST['content']) && !isset($errors)) {
    $postContent = $_POST['content'];
    $contentMaximum = 4000;
    $contentLength = strlen($postContent);
    $_SESSION['content'] = nl2br(htmlsc($postContent));
}

// タイトルのエラー処理
if (isset($postTitle)) {
    if ($titleLength == 0 || $titleLength > $titleMaximum) {
        $errors[] = '※タイトルが1～32文字ではありません。';
    }
} else {
    if (isset($_SESSION['title'])) {
        $errors[] = '※タイトルを再入力してください（1～32文字以内）';
    } else {
        $errors[] = '※タイトルを入力してください（1～32文字以内）';
    }
}

// 内容のエラー処理
if (isset($postContent)) {
    if ($contentLength == 0 || $contentLength > $contentMaximum) {
        $errors[] = '※内容が1～1000文字ではありません。';
    }
} else {
    if (isset($_SESSION['content'])) {
        $errors[] = '※内容を再入力してください（1～1000文字以内）';
    } else {
        $errors[] = '※内容を入力してください（1～1000文字以内）';
    }
}

// エラーがない場合は確認画面に遷移
if (!isset($errors)) {
    $_SESSION['title'] = htmlsc($postTitle);
    $_SESSION['content'] = nl2br(htmlsc($postContent));
    header('location: new_thread_cfm.php');
    exit();
}

$tokenByte = openssl_random_pseudo_bytes(16);
$token = bin2hex($tokenByte);
$_SESSION['csrfToken'] = $token;
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
            <!-- <h4>セキュリティ対策のため、右記5種の半角記号を使用すると文字化けが起きる仕様となっています。 < > & " '</h4> -->
            <?php if (isset($display)) {
                echo $display;
            } ?>
        </div>

        <div class="content">
            <h4><?php foreach ($errors as $error) {
                    echo $error . '<br>' . '<br>';
                } ?></h4>
            <form class="content_center" action="" method="POST">
                <input type="hidden" name="token" value="<?php echo $_SESSION['csrfToken'] ?>">
                <p>【タイトル】</p>
                <p><textarea name="title" cols="40" rows="2"><?php if (isset($_SESSION['title'])) {
                                                                    echo $_SESSION['title'];
                                                                } ?></textarea></p>
                <p><br>【内容】</p>
                <p><textarea name="content" cols="40" rows="10"><?php if (isset($_SESSION['content'])) {
                                                                    echo strip_tags($_SESSION['content']);
                                                                } ?></textarea></p>
                <p><input class="btn btn_blue" type="submit" name="cfm" value="確認画面にすすむ"></p>
            </form>
        </div>

        <div class="bottom">
            <!-- 前のページが存在している & 前のページのアドレスにサイトのホスト名が含まれていれば、前のページに戻るボタンを表示する -->
            <?php $hostName = $_SERVER['HTTP_HOST'];
            if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $hostName) !== false) : ?>
                <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                    <button class="btn" type="button">前の画面に戻る</button>
                </a>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>