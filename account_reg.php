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


// 新規アカウント条件　 
// ユーザー名：4～16文字以内。半角 英大文字・英小文字・数字・アンダーバー。　空文字,false,NULL以外。重複していない。
// パスワード：6～16文字以内。半角 英大文字・英小文字・数字・下記記号。　　　空文字以外。
// パスワードに使用できる記号32種類　! " # $ % & ' ( ) * + , - . / : ; < = > ? @ [ ￥ ] ^ _ ` { | } ~　

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
        $errors[] = '※ユーザー名に入力が不可能な文字が含まれています。<br>
                    （入力可能な文字は<a href="account_reg_str.php" target="_blank" rel="noopener noreferrer">こちら</a>を参考にしてください。）';
    }
    if (preg_match('/^[_]/', $postName)) {
        $errors[] = '※ユーザー名の先頭に _（アンダーバー）は使用できません。';
    }
} else {
    $errors[] = '※ユーザー名を入力してください（半角英数字とアンダーバー 4～16文字以内）';
}

// ユーザー名の重複エラー処理
if (isset($postName) && !isset($errors)) {
    $stmt = $dbh->prepare('SELECT * FROM users WHERE name = :name');
    $stmt->bindValue(':name', $postName, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data !== false) {
        $errors[] = '※このユーザー名は、すでに他のユーザーに使用されています。<br>
                    他のユーザー名をご登録ください。';
    }
}

// パスワードのエラー処理
if (isset($postPass)) {
    if ($passLength < $passMinimum || $passLength > $postMaximum) {
        $errors[] = '※パスワードが6～16文字ではありません。';
    }
    if (preg_match('/[^!-~]/', $postPass)) {
        $errors[] = '※パスワードに入力が不可能な文字が含まれています。<br>
                    （入力可能な文字は<a href="account_reg_str.php" target="_blank" rel="noopener noreferrer">こちら</a>を参考にしてください。）';
    }
    if (preg_match('/^[!-\/:-@[-`{-~]/', $postPass)) {
        $errors[] = '※パスワードの先頭に記号は使用できません。';
    }
} else {
    $errors[] = '※パスワードを入力してください（半角英数字と記号 6～16文字以内）';
}

// エラーがない場合は確認画面に遷移
if (!isset($errors)) {
    $_SESSION['newName'] = $postName;
    $_SESSION['newPass'] = $postPass;
    header('location: account_reg_cfm.php');
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
    <title>アカウント新規登録</title>
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
            <h2 class="head_title">アカウント新規登録</h2>
        </div>

        <div class="content">
            <form class="content_center" action="" method="POST">
                <h4><?php foreach ($errors as $error) {
                        echo $error . '<br>' . '<br>';
                    } ?></h4>
                <input type="hidden" name="token" value="<?php echo $_SESSION['csrfToken'] ?>">
                <p>ユーザー名　<input type="text" name="name" value="<?php if (isset($_POST['name'])) {
                                                                    echo htmlsc($_POST['name']);
                                                                } ?>"></p>
                <p>パスワード　<input type="text" name="pass" value="<?php if (isset($_POST['pass'])) {
                                                                    echo htmlsc($_POST['pass']);
                                                                } ?>"></p>
                <p><input class="btn btn_blue" type="submit" name="account_reg_btn" value="確認画面にすすむ"></p>
            </form>
            <h4>【注意事項】</h4>
            <p>※登録に使用できる文字は<a href="account_reg_str.php" target="_blank" rel="noopener noreferrer">こちら</a>を参考にしてください。</p>
            <p>※ユーザー名とパスワードは、同じものを使用しないでください。</p>
            <p>※ユーザー名とパスワードは、電話番号や誕生日などの個人情報を使用しないでください。</p>
            <p>※パスワードは、第三者に推測されやすいものを使用しないでください。（passwordやabcd1234など）</p>
            <p>※パスワードは、他のウェブサイトで使用しているものを使い回さないでください。</p>
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