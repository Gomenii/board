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


// 新規アカウント条件　 
// ユーザー名：4～16文字以内。半角 英大文字・英小文字・数字・アンダーバー。　空文字,false,NULL以外。被っていない。
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
    $_SESSION['name'] = $postName;
    $_SESSION['pass'] = $postPass;
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
</head>

<body>
    <div class="header">
        <h1><a href="toppage.php">サンプル掲示板</a></h1>
    </div>

    <div class="main">
        <h2>アカウント新規登録</h2>
    </div>

    <div class="main">

        <form action="" method="POST" class="account_reg">
            <h4><?php foreach ($errors as $error) {
                    echo $error . '<br>' . '<br>';
                } ?></h4>
            <p>ユーザー名　<input type="text" name="name" value="<?php if (isset($_POST['name'])) {
                                                                htmlsc($_POST['name']);
                                                            } ?>"></p>
            <p>パスワード　<input type="text" name="pass" value="<?php if (isset($_POST['pass'])) {
                                                                htmlsc($_POST['pass']);
                                                            } ?>"></p>
            <p><input type="submit" name="account_reg_cfm_btn" value="確認画面にすすむ"></p>
        </form>

        <div class="account_reg_caution">
            <h4>【注意事項】</h4>
            <p>※登録に使用できる文字は<a href="account_reg_str.php" target="_blank" rel="noopener noreferrer">こちら</a>を参考にしてください。</p>
            <p>※ユーザー名とパスワードは、同じものを使用しないでください。</p>
            <p>※ユーザー名とパスワードは、電話番号や誕生日などの個人情報を使用しないでください。</p>
            <p>※パスワードは、第三者に推測されやすいものを使用しないでください。（passwordやabcd1234など）</p>
            <p>※パスワードは、他のウェブサイトで使用しているものを使い回さないでください。</p>
        </div>

    </div>

    <div class="main">
        <!-- 前のページが存在している & 前のページのアドレスにサイトのホスト名が含まれていれば、前のページに戻るボタンを表示する -->
        <?php $hostName = $_SERVER['HTTP_HOST'];
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $hostName) !== false) : ?>
            <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                <button class="back_btn" type="button">前の画面に戻る</button>
            </a>
        <?php endif; ?>
    </div>

</body>

</html>