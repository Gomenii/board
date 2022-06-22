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

// ログインエラー処理・リクエストエラー処理
if (!empty($_POST)) {
    if ($loginJudge == '未ログイン') {
        $errors[] = '※ログインされていないため、投稿できません。';
    }
    if (!isset($_POST["token"]) || $_POST["token"] !== $_SESSION['resCsrfToken']) {
        $_SESSION = array();
        session_destroy();
        header('Location: request.error.php');
        exit();
    }
}

// トークン作成
$tokenByte = openssl_random_pseudo_bytes(16);
$token = bin2hex($tokenByte);
$_SESSION['resCsrfToken'] = $token;

// ファイル生成時に割り振られたスレッドidを元に、スレッド情報を取得（okikaeは置き換えられる）
$threadid = 'okikae';
$stmt = $dbh->prepare('SELECT * FROM threads WHERE id = :id');
$stmt->bindValue(':id', $threadid, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// レス情報取得
$resStmt = $dbh->prepare('SELECT * FROM posts WHERE thread_id = :thread_id ORDER BY id DESC');
$resStmt->bindValue(':thread_id', $threadid, PDO::PARAM_STR);
$resStmt->execute();
$resData = $resStmt->fetchAll(PDO::FETCH_ASSOC);



// レス投稿条件　 ※ログイン必須　内容：1～1000文字以内（全角）1～4000文字以内（半角）
// レスのエラー処理

if (!isset($errors)) {
    if (isset($_POST['content'])) {
        $contentMaximum = 4000;
        $contentLength = strlen($_POST['content']);
        if ($contentLength == 0 || $contentLength > $contentMaximum) {
            $errors[] = '※レス内容が1～1000文字ではありません。';
        }
    }
}

// エラーがない場合はレスを投稿し、リダイレクト
if (!isset($errors) && !empty($_POST)) {
    $name = $_SESSION['loginName'];
    $content = htmlsc($_POST['content']);

    $stmt = $dbh->prepare('INSERT INTO posts (thread_id, name, content) VALUES (:thread_id, :name, :content)');
    $stmt->bindValue(':thread_id', $threadid, PDO::PARAM_STR);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    $stmt->execute();

    header('location:' . $_SERVER['PHP_SELF']);
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
    <title><?= $data['title'] ?></title>
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
            <?php if (isset($display)) {
                echo $display;
            } ?>
        </div>

        <div class="content">

            <div class="content_left">
                <h2><?= $data['title'] ?></h2>
                <?php
                echo '<p>' . 'No.' . $data['id'] . '　作成者：' . $data['name'] . '　作成日時：' . $data['created'] . '</p>';
                echo '<p>' . '<h4>スレッド内容</h4>'  . $data['content'] . '</p>';
                ?>
            </div>

            <h5><?php if (isset($errors)) {
                    foreach ($errors as $error) {
                        echo  '<p>' . $error . '</p>';
                    }
                } ?></h5>

            <div class="content_left content_res">
                <?php
                if (!empty($resData)) {
                    $count = count($resData);
                    echo '<h4>---レス投稿（' . $count . '件）---</h4>';
                    foreach ($resData as $rd) {
                        echo '[' . $count . '] 投稿者:<b>' . $rd['name'] . '</b>　投稿日時:' . $rd['created'] . '<br>'
                            . '<p>' . $rd['content'] . '</p>' . '<p>' . '</p>';
                        $count--;
                    }
                } else {
                    echo '<h4>---レス投稿は、まだありません。---</h4>';
                }
                ?>
            </div>

            <div class="content_form">
                <form action="" method="POST">
                    <input type="hidden" name="token" value="<?= $_SESSION['resCsrfToken']; ?>">
                    <p>レス投稿は1～1000文字以内です。</p>
                    <p><textarea name="content" cols="40" rows="6"></textarea></p>
                    <p><input class="btn btn_small btn_blue" type="submit" name="res" value="レス投稿"></p>
                </form>
            </div>

        </div>

        <div class="bottom">
            <p><a href="toppage.php">
                    <button class="btn" type="button">トップページへ</button></p>
            </a>
        </div>

    </div>

</body>

</html>