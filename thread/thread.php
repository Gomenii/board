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
    $display = '※投稿したりスレッドを作成するには、<a href="../account/index.php">新規登録</a>または<a href="../menu/login.php">ログイン</a>が必要です。';
}

// ログインエラー処理・リクエストエラー処理
if (!empty($_POST)) {
    if ($loginJudge == '未ログイン') {
        $errors[] = '※ログインされていないため、投稿できません。';
    }
    if (!isset($_POST["token"]) || $_POST["token"] !== $_SESSION['resCsrfToken']) {
        $_SESSION = array();
        session_destroy();
        header('Location: ../assets/request_error.php');
        exit();
    }
}

// トークン作成
$tokenByte = openssl_random_pseudo_bytes(16);
$token = bin2hex($tokenByte);
$_SESSION['resCsrfToken'] = $token;

// URLで受け渡されたスレッドidを元にスレッド情報を取得
$threadId = $_REQUEST['id'];
$stmt = $dbh->prepare('SELECT * FROM threads WHERE id = :id');
$stmt->bindValue(':id', $threadId, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// レス情報全件取得
$resStmt = $dbh->prepare('SELECT * FROM posts WHERE thread_id = :thread_id ORDER BY id DESC');
$resStmt->bindValue(':thread_id', $threadId, PDO::PARAM_INT);
$resStmt->execute();
$resData = $resStmt->fetchAll(PDO::FETCH_ASSOC);

// 最新レス情報１件取得
$resNewestStmt = $dbh->prepare('SELECT * FROM posts WHERE thread_id = :thread_id ORDER BY id DESC');
$resNewestStmt->bindValue(':thread_id', $threadId, PDO::PARAM_INT);
$resNewestStmt->execute();
$resNewest = $resNewestStmt->fetch(PDO::FETCH_ASSOC);

if (!empty($resNewest)) {
    $number = $resNewest['number'] + 1;
} else {
    $number = 1;
}

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

// エラーがない場合はレスをDBに保存し、リダイレクト
if (!isset($errors) && !empty($_POST)) {
    $threadTitle = $data['title'];
    $name = $_SESSION['loginName'];
    $content = nl2br(htmlsc($_POST['content']));

    $stmt = $dbh->prepare('INSERT INTO posts (number, thread_id, thread_title, name, content) VALUES (:number, :thread_id, :thread_title, :name, :content)');
    $stmt->bindValue(':number', $number, PDO::PARAM_INT);
    $stmt->bindValue(':thread_id', $threadId, PDO::PARAM_INT);
    $stmt->bindValue(':thread_title', $threadTitle, PDO::PARAM_STR);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, PDO::PARAM_STR);
    $stmt->execute();

    header('location:' . $_SERVER['HTTP_REFERER']);
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
    <link rel="stylesheet" type="text/css" href="../assets/board.css">
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
        <h1 class="header_title"><a href="../index.php">サンプル掲示板</a></h1>
        <button class="menu_btn">Menu</button>
        <p><?= $loginJudge; ?></p>
        <nav class="menu_list">
            <ul>
                <li><a href="../index.php">トップページ</a></li>
                <li><a href="../mypage/index.php">マイページ</a></li>
                <li><a href="../menu/logout_cfm.php">ログアウト</a></li>
                <li><a href="../menu/inquiry.php">お問い合わせ</a></li>
                <li><a href="../menu/admin.php">運営者情報</a></li>
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
                    echo '<h4>---レス投稿---</h4>';
                    $resCount = $resNewest['number'];
                    foreach ($resData as $rd) {
                        // 削除されたレスに「削除されました表示」
                        if ($resCount != $rd['number']) {
                            $count = 0;
                            for ($a = $resCount; $a > $rd['number']; $a--) {
                                echo '<p>[' . $a . '] は削除されました。</p>';
                                $count++;
                            }
                            $resCount -= $count;
                        }
                        $resCount -= 1;

                        // レス内容出力処理（自分のレスの場合は削除ボタン表示）
                        echo '[<a href="../assets/res_reply.php?id=' . $rd['id'] . '">' . $rd['number'] . '</a>] 投稿者:<b>' . $rd['name'] . '</b>　投稿日時:' . $rd['created'];
                        if (isset($_SESSION['loginName']) && $_SESSION['loginName'] === $rd['name']) {
                            echo '　<a class="content_res_delete" href="../assets/res_delete.php?id=' . $rd['id'] . '">[削除]</a>';
                        }
                        echo '<p>' . $rd['content'] . '</p>';
                    }

                    // 最古の投稿が[1]ではない場合、最古の投稿から[1]までの「削除されました表示」出力
                    if ($resCount !== 0) {
                        for ($b = $resCount; $b > 0; $b--) {
                            echo '<p>[' . $b . '] は削除されました。</p>';
                        }
                    }
                } else {
                    echo '<h4>---レス投稿は、まだありません。---</h4>';
                }
                ?>
            </div>

            <div class="content_form">
                <form action="" method="POST">
                    <input type="hidden" name="token" value="<?= $_SESSION['resCsrfToken']; ?>">
                    <p>レス投稿は1～1000文字以内でお願いします。</p>
                    <p>[ ]内の番号をクリックすることで返信ができます。</p>
                    <p><textarea name="content" cols="40" rows="6"><?php if (isset($_SESSION['resNumber'])) {
                                                                        echo '>>' . $_SESSION['resNumber'] . '　' . $_SESSION['resName'] . 'さん&#13;&#13;';
                                                                        unset($_SESSION['resNumber'], $_SESSION['resName']);
                                                                    }
                                                                    ?></textarea></p>
                    <p><input class="btn btn_blue" type="submit" name="res" value="レス投稿"></p>
                </form>
            </div>

        </div>

        <div class="bottom">
            <p><a href="./index.php">
                    <button class="btn" type="button">スレッド一覧へ</button>
                </a></p>
        </div>

    </div>

</body>

</html>