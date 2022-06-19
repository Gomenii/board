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

// リクエストエラー処理
if (!empty($_POST)) {
    if (!isset($_POST["token"]) || $_POST["token"] !== $_SESSION['csrfToken']) {
        $_SESSION = array();
        header('Location: request.error.php');
        exit();
    }
}

// トークン作成
$tokenByte = openssl_random_pseudo_bytes(16);
$token = bin2hex($tokenByte);
$_SESSION['csrfToken'] = $token;


// 以下、入力判定

$postMaximum = 16;

// ユーザー名の入力があれば変数に格納（未入力での送信はfalseとしたい為、!empty使用）
if (!empty($_POST['name'])) {
    $postName = $_POST['name'];
    $nameMinimum = 4;
    $nameLength = strlen($postName);
}

// パスワードの入力があれば変数に格納（未入力での送信はfalseとしたい為、!empty使用）
if (!empty($_POST['pass'])) {
    $postPass = $_POST['pass'];
    $passMinimum = 6;
    $passLength = strlen($postPass);
}

// ユーザー名のエラー処理
if (isset($postName)) {
    if ($nameLength < $nameMinimum || $nameLength > $postMaximum) {
        $errors[] = '※ユーザー名が4～16文字ではありません。';
    }
    if (preg_match('/[^a-zA-Z0-9_]/', $postName)) {
        $errors[] = '※ユーザー名に使用不可能な文字が含まれています。';
    }
    if (preg_match('/^[_]/', $postName)) {
        $errors[] = '※ユーザー名の先頭に _（アンダーバー）は使用できません。';
    }
} else {
    $errors[] = '※ユーザー名を入力してください。';
}

// パスワードのエラー処理
if (isset($postPass)) {
    if ($passLength < $passMinimum || $passLength > $postMaximum) {
        $errors[] = '※パスワードが6～16文字ではありません。';
    }
    if (preg_match('/[^!-~]/', $postPass)) {
        $errors[] = '※パスワードに使用不可能な文字が含まれています。';
    }
    if (preg_match('/^[!-\/:-@[-`{-~]/', $postPass)) {
        $errors[] = '※パスワードの先頭に記号は使用できません。';
    }
} else {
    $errors[] = '※パスワードを入力してください。';
}

// 入力されたユーザー名でDBの検索を実行。
// データ（id, name, password, created, modified）を取得し、変数に格納（key:カラム名　value:データ）
if (!isset($errors)) {
    $name = $_POST['name'];
    $stmt = $dbh->prepare('SELECT * FROM users WHERE name = :name');
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
}

// DBの検索結果があった場合は、入力されたパスワードがDBのハッシュ化されたパスワードと一致するか確認
if (isset($data)) {
    if ($data !== false) {
        $passMatch = password_verify($_POST['pass'], $data['password']);
    } else {
        $errors[] = '※ユーザー名またはパスワードが違います。';
    }
}

// パスワードが一致していれば、ログイン名をセッションに保存しトップページに遷移
if (isset($passMatch)) {
    if ($passMatch) {
        $_SESSION['start'] = time();
        $_SESSION['loginName'] = $_POST['name'];
        unset($_SESSION['csrfToken']);
        header('Location: toppage.php');
        exit();
    } else {
        $errors[] = '※ユーザー名またはパスワードが違います。';
    }
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
    <title>ログインする</title>
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
        <p><?php echo $loginJudge; ?></p>
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
            <h2 class="head_title">ログインする</h2>
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
                <p>ユーザー名　<input type="text" name="name" value="<?php if (isset($_POST['name'])) {
                                                                    echo htmlsc($_POST['name']);
                                                                } ?>"></p>
                <p>パスワード　<input type="text" name="pass" value="<?php if (isset($_POST['pass'])) {
                                                                    echo htmlsc($_POST['pass']);
                                                                } ?>"></p>
                <p><input class="btn btn_small btn_blue" type="submit" name="login" value="ログイン"></p>
            </form>
        </div>

        <p id="hazimete">はじめての方はこちら</p>
        <a href="account_reg.php">
            <button class="btn btn_small btn_blue" type="button">新規登録</button>
        </a>

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