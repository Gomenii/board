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
?>

<?php

// アカウント条件　 
// ユーザー名：4～16文字以内。半角 英大文字・英小文字・数字・_ 。被っていない。' '(空文字),false, NULL以外。
// パスワード：6～16文字以内。半角 英大文字・英小文字・数字・下記記号。' '(空文字)以外。
// パスワードに使用できる記号： ! " # $ % & ' ( ) * + - , . / : ; < = > ? @ [ ] ^ _  { | } ~　
// $_POSTが空（' '空文字, 0, false, NULL）ではなければ確認画面へ遷移

$postName = $_POST['name'];
$postPass = $_POST['pass'];
$postInput = !empty($postPass) && !empty($postPass);

if (empty($postName) && empty($postPass)) {
    $errors[] = '新規登録するユーザー名とパスワードを入力してください。<br>
              （ユーザー名 4～16文字以内、パスワード 6～16文字以内）';
}

if (empty($postName) && !empty($postPass)) {
    $errors[] = '※ユーザー名が入力されていません。';
}

if (!empty($postName) && empty($postPass)) {
    $errors[] = '※パスワードが入力されていません。';
}

$maximum = 16;
$nameMinimum = 4;
$passMinimum = 6;
$nameLength = strlen($postName);
$passLength = strlen($postPass);

if ($postInput) {
    if ($nameLength < $nameMinimum || $nameLength > $maximum) {
        $errors[] = '※ユーザー名が4～16文字ではありません。';
    }
    if ($passLength < $passMinimum || $passLength > $maximum) {
        $errors[] = '※パスワードが6～16文字ではありません。';
        echo $passLength;
        echo $nameLength;
    }
}




// if (isset($_POST['name']) && isset($_POST['pass'])) {
//     $_SESSION['name'] = $_POST['name'];
//     $_SESSION['pass'] = $_POST['pass'];
//     header('Location: account_reg_cfm.php');
//     exit();
// }


// // $_POSTが空（' '空文字, 0, false, NULL）ではなければ実行
//     if (!empty($_POST['name']) && !empty($_POST['pass'])) {
//         $name = $_POST['name'];
//         $pass = $_POST['pass'];
//         $hash = password_hash($pass, PASSWORD_BCRYPT);
//         $limit = 4;
//         $passLength = strlen($pass);

//         // $_POSTが空（' '空文字, 0, false, NULL）ではない & $_POST[password]が4文字以上か確認
//         if ($passLength >= $limit) {
//             $stmt = $dbh->prepare('INSERT INTO users (name, password) VALUES (:name, :password)');
//             $stmt->bindValue(':name', $name, PDO::PARAM_STR);
//             $stmt->bindValue(':password', $hash, PDO::PARAM_STR);
//             $stmt->execute();
//             header ('Location: account_reg_cfm.php');
//             exit();
//         }

//     }
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
                    echo $error;
                    echo "\n";
                } ?></h4>
            <p>ユーザー名　<input type="text" name="name" value="<?php if (isset($_POST['name'])) {
                                                                echo htmlspecialchars($_POST['name'], ENT_QUOTES);
                                                            } ?>"></p>
            <p>パスワード　<input type="text" name="pass" value="<?php if (isset($_POST['pass'])) {
                                                                echo htmlspecialchars($_POST['pass'], ENT_QUOTES);
                                                            } ?>"></p>
            <p><input type="submit" name="account_reg_cfm_btn" value="確認画面にすすむ"></p>
        </form>

        <p>※ユーザー名とパスワードは、同じものを使用しないでください。また、第三者に推測されやすいものを設定しないでください。</p>
        <p>ユーザー名に使える文字列 : 半角英大文字、半角英小文字、半角数字、半角 _（アンダーバーのみ）</p>
        <p>パスワードに使える文字列 : 半角英大文字、半角英小文字、半角数字、下記半角記号30種類</p>
        <p>! " # $ % & ' ( ) * + - , . / : ; <=> ? @ [ ] ^ _ { | } ~</p>

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